<?php
namespace APP\plugins\generic\JournalCategories;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use APP\core\Application;
use PKP\core\JSONMessage;
use APP\notification\NotificationManager;
use PKP\notification\PKPNotification;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;

class JournalCategoriesPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = NULL)
    {
        // Register the plugin even when it is not enabled
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
            // Hook into the site index template
            Hook::add('TemplateManager::display', [$this, 'handleTemplateDisplay']);

            // Add template data hook
            Hook::add('TemplateManager::fetch', [$this, 'handleTemplateFetch']);
        }

        return $success;
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
    {
        return 'Journal Categories';
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return 'An OJS plugin built for Edinburgh Diamond to add journal categories on the main homepage. Categories are configurable via plugin settings.';
    }

    /**
     * Mark this as a site-wide plugin so it appears in Admin > Site Settings > Plugins
     */
    public function isSitePlugin()
    {
        return true;
    }

    /**
     * Indicate this plugin has a settings form
     */
    public function getActions($request, $verb)
    {
        $router = $request->getRouter();
        return array_merge(
            $this->getEnabled() ? [
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url(
                            request: $request,
                            op: 'manage',
                            params: [
                                'verb' => 'settings',
                                'plugin' => $this->getName(),
                                'category' => 'generic',
                            ]
                        ),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ] : [],
            parent::getActions($request, $verb)
        );
    }

    /**
     * Handle settings form display and save
     */
    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $form = new JournalCategoriesSettingsForm($this);

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        $notificationManager = new NotificationManager();
                        $notificationManager->createTrivialNotification(
                            $request->getUser()->getId(),
                            PKPNotification::NOTIFICATION_TYPE_SUCCESS,
                            ['contents' => __('common.changesSaved')]
                        );
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }

                $form->setData('pluginName', $this->getName());
                return new JSONMessage(true, $form->fetch($request));
        }

        return parent::manage($args, $request);
    }

    /**
     * Get journal categories from plugin settings (falls back to empty array)
     */
    private function getJournalCategories(): array
    {
        $categoriesJson = $this->getSetting(CONTEXT_SITE, 'categories');
        if (!$categoriesJson) {
            return [];
        }

        $categories = json_decode($categoriesJson, true);
        return is_array($categories) ? $categories : [];
    }

    /**
     * Handle template display
     */
    public function handleTemplateDisplay(string $hookName, array $params): bool {
        $templateMgr = $params[0];
        $template = &$params[1];

        // Only modify the site index page
        if ($template === 'frontend/pages/indexSite.tpl') {
            // Add stylesheet
            $request = Application::get()->getRequest();
            $baseUrl = $request->getBaseUrl();
            $cssUrl = $baseUrl . '/' . $this->getPluginPath() . '/styles/journal_category.css';

            $templateMgr->addStyleSheet(
                'journalCategories',
                $cssUrl,
                [
                    'contexts' => 'frontend',
                    'priority' => STYLE_SEQUENCE_NORMAL
                ]
            );

            // Get all journals
            $contextDao = Application::getContextDAO();
            $contexts = $contextDao->getAll(true);

            // Organize journals by categories
            $categorizedJournals = $this->organizeJournalsByCategory($contexts);

            // Add categorized journals to template
            $templateMgr->assign('categorizedJournals', $categorizedJournals);
            $templateMgr->assign('journalCategories', $this->getJournalCategories());

            // Use custom template
            $template = $this->getTemplateResource('indexSite.tpl');
        }

        return false;
    }

    /**
     * Handle template fetch to inject custom template
     */
    public function handleTemplateFetch(string $hookName, array $params): bool {
        $template = &$params[1];

        if ($template === 'frontend/pages/indexSite.tpl') {
            $template = $this->getTemplateResource('indexSite.tpl');
        }

        return false;
    }

    /**
     * Organize journals by category
     */
    private function organizeJournalsByCategory($contexts): array {
        $categories = $this->getJournalCategories();
        $categorizedJournals = [];
        $uncategorized = [];

        // Initialize categories
        foreach ($categories as $categoryName => $categoryInfo) {
            $categorizedJournals[$categoryName] = [
                'description' => $categoryInfo['description'],
                'journals' => []
            ];
        }

        // Iterate through contexts and categorize
        while ($context = $contexts->next()) {
            $journalId = $context->getId();
            $categorized = false;

            foreach ($categories as $categoryName => $categoryInfo) {
                // journal_ids stored as comma-separated string or array
                $ids = is_array($categoryInfo['journal_ids'])
                    ? $categoryInfo['journal_ids']
                    : array_map('intval', array_filter(array_map('trim', explode(',', $categoryInfo['journal_ids']))));

                if (in_array($journalId, $ids)) {
                    $categorizedJournals[$categoryName]['journals'][] = $context;
                    $categorized = true;
                    break;
                }
            }

            // Add to uncategorized if not in any category
            if (!$categorized) {
                $uncategorized[] = $context;
            }
        }

        // Add uncategorized journals if any exist
        if (!empty($uncategorized)) {
            $categorizedJournals['Other Journals'] = [
                'description' => 'Additional journals',
                'journals' => $uncategorized
            ];
        }

        return $categorizedJournals;
    }
}