<?php
import('lib.pkp.classes.plugins.ThemePlugin');
class EdinburghDiamondThemePlugin extends ThemePlugin {

    /**
     * Load the custom styles for our theme
     * @return null
     */
    public function init() {
        // Use the parent theme's unique plugin slug
        $this->setParent('defaultthemeplugin');

        $this->modifyStyle('stylesheet', array('addLess' => array('styles/journal_category.less')));
    }

    /**
     * Get the display name of this theme
     * @return string
     */
    function getDisplayName() {
        return 'Edinburgh Diamond Theme';
    }

    /**
     * Get the description of this plugin
     * @return string
     */
    function getDescription() {
        return 'An OJS theme built for Edinburgh Diamond specifically to add journal categories on the main homepage.';
    }
}