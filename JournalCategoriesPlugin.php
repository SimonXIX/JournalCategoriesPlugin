<?php
namespace APP\plugins\generic\JournalCategories;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use APP\core\Application;

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
        return 'An OJS plugin built for Edinburgh Diamond to add journal categories on the main homepage.';
    }

    /**
     * Define journal categories with journal IDs
     * You can modify these arrays to organize your journals
     */
    private function getJournalCategories() {
        // hardcoded in PHP
        $categories = [
            'Active Journals' => [
                'journal_ids' => [1, 2, 4, 11, 6, 17, 16, 5, 9, 13, 15],
                'description' => 'Ongoing journals.'
            ],
            'Inactive Journals' => [
                'journal_ids' => [12, 14, 7, 8, 18, 10],
                'description' => 'Inactive journals.'
            ]
        ];
        
        return $categories;
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
                if (in_array($journalId, $categoryInfo['journal_ids'])) {
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