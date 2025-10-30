# DWorkflow Installation Instructions

## Prerequisites

Before installing DWorkflow, ensure you have:

- Drupal 10.x or 11.x installed
- Composer available
- Access to Drush (recommended)
- Administrative access to your Drupal site

## Step 1: Install Dependencies

DWorkflow requires the Dynamic Entity Reference module. Install it using Composer:

```bash
composer require drupal/dynamic_entity_reference
```

Enable the Dynamic Entity Reference module:

```bash
drush en dynamic_entity_reference -y
```

Or via the UI: Go to **Extend** and enable "Dynamic Entity Reference"

## Step 2: Install DWorkflow Module

### Option A: Using Composer (Recommended for custom modules)

If you're managing this as a custom module in your project:

1. Place the `dworkflow` directory in `modules/custom/`:
   ```
   your-drupal-root/
   └── modules/
       └── custom/
           └── dworkflow/
   ```

2. Enable the module:
   ```bash
   drush en dworkflow -y
   ```

### Option B: Manual Installation

1. Download or copy the dworkflow module to your Drupal installation:
   ```
   your-drupal-root/
   └── modules/
       └── custom/
           └── dworkflow/
   ```

2. Enable via Drush:
   ```bash
   drush en dworkflow -y
   ```

   Or via the UI:
   - Navigate to **Extend** (`/admin/modules`)
   - Find "DWorkflow" in the list
   - Check the checkbox
   - Click "Install" at the bottom

3. Clear caches:
   ```bash
   drush cr
   ```

## Step 3: Verify Installation

Check that the module is installed correctly:

```bash
drush pm:list | grep dworkflow
```

You should see:
```
DWorkflow (dworkflow)    Enabled    Module
```

## Step 4: Initial Configuration

### A. Configure DWorkflow Settings

1. Navigate to **Configuration > Workflow > DWorkflow**
   - URL: `/admin/config/workflow/dworkflow`

2. Select **Enabled Content Types**:
   - Check the content types you want to use with workflows
   - Common choices: Article, Page, Basic page
   - For Open Social: Topic, Event, Book

3. Verify **Resource Location Vocabulary**:
   - Default is "resource_locations"
   - This vocabulary is created automatically during installation

4. Click **Save configuration**

### B. Review Resource Locations

The module automatically creates a "Resource Locations" vocabulary with example terms:

1. Navigate to **Structure > Taxonomy > Resource Locations**
   - URL: `/admin/structure/taxonomy/manage/resource_locations`

2. Review the example terms:
   - Google Drive - Main Folder
   - Project Server - /projects
   - SharePoint Site
   - GitHub Repository
   - Confluence Space

3. Edit, delete, or add new terms as needed for your organization

### C. Create Your First Workflow List

1. Navigate to **Structure > Workflow Lists**
   - URL: `/admin/structure/workflow-list`

2. Click **Add Workflow List**

3. Fill in the form:
   - **Name**: e.g., "Test Workflow"
   - **Machine name**: Auto-generated (e.g., "test_workflow")
   - **Description**: Optional description
   - **Assigned Users and Groups**: Start typing to find users or groups
   - **Resource Location Tags**: Select one or more resource locations

4. Click **Save**

## Step 5: Test the Installation

### Test 1: Assign Workflow to Content

1. Create or edit a piece of content (e.g., an Article)
2. Look for the **Workflow List** field in the edit form
3. Select your test workflow
4. Save the content
5. View the content - you should see "Workflow Information" displayed

### Test 2: Use Quick Edit

1. Navigate to **Structure > Workflow Lists**
2. Click **Quick Edit** on your test workflow
3. Modify the assignments
4. Click **Update Workflow**
5. Verify changes were saved

### Test 3: Use Assign Tab

1. View any content item (that has workflow field enabled)
2. Look for the **Assign Workflow** tab
3. Click it and select a workflow
4. Save and verify

## Optional: Group Module Integration

If you want to assign groups to workflows (for Open Social or Group module):

1. Install the Group module:
   ```bash
   composer require drupal/group
   drush en group -y
   ```

2. Create at least one group

3. Clear caches:
   ```bash
   drush cr
   ```

4. Groups will now appear in the workflow assignment field

## Post-Installation Tasks

### Set Permissions

1. Navigate to **People > Permissions**
   - URL: `/admin/people/permissions`

2. Configure DWorkflow permissions:
   
   **For Administrators:**
   - ✓ Administer workflow lists
   - ✓ Assign workflow lists to content
   - ✓ View workflow list assignments

   **For Content Editors:**
   - ✓ Assign workflow lists to content
   - ✓ View workflow list assignments

   **For Content Viewers:**
   - ✓ View workflow list assignments

3. Click **Save permissions**

### Configure Display Settings (Optional)

For each enabled content type:

1. Navigate to **Structure > Content types > [Type] > Manage display**
2. Find "Workflow Information" in the list
3. Adjust weight and visibility as needed
4. Click **Save**

## Verification Checklist

- [ ] Dynamic Entity Reference module installed and enabled
- [ ] DWorkflow module enabled
- [ ] Resource Locations vocabulary exists with terms
- [ ] At least one content type enabled in settings
- [ ] Test workflow created successfully
- [ ] Workflow assigned to test content
- [ ] Workflow information displays on content view
- [ ] Permissions configured correctly
- [ ] (Optional) Group module integration working

## Troubleshooting

### Issue: Module won't enable

**Solution:**
```bash
# Verify Dynamic Entity Reference is installed
composer require drupal/dynamic_entity_reference
drush en dynamic_entity_reference -y

# Clear caches and try again
drush cr
drush en dworkflow -y
```

### Issue: Can't see workflow field on content

**Solution:**
1. Check content type is enabled: `/admin/config/workflow/dworkflow`
2. Clear caches: `drush cr`
3. Verify field exists: `/admin/structure/types/manage/[type]/fields`

### Issue: Groups not showing up

**Solution:**
1. Install Group module: `composer require drupal/group`
2. Enable it: `drush en group -y`
3. Create at least one group
4. Clear caches: `drush cr`

### Issue: Permission denied errors

**Solution:**
Go to `/admin/people/permissions` and verify users have appropriate DWorkflow permissions.

### Issue: Vocabulary doesn't exist

**Solution:**
The vocabulary should be created automatically. If not:
1. Create it manually at `/admin/structure/taxonomy/add`
2. Name it "Resource Locations"
3. Machine name: "resource_locations"
4. Update setting at `/admin/config/workflow/dworkflow`

## Uninstallation

To remove DWorkflow:

```bash
# Uninstall the module
drush pmu dworkflow -y

# Clear caches
drush cr
```

**Note:** Uninstallation will:
- Remove the workflow list field from all content types
- Preserve the Resource Locations vocabulary (you can delete manually if needed)
- Not affect the Dynamic Entity Reference module

## Next Steps

After installation:

1. Read the README.md file for usage examples
2. Create workflows for your teams/projects
3. Assign workflows to relevant content
4. Train your team on using the Quick Edit feature
5. Customize the workflow information display if needed

## Getting Help

If you encounter issues:

1. Check the README.md file
2. Review this INSTALL file
3. Check Drupal logs: **Reports > Recent log messages**
4. Review the Dynamic Entity Reference module documentation
5. Check Drupal.org for similar issues

## System Requirements Summary

- **PHP**: 8.1 or higher (Drupal 10/11 requirement)
- **Drupal**: 10.x or 11.x
- **Required Modules**:
  - Node (core)
  - User (core)
  - Taxonomy (core)
  - Dynamic Entity Reference (contrib)
- **Optional Modules**:
  - Group (contrib) - for group assignments
- **Database**: MySQL 5.7.8+, MariaDB 10.3.7+, PostgreSQL 12+, or SQLite 3.26+

Congratulations! You've successfully installed DWorkflow.
