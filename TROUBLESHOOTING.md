# Workflow Assignment - Installation Troubleshooting

## Common Installation Issues

### Error: "Missing bundle entity, entity type node_type, entity id [TYPE]"

This error occurs when the module tries to add a field to a content type that doesn't exist on your site.

**Solution 1: Fix Automatically (Recommended)**

Run this Drush command to clean up and reconfigure:

```bash
# First, uninstall the module if it's partially installed
drush pmu workflow_assignment -y

# Clear caches
drush cr

# Re-enable the module
drush en workflow_assignment -y

# Clear caches again
drush cr
```

**Solution 2: Manual Configuration**

If the automatic method doesn't work:

1. Check which content types exist on your site:
   ```bash
   drush ev "print_r(array_keys(\Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple()));"
   ```

2. Edit the configuration file before installation:
   - Open: `workflow_assignment/config/install/workflow_assignment.settings.yml`
   - Update `allowed_content_types` to only include content types that exist on your site
   - Save the file

3. Install the module:
   ```bash
   drush en workflow_assignment -y
   drush cr
   ```

**Solution 3: Fix After Installation**

If you've already installed and got the error:

```bash
# Export current config (if needed)
drush cex -y

# Edit the config
drush config:edit workflow_assignment.settings

# Remove non-existent content types from 'allowed_content_types' array
# Save and exit

# Import the config
drush cim -y

# Clear caches
drush cr
```

## Verify Installation

After installation, verify everything is working:

```bash
# Check if the module is enabled
drush pm:list | grep workflow_assignment

# Check the configuration
drush config:get workflow_assignment.settings

# Check if the field was created
drush field:list node

# Test creating a workflow list
# Go to: /admin/structure/workflow-list/add
```

## Content Type Detection Script

Run this to see which content types are available on your site:

```php
<?php
// Save as check_content_types.php and run: drush php:script check_content_types.php

$node_type_storage = \Drupal::entityTypeManager()->getStorage('node_type');
$node_types = $node_type_storage->loadMultiple();

echo "Available content types on this site:\n";
echo "=====================================\n\n";

foreach ($node_types as $type) {
  echo "- " . $type->id() . " (" . $type->label() . ")\n";
}

echo "\nRecommended for workflow_assignment.settings.yml:\n";
echo "================================================\n";
echo "allowed_content_types:\n";
foreach ($node_types as $type) {
  echo "  - " . $type->id() . "\n";
}
```

## Open Social Specific

For Open Social installations, these content types typically exist:
- `topic` - Community discussions
- `event` - Community events  
- `event_enrollment` - Event enrollments (usually don't need workflows)
- `page` - May or may not exist depending on your setup

**Recommended Open Social Configuration:**

```yaml
allowed_content_types:
  - topic
  - event
```

Add `page` only if it exists on your site.

## Reconfigure After Installation

To change which content types have workflow support after installation:

1. Go to: **Configuration > Workflow > Workflow Assignment**
   (`/admin/config/workflow/workflow-assignment`)

2. Check only the content types you want

3. Click **Save configuration**

The module will automatically add/remove the field from content types based on your selection.

## Clean Reinstall

If you need to completely start over:

```bash
# 1. Uninstall the module
drush pmu workflow_assignment -y

# 2. Delete any remaining config
drush config:delete workflow_assignment.settings
drush cdel field.storage.node.field_workflow_list
drush cdel field.field.node.*.field_workflow_list

# 3. Clear caches
drush cr

# 4. Fix the settings file (see above)

# 5. Reinstall
drush en workflow_assignment -y
drush cr
```

## Getting Help

If you continue to have issues:

1. Check what content types exist: `/admin/structure/types`
2. Verify the module's requirements are met
3. Check Drupal logs: `/admin/reports/dblog`
4. Look for specific error messages

## Contact

For persistent issues, provide:
- Your Drupal version
- Your distribution (Standard Drupal, Open Social, etc.)
- List of content types on your site
- Full error message from installation
