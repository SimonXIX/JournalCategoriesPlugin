<?php
namespace APP\plugins\generic\JournalCategories;

use PKP\form\Form;
use PKP\form\validation\FormValidatorPost;
use PKP\form\validation\FormValidatorCSRF;

class JournalCategoriesSettingsForm extends Form
{
    private JournalCategoriesPlugin $plugin;

    public function __construct(JournalCategoriesPlugin $plugin)
    {
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->plugin = $plugin;

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Load saved settings into the form
     */
    public function initData()
    {
        $categoriesJson = $this->plugin->getSetting(CONTEXT_SITE, 'categories');
        $categories = $categoriesJson ? json_decode($categoriesJson, true) : [];

        // Convert internal structure to the editable textarea format:
        // Each line: CategoryName | journal_id1, journal_id2 | Description
        $lines = [];
        foreach ($categories as $name => $info) {
            $ids = is_array($info['journal_ids'])
                ? implode(', ', $info['journal_ids'])
                : $info['journal_ids'];
            $lines[] = $name . ' | ' . $ids . ' | ' . ($info['description'] ?? '');
        }

        $this->setData('categoriesText', implode("\n", $lines));
    }

    /**
     * Read form input
     */
    public function readInputData()
    {
        $this->readUserVars(['categoriesText']);
    }

    /**
     * Assign template variables and fetch the form
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = \PKP\template\PKPTemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * Parse and save the settings
     */
    public function execute(...$functionArgs)
    {
        $rawText = trim($this->getData('categoriesText') ?? '');
        $categories = [];

        foreach (explode("\n", $rawText) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue; // skip blank lines and comments
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 2) {
                continue; // skip malformed lines
            }

            $name = $parts[0];
            $idsRaw = $parts[1];
            $description = $parts[2] ?? '';

            // Parse IDs — accept comma-separated integers
            $ids = array_values(
                array_filter(
                    array_map('intval', array_map('trim', explode(',', $idsRaw)))
                )
            );

            if ($name !== '') {
                $categories[$name] = [
                    'journal_ids' => $ids,
                    'description' => $description,
                ];
            }
        }

        $this->plugin->updateSetting(CONTEXT_SITE, 'categories', json_encode($categories), 'string');

        return parent::execute(...$functionArgs);
    }
}