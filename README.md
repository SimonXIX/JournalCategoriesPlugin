# Journal Categories Plugin for OJS 3.4

This plugin, designed and configured for Edinburgh Diamond, organises journals on the OJS homepage into predefined categories based on journal IDs. 

## Features

- Categorize journals on the site homepage
- Support for multiple categories with descriptions
- Displays uncategorized journals in an "Other Journals" section

## Requirements

- **OJS version:** 3.4.x (tested on 3.4.0-7)

## Installation

1. Copy the entire `JournalCategories` folder to your OJS installation:
   ```
   OJS-WEB-PATH/plugins/generic/JournalCategories/
   ```

2. Log in to OJS as an administrator

3. Navigate to Administration > Site Settings > Plugins

4. Find "Journal Categories" in the list

5. Click the checkbox to enable the plugin

## Configuration

Journal categories are currently configured using journal IDs hardcoded in JournalCategoriesPlugin.php. This is less than ideal.

Edit `JournalCategoriesPlugin.php` and modify the `getJournalCategories()` method:

```php
private function getJournalCategories() {
    $categories = [
        'Science & Technology' => [
            'journal_ids' => [1, 2, 3],  // Replace with your journal IDs
            'description' => 'Journals focused on scientific research and technology'
        ],
        'Social Sciences' => [
            'journal_ids' => [4, 5, 6],  // Replace with your journal IDs
            'description' => 'Journals covering social sciences and humanities'
        ],
        // Add more categories as needed
    ];
    
    return $categories;
}
```

## File Structure

```
JournalCategories/
├── JournalCategoriesPlugin.php         # Main plugin class
├── version.xml                         # Plugin version info
├── styles/
│   └── journal_category.css            # Compiled CSS (loaded by default)
├── templates/
│   └── indexSite.tpl                   # Custom homepage template
└── README.md                           # This file
```

## License

This plugin is released under the MIT License. See the `LICENSE` file for full terms.