# Journal Categories Plugin for OJS 3.4

This plugin, designed for [Edinburgh Diamond](https://library.ed.ac.uk/research-support/edinburgh-diamond), organises journals on the OJS homepage into predefined categories based on journal IDs. 

## Features

- Categorize journals on the site homepage
- Support for multiple categories with descriptions
- Displays uncategorized journals in an "Other Journals" section

## Requirements

- **OJS version:** 3.4.x (tested on 3.4.0-6 and 3.4.0-7)

## Installation

1. Download the .zip release file.

2. Log in to OJS as an administrator

3. Navigate to Administration > Site Settings > Plugins

4. Click on **Upload A New Plugin** and upload the .zip file.

5. Find "Journal Categories" in the list

6. Click the checkbox to enable the plugin

OR 

1. Copy the entire `JournalCategories` folder to your OJS installation:
   ```
   OJS-WEB-PATH/plugins/generic/JournalCategories/
   ```

2. Log in to OJS as an administrator

3. Navigate to Administration > Site Settings > Plugins

4. Find "Journal Categories" in the list

5. Click the checkbox to enable the plugin

## Configuration

Journal categories are configured through the OJS admin interface.

1. Log in as an administrator

2. Navigate to Administration > Site Settings > Plugins

3. Find "Journal Categories" and click **Settings**

4. Enter your categories in the textarea, one per line, using this format:
```
Category Name | id1, id2, id3 | Optional description
```

For example:
```
Science & Technology | 1, 2, 3 | Journals focused on scientific research and technology.
Social Sciences | 4, 5, 6 | Journals covering social sciences and humanities.
```

Journal IDs are the numeric IDs from your OJS database. Any journals not assigned to a category will appear in an "Other Journals" section at the bottom of the page. Lines starting with `#` are ignored and can be used as comments.

## File Structure

```
JournalCategories/
├── JournalCategoriesPlugin.php         # Main plugin class
├── JournalCategoriesSettingsForm.php   # Settings form handler
├── version.xml                         # Plugin version info
├── styles/
│   └── journal_category.css            # Compiled CSS (loaded by default)
├── templates/
│   ├── indexSite.tpl                   # Custom homepage template
│   └── settingsForm.tpl                # Settings form template
└── README.md                           # This file
```

## License

This plugin is released under the MIT License. See the `LICENSE` file for full terms.
