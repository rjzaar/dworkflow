# Workflow Assignment Module - IMPROVED VERSION

A custom Drupal 10+ module that provides a flexible workflow system with dedicated workflow tabs and destination location support.

## 🆕 NEW FEATURES IN THIS IMPROVED VERSION

### 1. **Dedicated Workflow Tab**
- Workflows now appear on their own separate tab on content pages
- Clean, organized interface specifically for workflow management
- Easy access without cluttering the main content view
- Tab only appears on content types that have workflows enabled

### 2. **Destination Location System**
- New "Destination Locations" taxonomy vocabulary
- **Two default destination locations pre-configured:**
  - **Public** - For publicly accessible content destinations
  - **Private** - For restricted/private content destinations
- Easily add more destination locations as needed
- Visual distinction between Public and Private in the UI

### 3. **Enhanced User Interface**
- Improved styling with color-coded destination tags
- Visual icons for destination locations (📍)
- Better organization of workflow information
- Responsive and modern design

## Features

### Core Functionality
- ✅ Create Custom Workflow Lists with descriptions
- ✅ Assign Users to workflows dynamically
- ✅ Assign Groups to workflows (Open Social/Group module support)
- ✅ Tag workflows with Resource Locations
- ✅ **NEW:** Tag workflows with Destination Locations
- ✅ **NEW:** Separate Workflow tab on content pages
- ✅ On-the-Fly workflow modifications
- ✅ Quick Edit interface for rapid changes
- ✅ Visual workflow information display

### Destination Locations
The destination location feature allows you to specify where workflow content should be published or made available:

- **Public Destination**: Content available to all users
- **Private Destination**: Content with restricted access
- **Custom Destinations**: Add your own destination types

## Requirements

- Drupal 10.x or 11.x
- Node module (core)
- Taxonomy module (core)
- User module (core)
- Optional: Group module (for group assignments)

## Installation

1. **Copy Module Files**
   ```bash
   cp -r workflow_assignment /path/to/drupal/modules/custom/
   ```

2. **Enable the Module**
   ```bash
   drush en workflow_assignment -y
   drush cr
   ```

3. **Verify Installation**
   - Check that the Resource Locations vocabulary was created
   - Check that the Destination Locations vocabulary was created with Public and Private terms
   - Navigate to Configuration > Workflow > Workflow Assignment

## Configuration

### Step 1: Configure Content Types

1. Go to: `/admin/config/workflow/workflow-assignment`
2. Select which content types should have workflow support
3. Choose the taxonomy vocabularies for:
   - Resource Locations (default: resource_locations)
   - Destination Locations (default: destination_locations)
4. Save configuration

The workflow field will automatically be added to selected content types.

### Step 2: Set Up Resource Locations

1. Go to: `/admin/structure/taxonomy/manage/resource_locations`
2. Add terms for your resource locations, such as:
   - Google Drive - Marketing Folder
   - Project Server - /projects/q1
   - SharePoint Site
   - GitHub Repository

### Step 3: Manage Destination Locations

1. Go to: `/admin/structure/taxonomy/manage/destination_locations`
2. The default terms (Public and Private) are already created
3. Add additional destination locations as needed:
   - Internal Wiki
   - Customer Portal
   - Partner Site
   - etc.

### Step 4: Create Workflow Lists

1. Go to: `/admin/structure/workflow-list`
2. Click "Add Workflow List"
3. Fill in:
   - **Name**: Descriptive workflow name
   - **Description**: Optional details
   - **Assigned Users**: Select team members
   - **Assigned Groups**: Select groups (if applicable)
   - **Resource Location Tags**: Where resources are stored
   - **Destination Locations**: Where content will be published (Public/Private/etc.)
4. Save

## Usage

### Accessing the Workflow Tab

1. Navigate to any content item that has workflows enabled
2. Click the **"Workflow"** tab (appears next to View/Edit tabs)
3. View all workflow information in one organized place
4. Click "Assign Workflow" or "Change Workflow" to modify

### Assigning Workflows to Content

**Method 1: From Content Edit Form**
- Edit your content
- Find the "Workflow List" field
- Select a workflow
- Save

**Method 2: From the Workflow Tab** (Recommended)
- Go to content page
- Click "Workflow" tab
- Click "Assign Workflow" button
- Select workflow from dropdown
- Save

### Quick Editing Workflows

**Fastest method to update workflow assignments:**

1. Go to: `/admin/structure/workflow-list`
2. Click "Quick Edit" on any workflow
3. Modify:
   - Assigned users
   - Assigned groups
   - Resource locations
   - **NEW:** Destination locations
4. Click "Update Workflow"

Changes apply immediately to all content using this workflow.

### Viewing Workflow Information

When viewing content with an assigned workflow, the Workflow tab displays:

- Workflow name and description
- Assigned users (with full names)
- Assigned groups
- Resource locations
- **NEW:** Destination locations with visual indicators

#### Example Display:

```
Workflow Information
-------------------
Name: Q1 Marketing Campaign

Description: Marketing workflow for Q1 2025

Assigned Users:
• John Smith
• Jane Doe
• Marketing Manager

Resource Locations:
• Google Drive - Marketing Folder
• Trello Board - Q1 Projects

Destination Locations:
📍 Public
📍 Customer Portal
```

## Use Case Examples

### Example 1: Public Blog Post Workflow

```
Workflow: "Blog Publishing Workflow"
Assigned Users:
  - content_writer
  - editor
  - seo_specialist
  
Resource Locations:
  - Google Drive - Blog Drafts
  - Media Library - Blog Images

Destination Locations:
  - Public  ✓ (visible to all)

Assigned To:
  - Blog Post: "10 Tips for Better Productivity"
  - Blog Post: "Company News Update"
```

### Example 2: Internal Documentation Workflow

```
Workflow: "Internal Wiki Documentation"
Assigned Users:
  - tech_writer
  - department_head
  - documentation_reviewer

Resource Locations:
  - Confluence - Engineering Docs
  - GitHub - /docs/internal

Destination Locations:
  - Private  ✓ (restricted access)
  - Internal Wiki  ✓

Assigned To:
  - Page: "Employee Onboarding Guide"
  - Page: "Internal API Documentation"
```

### Example 3: Mixed Access Project Workflow

```
Workflow: "Product Launch 2025"
Assigned Users:
  - project_manager
  - marketing_team
  - sales_team

Resource Locations:
  - Project Server - /launch-2025
  - Asset Library - Product Launch

Destination Locations:
  - Public  ✓ (marketing materials)
  - Private  ✓ (internal strategy docs)
  - Partner Site  ✓ (partner resources)

Assigned To:
  - Event: "Product Launch Event"
  - Page: "Product Launch Strategy" (Private)
  - Page: "Product Announcement" (Public)
```

## API Usage

### Creating a Workflow with Destinations

```php
use Drupal\workflow_assignment\Entity\WorkflowList;

$workflow = WorkflowList::create([
  'id' => 'my_project',
  'label' => 'My Project Workflow',
  'description' => 'Workflow for my project',
]);

// Assign users
$workflow->addAssignedUser(5);
$workflow->addAssignedUser(12);

// Add resource tags
$workflow->addResourceTag(10);

// Add destination tags (NEW!)
$workflow->addDestinationTag(1);  // Public
$workflow->addDestinationTag(2);  // Private

$workflow->save();
```

### Getting Destination Information

```php
$workflow = \Drupal::entityTypeManager()
  ->getStorage('workflow_list')
  ->load('my_project');

// Get destination tags
$destinations = $workflow->getDestinationTags();
// Returns: [1, 2] (term IDs)

// Load destination terms
$term_storage = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term');
  
foreach ($destinations as $tid) {
  $term = $term_storage->load($tid);
  echo $term->getName();  // "Public" or "Private"
}
```

## Permissions

- **Administer workflow lists** - Create, edit, delete workflows
- **Assign workflow lists to content** - Assign/change workflows on content
- **View workflow list assignments** - View workflow information

## Theming

### Template Files

- `workflow-tab-content.html.twig` - Workflow tab display template

### CSS Classes

- `.workflow-tab-content` - Main tab container
- `.workflow-section` - Section wrapper
- `.workflow-field` - Individual field display
- `.workflow-field--destinations` - Destination fields (green themed)
- `.destination-tag--public` - Public destination styling (blue)
- `.destination-tag--private` - Private destination styling (red)

### Customizing Destination Colors

Edit `css/workflow-tab.css`:

```css
.destination-tag--public {
  background: #e3f2fd !important;
  border-color: #90caf9 !important;
  color: #1976d2;
}

.destination-tag--private {
  background: #fce4ec !important;
  border-color: #f48fb1 !important;
  color: #c2185b;
}
```

## Architecture

### Key Components

1. **WorkflowList Entity** (`src/Entity/WorkflowList.php`)
   - Config entity storing workflow data
   - Methods for managing users, groups, resources, and destinations

2. **NodeWorkflowController** (`src/Controller/NodeWorkflowController.php`)
   - Handles workflow tab display
   - NEW: Dedicated controller for tab functionality

3. **Forms**
   - `WorkflowListForm.php` - Full workflow create/edit
   - `QuickEditWorkflowForm.php` - Streamlined editing
   - `NodeAssignWorkflowForm.php` - Assign workflows to content
   - `WorkflowAssignmentSettingsForm.php` - Module configuration

4. **Routing**
   - Dedicated route for workflow tab (`/node/{nid}/workflow`)
   - Tab integration via `links.task.yml`

## Upgrade Notes

### Upgrading from Original Version

If you have the original dworkflow module installed:

1. **Backup your database**
2. Install this improved version
3. Run update hooks:
   ```bash
   drush updatedb
   drush cr
   ```
4. The update will:
   - Create the destination_locations vocabulary
   - Add Public and Private default terms
   - Update module configuration

## Troubleshooting

### Workflow Tab Not Appearing

- Check content type is enabled in settings
- Verify user has "view workflow list assignments" permission
- Clear caches: `drush cr`

### Destination Locations Missing

- Run update hooks: `drush updatedb`
- Manually create vocabulary at `/admin/structure/taxonomy/add`
- Set vocabulary in settings: `/admin/config/workflow/workflow-assignment`

### Field Not on Content Type

- Save settings again to trigger field creation
- Check field configuration: `/admin/structure/types/manage/[type]/fields`
- Manually add if needed: field name is `field_workflow_list`

## Development

### Adding Custom Destination Types

1. Go to `/admin/structure/taxonomy/manage/destination_locations`
2. Click "Add term"
3. Create your custom destination (e.g., "Partner Portal", "Mobile App")
4. Optionally add custom CSS in `css/workflow-tab.css`:

```css
.destination-tag--partner-portal {
  background: #fff3e0 !important;
  border-color: #ffb74d !important;
  color: #f57c00;
}
```

### Extending the Module

Implement hooks for custom functionality:

```php
/**
 * Implements hook_workflow_list_presave().
 */
function mymodule_workflow_list_presave($entity) {
  // React to workflow changes
  if ($entity->hasDestinationTag('public')) {
    // Custom logic for public destinations
  }
}
```

## Support

For issues or feature requests:
- Review this documentation
- Check Drupal.org documentation
- Review GitHub issues

## License

This module is provided as-is for use with Drupal 10+.

## Credits

Improved version with:
- Dedicated workflow tab functionality
- Destination location system with Public/Private defaults
- Enhanced UI and styling
- Better code organization

---

**Version:** 2.0  
**Last Updated:** 2025  
**Drupal Compatibility:** 10.x, 11.x
