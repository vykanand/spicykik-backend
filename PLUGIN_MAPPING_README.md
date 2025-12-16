# Plugin Field Mapping System

## Overview

This system allows you to map specific form fields to specific plugins in your ERP modules. When a field has a plugin mapping, clicking the plugin icon will directly open that plugin instead of showing the plugin selector.

## Features

- **Database-Driven**: Plugin mappings are stored in the `navigation` table's `plugin` column as JSON
- **Per-Module Configuration**: Each module can have its own unique field-to-plugin mappings
- **Dynamic UI**: Plugin icons only appear next to fields that have a mapping configured
- **Easy Management**: Admin interface at `pluginmap.php` for configuring mappings
- **One-to-One Mapping**: Each field can be mapped to exactly one plugin

## Setup Instructions

### 1. Database Migration

Run the SQL migration to add the plugin column:

```sql
ALTER TABLE `navigation`
ADD COLUMN IF NOT EXISTS `plugin` TEXT NULL AFTER `urn`;
```

Or run the migration file:

```bash
mysql -u root -p erpz < add_plugin_column.sql
```

### 2. Access Plugin Mapping Interface

1. Go to `erpconsole/manage.php`
2. Click the extension icon (⬡) next to any module
3. This opens `pluginmap.php?module=ModuleName`

### 3. Configure Field Mappings

1. Select a plugin from the dropdown for each field
2. Click "Save Mappings"
3. Mappings are saved to the database immediately

### 4. Using Mapped Plugins

- Fields with mappings will show a plugin icon
- Clicking the icon opens the mapped plugin directly
- Fields without mappings won't show a plugin icon

## Technical Implementation

### Database Structure

```sql
navigation table:
- id (int)
- nav (text) - Module name
- urn (text) - Module path
- plugin (text) - JSON mapping of fields to plugins
- created_at (datetime)
```

Example `plugin` column value:

```json
{
  "meeting_link": "Experte Meetings",
  "file_upload": "Document Manager",
  "description": "Gigafile Uploader"
}
```

### API Endpoints

**Get Plugin Mappings**

```
GET /modulename/api.php?getpluginmap=true
```

Returns: JSON object of field-to-plugin mappings

### Angular Integration

**In app.js:**

```javascript
$scope.fieldPluginMap = {}; // Initialized at controller start

// Loaded in $scope.admin()
$http.get($scope.url + "?getpluginmap=true").success(function (pluginData) {
  $scope.fieldPluginMap = pluginData || {};
});

// Used in $scope.allowplugin()
var mappedPlugin = $scope.fieldPluginMap[fieldName];
if (mappedPlugin) {
  // Open plugin directly
} else {
  // Show plugin selector
}
```

**In boot.html:**

```html
<!-- Plugin icon only shows if mapping exists -->
<img
  src="plugin.png"
  ng-if="fieldPluginMap[key]"
  ng-click="allowplugin(key,'addModal', $event)"
  title="Open {{fieldPluginMap[key]}}"
/>
```

## Files Modified/Created

### New Files

- `pluginmap.php` - Plugin mapping configuration UI
- `add_plugin_column.sql` - Database migration script
- `PLUGIN_MAPPING_README.md` - This documentation

### Modified Files

- `erpconsole/manage.php` - Added plugin mapping button
- `weather1/api.php` - Added getpluginmap endpoint
- `weather1/app/app.js` - Added plugin mapping logic
- `weather1/boot.html` - Added conditional plugin icon display
- `api.php` (root) - Added getpluginmap endpoint for other modules

## Extending to Other Modules

To add plugin mapping support to other modules:

1. **Update the module's api.php** (if using custom API):
   Add the getpluginmap endpoint code from weather1/api.php

2. **Update the module's app.js**:

   - Initialize `$scope.fieldPluginMap = {}`
   - Load mappings in admin/init function
   - Update allowplugin() to check mappings

3. **Update the module's boot.html**:
   - Add `ng-if="fieldPluginMap[key]"` to plugin icons
   - Add title attribute showing plugin name

## Available Plugins

The system automatically detects plugins from the `/plugins/` directory:

- Experte Meetings
- Document Manager
- Gigafile Uploader
- Multi Tools
- sagioLMS
- testplugin
- caresecure

## Benefits

1. **Better UX**: Users don't need to select plugins every time
2. **Consistency**: Enforces which plugins should be used for which fields
3. **Efficiency**: Faster data entry with direct plugin access
4. **Flexibility**: Easy to reconfigure without code changes
5. **Scalability**: Works across all modules with minimal setup

## Future Enhancements

- Multi-plugin support per field (filtered list)
- Field validation based on plugin type
- Plugin presets for common field types
- Bulk mapping configuration
- Plugin usage analytics
