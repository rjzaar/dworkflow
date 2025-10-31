# DWorkflow - Workflow Assignment Field

A Drupal 10/11 module that provides a **field type** for assigning multiple users and/or groups directly to content for workflow management.

## Overview

This module provides a custom field type that you can add to any content type (or other fieldable entity). The field allows editors to assign multiple users and/or groups to the content, with each assignment specifying whether it's a user or group.

**No separate entities needed** - assignments are stored directly on the content as field values.

## Features

- **Custom Field Type**: Add "Workflow Assignment" field to any content type
- **Multi-Value**: Assign multiple users and/or groups to a single piece of content
- **Type Selection**: Choose User or Group for each assignment
- **Entity Autocomplete**: Easy search and selection
- **AJAX Updates**: Type selector dynamically updates autocomplete
- **Visual Display**: Formatted list with badges showing user/group type
- **Group Module Support**: Automatically detects and supports Group module

## Requirements

- Drupal 10.x or 11.x
- Node module (core)
- User module (core)
- Optional: Group module (for group assignments)

## Installation

1. **Copy the module:**
   ```bash
   cp -r dworkflow /path/to/drupal/modules/custom/
   ```

2. **Enable the module:**
   ```bash
   drush en dworkflow -y
   drush cr
   ```

## Usage

### Adding the Field to a Content Type

1. Navigate to **Structure > Content types > [Your Type] > Manage fields**
   - Example: `/admin/structure/types/manage/article/fields`

2. Click **Add field**

3. Under "Add a new field":
   - **Field type**: Select "Workflow Assignment"
   - **Label**: Enter a label (e.g., "Assigned To", "Workflow Team", "Reviewers")

4. Click **Save and continue**

5. Configure field settings:
   - **Allowed number of values**: Unlimited (default and recommended)
   - Or set a specific limit if needed

6. Click **Save field settings**

7. Configure form display settings (optional)

8. Click **Save settings**

### Assigning Users and Groups to Content

When editing content with the workflow assignment field:

1. **For each assignment:**
   - Select **Type** (User or Group)
   - Use **autocomplete** to find and select the user or group
   - Click **Add another item** to add more assignments

2. **Save** the content

### Viewing Assignments

When viewing content, assigned users and groups are displayed with:
- **Badges** indicating type (USER or GROUP)
- **Links** to user/group profiles
- **Clean formatting** for easy scanning

## Field Configuration

### Field Settings

- **Allowed number of values**: 
  - Unlimited (recommended)
  - Limited (set specific number)

### Form Display

The field uses the "Workflow Assignment Selector" widget:
- Type dropdown (User/Group)
- Entity autocomplete
- AJAX-powered updates

### Display Settings

The field uses the "Workflow Assignment List" formatter:
- Lists all assignments
- Shows type badges
- Links to entities
- Styled display

## Examples

### Example 1: Article with Review Team

**Content Type:** Article

**Field Label:** "Review Team"

**Assignments:**
```
[USER]  editor_jane
[USER]  writer_john
[GROUP] Editorial Team
```

### Example 2: Project with Collaborators

**Content Type:** Project

**Field Label:** "Collaborators"

**Assignments:**
```
[USER]  project_manager
[USER]  developer_alice
[USER]  developer_bob
[GROUP] QA Team
[GROUP] DevOps Team
```

### Example 3: Event with Organizers

**Content Type:** Event

**Field Label:** "Event Organizers"

**Assignments:**
```
[USER]  event_coordinator
[GROUP] Volunteers
[GROUP] Sponsor Committee
```

## API Usage

### Getting Assignments Programmatically

```php
// Load a node
$node = \Drupal\node\Entity\Node::load(123);

// Check if field exists
if ($node->hasField('field_workflow_assignment')) {
  
  // Get all assignments
  $assignments = $node->get('field_workflow_assignment');
  
  foreach ($assignments as $assignment) {
    $type = $assignment->target_type;     // 'user' or 'group'
    $id = $assignment->target_id;         // Entity ID
    $entity = $assignment->getEntity();   // Loaded entity object
    
    if ($entity) {
      $name = $entity->label();
      echo "{$type}: {$name} (ID: {$id})\n";
    }
  }
}
```

### Adding Assignments Programmatically

```php
use Drupal\node\Entity\Node;

// Load or create node
$node = Node::load(123);

// Add assignments
$node->field_workflow_assignment[] = [
  'target_type' => 'user',
  'target_id' => 5,
];

$node->field_workflow_assignment[] = [
  'target_type' => 'group',
  'target_id' => 3,
];

$node->field_workflow_assignment[] = [
  'target_type' => 'user',
  'target_id' => 12,
];

$node->save();
```

### Querying Content by Assignment

```php
// Find all nodes assigned to user 5
$query = \Drupal::entityQuery('node')
  ->condition('field_workflow_assignment.target_type', 'user')
  ->condition('field_workflow_assignment.target_id', 5)
  ->accessCheck(TRUE);

$nids = $query->execute();

// Find all nodes assigned to group 3
$query = \Drupal::entityQuery('node')
  ->condition('field_workflow_assignment.target_type', 'group')
  ->condition('field_workflow_assignment.target_id', 3)
  ->accessCheck(TRUE);

$nids = $query->execute();
```

### Getting All Users Assigned

```php
$users = [];
$assignments = $node->get('field_workflow_assignment');

foreach ($assignments as $assignment) {
  if ($assignment->target_type === 'user') {
    $users[] = $assignment->target_id;
  }
}

// Load all user entities at once
$user_entities = \Drupal::entityTypeManager()
  ->getStorage('user')
  ->loadMultiple($users);
```

### Getting All Groups Assigned

```php
$groups = [];
$assignments = $node->get('field_workflow_assignment');

foreach ($assignments as $assignment) {
  if ($assignment->target_type === 'group') {
    $groups[] = $assignment->target_id;
  }
}

// Load all group entities
if (!empty($groups)) {
  $group_entities = \Drupal::entityTypeManager()
    ->getStorage('group')
    ->loadMultiple($groups);
}
```

## Use Cases

### 1. Editorial Workflow
Add field to Article content type to track who needs to review/approve content.

### 2. Project Management
Add field to Project content type to assign team members and departments.

### 3. Task Assignment
Add field to Task content type to assign responsible users and groups.

### 4. Event Organization
Add field to Event content type to track organizers and volunteer teams.

### 5. Document Collaboration
Add field to Document content type to manage who can collaborate.

## Advantages

### Simple Integration
- Just add the field to your content type
- No complex configuration needed
- Works like any other field

### Flexible
- Use on any fieldable entity (nodes, terms, custom entities)
- Set per-entity-type field labels
- Configure number of values allowed

### Powerful
- Reference users and groups in one field
- Query by assignment
- Full entity access (name, email, etc.)

### User-Friendly
- Clear type selection
- Easy autocomplete
- Visual badges
- No training needed

## Field Storage

Assignments are stored directly in the field table:

```sql
-- Field table: node__field_workflow_assignment
CREATE TABLE node__field_workflow_assignment (
  entity_id INT,
  revision_id INT,
  delta INT,
  field_workflow_assignment_target_type VARCHAR(32),
  field_workflow_assignment_target_id INT,
  ...
);

-- Example data:
-- entity_id=123, delta=0, target_type='user', target_id=5
-- entity_id=123, delta=1, target_type='group', target_id=3
-- entity_id=123, delta=2, target_type='user', target_id=12
```

## Group Module Integration

If the Group module is installed and enabled:
- The "Group" option appears in the type selector
- Group autocomplete works automatically
- Group entities are loaded and displayed
- No additional configuration needed

## Permissions

Field access is controlled by standard Drupal field permissions:

- **Edit own [field]**: Users can edit assignments on their own content
- **Edit any [field]**: Users can edit assignments on any content
- **View [field]**: Users can view assignments

Configure via **People > Permissions** or using modules like Field Permissions.

## Theming

### Template

Override the template: `templates/dworkflow-assignment-list.html.twig`

Available variables:
```twig
assignments: [
  {
    type: 'user' or 'group',
    entity: Entity object,
    label: Entity label,
    url: Entity URL
  }
]
```

### CSS

Override styles by targeting:
```css
.dworkflow-assignments { }
.dworkflow-assignment-list { }
.dworkflow-assignment-item { }
.dworkflow-badge { }
.dworkflow-badge--user { }
.dworkflow-badge--group { }
```

## Uninstallation

To remove the module:

1. **Remove the field from all content types:**
   - Structure > Content types > [Type] > Manage fields
   - Delete the workflow assignment field
   - Repeat for all content types using the field

2. **Uninstall the module:**
   ```bash
   drush pmu dworkflow -y
   ```

**Note:** Removing the field will delete all assignment data!

## Troubleshooting

### Field doesn't appear in field type list

- Clear caches: `drush cr`
- Verify module is enabled: `drush pml | grep dworkflow`

### Group option doesn't appear

- Install and enable Group module
- Clear caches: `drush cr`

### AJAX not working

- Clear caches: `drush cr`
- Check browser console for JavaScript errors
- Verify jQuery is loaded

### Entities not loading

- Check entity IDs are valid
- Verify entities haven't been deleted
- Check entity access permissions

## Technical Details

### Field Type

- **ID**: `dworkflow_assignment`
- **Cardinality**: Unlimited
- **Properties**: `target_type` (string), `target_id` (integer)
- **Storage**: Database table per entity type

### Widget

- **ID**: `dworkflow_assignment_default`
- **Type**: Select + Entity Autocomplete
- **AJAX**: Yes (type selector updates autocomplete)

### Formatter

- **ID**: `dworkflow_assignment_default`
- **Output**: Themed list with badges
- **Links**: Yes (to entity pages)

## Support

For issues, questions, or contributions:

1. Check this documentation
2. Review Drupal.org documentation on custom field types
3. Check the Field API documentation

## License

GPL-2.0-or-later

## Author

Designed for flexible workflow management in Drupal 10/11.
