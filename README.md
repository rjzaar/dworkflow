# DWorkflow - Dynamic Workflow Assignment Module

A custom Drupal 10/11 module that provides a flexible workflow system where you can create workflow lists with properly configured entity reference fields for assigning users and/or groups, along with resource locations designated by taxonomy tags. All assignments can be changed on the fly.

## Features

- **Create Custom Workflow Lists** - Define named workflows with descriptions
- **Proper Entity Reference Fields** - Correctly configured fields to assign both users AND groups
- **Flexible Assignment Widget** - Add multiple users and/or groups with type selection
- **Assign Groups** - Add/remove Open Social or Group module groups to workflows
- **Resource Location Tagging** - Tag workflows with resource locations using taxonomy
- **On-the-Fly Changes** - Modify all assignments at any time without restrictions
- **Content Assignment** - Assign workflow lists to any content type
- **Quick Edit Interface** - Rapid workflow modification without full edit form
- **Visual Workflow Info** - Display workflow assignments on content pages

## Key Implementation

This module uses **properly configured entity reference fields** with:
- Multiple value fields for assignments
- Type selector (User/Group) for each entry
- Entity autocomplete for each type
- AJAX-powered dynamic forms
- Add/Remove buttons for entries

## Requirements

- Drupal 10.x or 11.x
- Node module (core)
- Taxonomy module (core)
- User module (core)
- Optional: Group module (for group assignments in Open Social)

## Installation

1. **Copy the module:**
   Copy the `dworkflow` folder to your Drupal installation's `modules/custom` directory

2. **Enable the module:**
   ```bash
   drush en dworkflow -y
   ```

3. **Clear caches:**
   ```bash
   drush cr
   ```

## Configuration

### 1. Configure Module Settings

Navigate to **Configuration > Workflow > DWorkflow** (`/admin/config/workflow/dworkflow`)

Configure:
- **Enabled Content Types** - Select which content types can have workflow lists assigned
- **Resource Location Vocabulary** - Choose the taxonomy vocabulary for resource locations (default: "resource_locations")

### 2. Create Resource Location Terms

Navigate to **Structure > Taxonomy > Resource Locations** (`/admin/structure/taxonomy/manage/resource_locations`)

Create terms for your resource locations, for example:
- Google Drive - Marketing Folder
- Project Server - /projects/q1
- SharePoint Site
- GitHub Repository
- Confluence Space

### 3. Create Workflow Lists

Navigate to **Structure > Workflow Lists** (`/admin/structure/workflow-list`)

Click **Add Workflow List** and configure:
- **Name** - Give your workflow a descriptive name
- **Description** - Optional description
- **Assigned Users and Groups** - Select users and/or groups (unified field!)
- **Resource Location Tags** - Tag with relevant resource locations

## Usage Examples

### Example 1: Marketing Campaign Workflow

1. Go to **Structure > Workflow Lists**
2. Click **Add Workflow List**
3. Fill in the details:
   - Name: "Q1 Marketing Campaign"
   - Description: "Marketing workflow for Q1 2025"
   - Assign entities: Select team members (users) and marketing group (group)
   - Tag resources: Select "Google Drive - Marketing" and "Trello - Q1 Board"
4. Click **Save**

### Example 2: Assigning Workflows to Content

**Method 1: From Content Edit Form**
- Create or edit content (page, article, etc.)
- Look for the **Workflow List** field
- Select the workflow list
- Save

**Method 2: Using Assign Tab**
- View any content item
- Click the **Assign Workflow** tab
- Select workflow list
- Click **Assign Workflow**

### Example 3: Quick Editing Workflows

**Quick Edit Method (Fastest):**
- Go to **Structure > Workflow Lists**
- Click **Quick Edit** on any workflow list
- Modify users, groups, or resource tags
- Click **Update Workflow**

**Full Edit Method:**
- Go to **Structure > Workflow Lists**
- Click **Edit** on any workflow list
- Make changes
- Click **Save**

## Viewing Workflow Information

When viewing content with an assigned workflow, a **Workflow Information** section appears showing:
- Workflow name and description
- Assigned users (with "user" badge)
- Assigned groups (with "group" badge)
- Resource locations

Example display:
```
Workflow Information
━━━━━━━━━━━━━━━━━━━━
Workflow: "Summer 2025 Campaign"

Assigned Users and Groups:
• [USER] marketing_manager
• [USER] content_writer
• [GROUP] Marketing Team

Resource Locations:
• Google Drive - Summer Campaign Folder
• Trello Board - Summer 2025
```

## Permissions

- **administer workflow lists** - Create, edit, delete workflow lists and modify assignments
- **assign workflow lists to content** - Assign and change workflows on content
- **view workflow list assignments** - View workflow information on content

## API / Programmatic Usage

### Creating a Workflow with Mixed Assignments

```php
use Drupal\dworkflow\Entity\WorkflowList;

// Create a new workflow list
$workflow = WorkflowList::create([
  'id' => 'my_project',
  'label' => 'My Project Workflow',
  'description' => 'Workflow for my awesome project',
]);

// Add users and groups
$workflow->addAssignedUser(5);  // User ID 5
$workflow->addAssignedUser(12); // User ID 12
$workflow->addAssignedGroup(3); // Group ID 3

// Add resource tags
$workflow->addResourceTag(10); // Term ID 10
$workflow->addResourceTag(15); // Term ID 15

$workflow->save();
```

### Modifying an Existing Workflow

```php
// Load workflow
$workflow = \Drupal::entityTypeManager()
  ->getStorage('workflow_list')
  ->load('my_project');

// Add a user
$workflow->addAssignedUser(20);

// Remove a user
$workflow->removeAssignedUser(5);

// Change resource tags
$workflow->setResourceTags([10, 11, 12]);

$workflow->save();
```

### Assigning Workflow to Content

```php
use Drupal\node\Entity\Node;

// Load node
$node = Node::load(123);

// Assign workflow
$node->set('field_workflow_list', 'my_project');
$node->save();

// Remove workflow
$node->set('field_workflow_list', NULL);
$node->save();
```

### Getting Workflow Information

```php
// Load workflow
$workflow = \Drupal::entityTypeManager()
  ->getStorage('workflow_list')
  ->load('my_project');

// Get all assigned entities (users and groups)
$entities = $workflow->getAssignedEntities();
// Returns: [
//   ['entity_type' => 'user', 'entity_id' => 5, 'entity' => $user_entity],
//   ['entity_type' => 'group', 'entity_id' => 3, 'entity' => $group_entity],
// ]

// Get only user IDs
$users = $workflow->getAssignedUsers();
// Returns: [5, 12, 20]

// Get only group IDs
$groups = $workflow->getAssignedGroups();
// Returns: [3]

// Get resource tags
$tags = $workflow->getResourceTags();
// Returns: [10, 15]

// Get metadata
$created = $workflow->getCreatedTime();
$changed = $workflow->getChangedTime();
```

## Open Social Integration

This module is designed to work seamlessly with Open Social:
- **Group Support** - Automatically detects and supports Group module groups
- **Content Types** - Works with Open Social content types (topic, event, book, etc.)
- **Team Collaboration** - Perfect for community-based workflows
- **Resource Sharing** - Tag shared resources for easy team access

## Troubleshooting

### Module Won't Enable

- Check dependencies are met
- Clear caches:
  ```bash
  drush cr
  ```

### Can't Assign Workflows to Content

- Verify content type is enabled in settings: `/admin/config/workflow/dworkflow`
- Check field configuration: `/admin/structure/types/manage/[content-type]/fields`

### Groups Not Available

- Install and enable the Group module
- Clear caches after enabling Group module
- Create at least one group

### Resource Locations Not Available

- Verify the vocabulary exists: `/admin/structure/taxonomy`
- Check vocabulary is selected in settings
- Create terms in the vocabulary

### Permission Issues

Go to **Configuration > People > Permissions** and verify:
- Users have appropriate permissions for their role
- "Administer workflow lists" for administrators
- "Assign workflow lists" for content editors

## Uninstallation

The module will automatically:
- Remove workflow list field from all content types
- Remove field storage
- Keep the Resource Locations vocabulary (contains user data)

To uninstall:
```bash
drush pmu dworkflow -y
```

## Implementation Approach

### Proper Entity Reference Fields

This module uses correctly configured entity reference fields with:

**Multi-value Form Widget:**
```
For each assignment:
  [Type: User ▼] [Select user...     ]
  [Type: Group ▼] [Select group...   ]
  [Type: User ▼] [Select user...     ]
  
  [Add another] [Remove last]
```

**Storage Format:**
```yaml
assigned_entities:
  - target_type: user
    target_id: 5
  - target_type: group
    target_id: 2
  - target_type: user
    target_id: 12
```

**Benefits:**
- Native Drupal entity reference
- No contrib dependencies
- Proper type selection per entry
- AJAX-powered dynamic forms
- Full control over validation
- Easy to extend

## Technical Details

- **WorkflowList Entity** - Config entity storing workflow data
- **Storage Format** - Assignments stored as array of target_type/target_id pairs
- **Form System** - AJAX-powered multi-value widget with type selectors
- **Display** - Custom theme template with CSS styling and type badges

## Support

For issues or feature requests, please refer to:
- This documentation
- Drupal.org Dynamic Entity Reference module documentation
- Drupal API documentation

## License

This module is provided as-is for use with Drupal 10/11.

## Author

Designed for flexible, dynamic workflow management in Drupal 10/11 and Open Social distributions.
