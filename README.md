# Workflow Assignment Module

A custom Drupal 10 module that provides a flexible workflow system where you can create workflow lists containing assigned users and/or groups, with resource locations designated by taxonomy tags. All assignments can be changed on the fly.

## Features

- **Create Custom Workflow Lists** - Define named workflows with descriptions
- **Assign Users** - Add/remove users to workflows dynamically
- **Assign Groups** - Add/remove Open Social or Group module groups to workflows
- **Resource Location Tagging** - Tag workflows with resource locations using taxonomy
- **On-the-Fly Changes** - Modify all assignments at any time without restrictions
- **Content Assignment** - Assign workflow lists to any content type
- **Quick Edit Interface** - Rapid workflow modification without full edit form
- **Visual Workflow Info** - Display workflow assignments on content pages

## Concept

Unlike standard Drupal workflows (which manage content states), this module creates **assignment-based workflows** where:

1. You create a **Workflow List** (e.g., "Q1 Marketing Campaign")
2. You assign **Users and/or Groups** to this workflow list
3. You tag it with **Resource Locations** (taxonomy terms like "Google Drive Folder A", "Project Server", etc.)
4. You can then **assign this workflow list to content** (pages, topics, events)
5. All assignments can be **changed at any time** - add/remove users, groups, or resource tags on the fly

This is perfect for:
- Project-based workflows
- Team assignments
- Resource management
- Dynamic collaboration scenarios
- Open Social communities

## Requirements

- Drupal 10.x
- Node module (core)
- Taxonomy module (core)
- User module (core)
- Optional: Group module (for group assignments in Open Social)

## Installation

1. Copy the `workflow_assignment` folder to your Drupal installation's `modules/custom` directory
2. Enable the module:
   ```bash
   drush en workflow_assignment -y
   ```
3. Clear caches:
   ```bash
   drush cr
   ```

## Configuration

### 1. Initial Setup

Navigate to **Configuration > Workflow > Workflow Assignment** (`/admin/config/workflow/workflow-assignment`)

Configure:
- **Enabled Content Types** - Select which content types can have workflow lists assigned
- **Resource Location Vocabulary** - Choose the taxonomy vocabulary for resource locations (default: "resource_locations")

### 2. Create Resource Location Tags

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
- **Assigned Users** - Select users (can be changed anytime)
- **Assigned Groups** - Select groups if Group module is installed
- **Resource Location Tags** - Tag with relevant resource locations

## Usage

### Creating a Workflow List

1. Go to **Structure > Workflow Lists**
2. Click **Add Workflow List**
3. Fill in the details:
   - Name: "Q1 Marketing Campaign"
   - Description: "Marketing workflow for Q1 2025"
   - Assign users: Select team members
   - Assign groups: Select relevant groups
   - Tag resources: Select resource locations
4. Click **Save**

### Assigning Workflow to Content

**Method 1: From Content Edit**
1. Create or edit content (page, topic, event, etc.)
2. Look for the **Workflow List** field
3. Select the workflow list
4. Save

**Method 2: Using Assign Tab**
1. View any content item
2. Click the **Assign Workflow** tab
3. Select workflow list
4. Click **Assign Workflow**

### Changing Assignments On-The-Fly

**Quick Edit Method** (Fastest):
1. Go to **Structure > Workflow Lists**
2. Click **Quick Edit** on any workflow list
3. Modify users, groups, or resource tags
4. Click **Update Workflow**

**Full Edit Method**:
1. Go to **Structure > Workflow Lists**
2. Click **Edit** on any workflow list
3. Make changes
4. Click **Save**

### Viewing Workflow Information

When viewing content with an assigned workflow:
1. A **Workflow Information** section appears
2. Shows:
   - Workflow name
   - Assigned users
   - Resource locations

## Use Cases

### Example 1: Marketing Campaign Workflow

```
Workflow: "Summer 2025 Campaign"
Assigned Users: 
  - marketing_manager
  - content_writer
  - designer
Resource Locations:
  - Google Drive - Summer Campaign Folder
  - Trello Board - Summer 2025
  - Asset Library - /marketing/summer

Assigned To: 
  - Blog Post: "Summer Sale Announcement"
  - Event: "Summer Product Launch"
  - Page: "Summer Landing Page"
```

### Example 2: Documentation Project

```
Workflow: "API Documentation v2"
Assigned Users:
  - tech_writer
  - senior_developer
  - qa_lead
Assigned Groups:
  - Engineering Team
Resource Locations:
  - GitHub - /docs/api-v2
  - Confluence - API Docs Space

Assigned To:
  - Book: "API Reference Guide"
  - Topic: "API Migration Plan"
```

### Example 3: Open Social Community Project

```
Workflow: "Community Event Planning"
Assigned Groups:
  - Event Coordinators
  - Volunteer Team
Resource Locations:
  - Google Calendar - Events
  - Drive - Event Assets
  
Assigned To:
  - Event: "Annual Community Meetup"
  - Topic: "Volunteer Signup Discussion"
```

## Permissions

- **Administer workflow lists** - Create, edit, delete workflow lists and modify assignments
- **Assign workflow lists to content** - Assign and change workflows on content
- **View workflow list assignments** - View workflow information on content

## API Usage

### Programmatic Workflow Creation

```php
use Drupal\workflow_assignment\Entity\WorkflowList;

// Create a new workflow list
$workflow = WorkflowList::create([
  'id' => 'my_project',
  'label' => 'My Project Workflow',
  'description' => 'Workflow for my awesome project',
]);

// Assign users
$workflow->addAssignedUser(5);  // User ID 5
$workflow->addAssignedUser(12); // User ID 12

// Assign groups
$workflow->addAssignedGroup(3); // Group ID 3

// Add resource tags
$workflow->addResourceTag(10);  // Term ID 10

$workflow->save();
```

### Modifying Workflow On-The-Fly

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

// Get assigned users
$users = $workflow->getAssignedUsers();
// Returns: [5, 12, 20]

// Get assigned groups
$groups = $workflow->getAssignedGroups();

// Get resource tags
$tags = $workflow->getResourceTags();

// Get metadata
$created = $workflow->getCreatedTime();
$changed = $workflow->getChangedTime();
```

## Open Social Integration

This module is designed to work seamlessly with Open Social:

- **Group Support** - Automatically detects and supports Group module groups
- **Content Types** - Pre-configured for Open Social content types (topic, event, book)
- **Team Collaboration** - Perfect for community-based workflows
- **Resource Sharing** - Tag shared resources for easy team access

## Architecture

### Key Components

1. **WorkflowList Entity** - Config entity storing workflow data
2. **WorkflowListForm** - Full create/edit form
3. **QuickEditWorkflowForm** - Streamlined on-the-fly editing
4. **NodeAssignWorkflowForm** - Assign workflows to content
5. **WorkflowListListBuilder** - Administration interface

### Storage

- Workflow lists: Configuration entities (exportable)
- Content assignments: Field on node entities
- Resource locations: Taxonomy terms

## Troubleshooting

### Workflow List Field Not Showing

1. Check module is enabled: `drush pm:list | grep workflow_assignment`
2. Clear caches: `drush cr`
3. Verify content type is enabled in settings
4. Check field configuration: `/admin/structure/types/manage/[content-type]/fields`

### Groups Not Available

- Install and enable the Group module
- Clear caches after enabling Group module
- Create at least one group

### Resource Tags Not Showing

1. Verify the vocabulary exists: `/admin/structure/taxonomy`
2. Check vocabulary is selected in settings
3. Create terms in the vocabulary

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

```bash
drush pmu workflow_assignment -y
```

## Development

### Extending the Module

You can extend workflow lists with additional functionality:

```php
/**
 * Implements hook_workflow_list_presave().
 */
function mymodule_workflow_list_presave($entity) {
  // Custom logic before saving workflow lists
}

/**
 * Implements hook_workflow_list_insert().
 */
function mymodule_workflow_list_insert($entity) {
  // React to new workflow list creation
}
```

## Support

For issues or feature requests:
1. Review this documentation
2. Check Drupal.org issue queues
3. Review Drupal API documentation

## License

This module is provided as-is for use with Drupal 10.

## Credits

Designed for flexible, dynamic workflow management in Drupal 10 and Open Social distributions.
