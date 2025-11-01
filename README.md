# DWorkflow - Enhanced Workflow Assignment Field

A Drupal 10/11 module that provides a **field type** for assigning multiple users and/or groups to content with **titles and comments** for workflow management.

## What's New in This Version

### Enhanced Features
- ✨ **Title for each assignment** - Give each workflow assignment a descriptive title
- 💬 **Comments from assignees** - Add comments or notes for each assignment
- 📊 **Row-based layout** - All assignment details (title, type, assignee, comment) display on the same row
- 🎯 **Tab support ready** - Can be placed in a separate tab using Field Group module

### Display Layout

Each assignment now shows in a clean row format:
```
Title          | Type  | Assignee      | Comment
Review Phase 1 | USER  | editor_jane   | Initial content review
Final Approval | GROUP | Editorial Team| Ready for publication
```

## Requirements

- Drupal 10.x or 11.x
- Node module (core)
- User module (core)
- Optional: Group module (for group assignments)
- **Recommended: Field Group module** (for tab organization)

## Installation

### 1. Install the Module

```bash
# Copy module to custom modules directory
cp -r dworkflow /path/to/drupal/modules/custom/

# Enable the module
drush en dworkflow -y
drush cr
```

### 2. Update Existing Installations

If you're upgrading from the previous version, you'll need to update the database:

```bash
# Update database to add new columns
drush updb -y

# Clear caches
drush cr
```

## Creating a Workflow Tab

To place the workflow field in its own tab, you'll need the **Field Group** module:

### Install Field Group

```bash
composer require drupal/field_group
drush en field_group -y
```

### Configure the Workflow Tab

1. Navigate to **Structure > Content types > [Your Type] > Manage form display**
   - Example: `/admin/structure/types/manage/article/form-display`

2. Click **Add group** (at the bottom)

3. Configure the group:
   - **Label**: "Workflow" (or your preferred name)
   - **Format**: Select "Horizontal tabs" or "Tab" (for vertical tabs)
   - Click **Create group**

4. **Drag your workflow field** into the new "Workflow" group

5. Configure group settings:
   - Click the gear icon on the Workflow group
   - Adjust settings as needed (description, required, etc.)
   - Save settings

6. **Save** the form display

Now your workflow assignments will appear in their own tab when editing content!

## Usage

### Adding the Field to a Content Type

1. Navigate to **Structure > Content types > [Your Type] > Manage fields**

2. Click **Add field**

3. Under "Add a new field":
   - **Field type**: Select "Workflow Assignment"
   - **Label**: Enter a label (e.g., "Workflow Assignments")

4. Configure field settings:
   - **Allowed number of values**: Unlimited (recommended)

5. Save the field

### Assigning Workflow Tasks

When editing content with the workflow assignment field:

1. **For each assignment:**
   - **Title**: Enter a descriptive title (e.g., "Initial Review", "Final Approval")
   - **Type**: Select User or Group
   - **Assignee**: Use autocomplete to find the user or group
   - **Comment**: Add any notes or instructions (optional)

2. Click **Add another item** to add more assignments

3. **Save** the content

### Example Workflow Assignment

```
Title: Content Review
Type: User
Assignee: editor_jane
Comment: Please review for grammar and style

Title: Technical Review  
Type: User
Assignee: tech_lead_bob
Comment: Check all code snippets and technical accuracy

Title: Final Approval
Type: Group
Assignee: Editorial Board
Comment: Ready for publication decision
```

## Field Display Configuration

### Standard Display

The default formatter displays assignments in a clean table-like format with headers:

| Title | Type | Assignee | Comment |
|-------|------|----------|---------|
| ... | ... | ... | ... |

### Customize Display

1. Navigate to **Structure > Content types > [Your Type] > Manage display**

2. Find your workflow assignment field

3. Click the gear icon to configure formatter settings

4. Save changes

## API Usage

### Getting Assignments Programmatically

```php
// Load a node
$node = \Drupal\node\Entity\Node::load(123);

// Get all assignments
if ($node->hasField('field_workflow_assignment')) {
  $assignments = $node->get('field_workflow_assignment');
  
  foreach ($assignments as $assignment) {
    $title = $assignment->title;
    $type = $assignment->target_type;
    $id = $assignment->target_id;
    $comment = $assignment->comment;
    $entity = $assignment->getEntity();
    
    if ($entity) {
      $name = $entity->label();
      echo "[$title] {$type}: {$name} - {$comment}\n";
    }
  }
}
```

### Adding Assignments Programmatically

```php
use Drupal\node\Entity\Node;

$node = Node::load(123);

// Add assignment with title and comment
$node->field_workflow_assignment[] = [
  'title' => 'Initial Review',
  'target_type' => 'user',
  'target_id' => 5,
  'comment' => 'Please review by end of week',
];

$node->field_workflow_assignment[] = [
  'title' => 'Technical Approval',
  'target_type' => 'group',
  'target_id' => 3,
  'comment' => 'Final technical sign-off required',
];

$node->save();
```

### Querying by Assignment Title

```php
// Find all nodes with "Final Approval" assignments
$query = \Drupal::entityQuery('node')
  ->condition('field_workflow_assignment.title', 'Final Approval', 'CONTAINS')
  ->accessCheck(TRUE);

$nids = $query->execute();
```

## Database Structure

### Updated Field Table

```sql
CREATE TABLE node__field_workflow_assignment (
  entity_id INT,
  revision_id INT,
  delta INT,
  field_workflow_assignment_title VARCHAR(255),      -- NEW
  field_workflow_assignment_target_type VARCHAR(32),
  field_workflow_assignment_target_id INT,
  field_workflow_assignment_comment TEXT,            -- NEW
  ...
);
```

### Example Data

```
entity_id=123, delta=0:
  title='Content Review'
  target_type='user'
  target_id=5
  comment='Check grammar and style'

entity_id=123, delta=1:
  title='Technical Review'
  target_type='group'
  target_id=3
  comment='Verify all technical details'
```

## Styling and Theming

### Override Template

Template file: `templates/dworkflow-assignment-list.html.twig`

Available variables:
```twig
assignments: [
  {
    title: 'Assignment title',
    type: 'user' or 'group',
    entity: Entity object,
    label: Entity label,
    url: Entity URL,
    comment: 'Assignee comment'
  }
]
```

### CSS Classes

The module provides extensive CSS classes for styling:

```css
/* Main containers */
.dworkflow-assignments { }
.dworkflow-assignment-list { }

/* Individual assignment rows */
.dworkflow-assignment-item { }
.dworkflow-assignment-title { }
.dworkflow-assignment-type { }
.dworkflow-assignment-assignee { }
.dworkflow-assignment-comment { }

/* Type badges */
.dworkflow-badge { }
.dworkflow-badge--user { }
.dworkflow-badge--group { }

/* Form widgets */
.dworkflow-assignment-row { }
.dworkflow-title-field { }
.dworkflow-type-field { }
.dworkflow-assignee-field { }
.dworkflow-comment-field { }
```

### Responsive Design

The layout automatically adapts to mobile devices, stacking fields vertically on smaller screens.

## Use Cases

### 1. Editorial Workflow
```
Title: Copy Edit → Type: User → Assignee: copy_editor → Comment: Check AP style
Title: Fact Check → Type: User → Assignee: fact_checker → Comment: Verify all sources
Title: Final Review → Type: Group → Assignee: Editors → Comment: Ready for publication
```

### 2. Project Management
```
Title: Development → Type: User → Assignee: developer_alice → Comment: Backend API
Title: Frontend → Type: User → Assignee: developer_bob → Comment: React components
Title: QA Testing → Type: Group → Assignee: QA Team → Comment: Full regression test
```

### 3. Document Approval
```
Title: Legal Review → Type: User → Assignee: legal_counsel → Comment: Contract terms
Title: Finance Approval → Type: User → Assignee: cfo → Comment: Budget approval
Title: CEO Sign-off → Type: User → Assignee: ceo → Comment: Final authorization
```

### 4. Event Planning
```
Title: Venue Booking → Type: User → Assignee: coordinator → Comment: Confirm by March 1
Title: Catering → Type: Group → Assignee: Catering Team → Comment: 150 guests
Title: Marketing → Type: Group → Assignee: Marketing → Comment: Social media campaign
```

## Migration from Previous Version

If you're upgrading from the previous version without title and comment fields:

### Step 1: Backup Your Database
```bash
drush sql:dump > backup.sql
```

### Step 2: Run Update
```bash
drush updb -y
```

### Step 3: Existing Data
- Existing assignments will remain intact
- Title and comment fields will be empty
- You can edit existing content to add titles and comments

## Troubleshooting

### Field doesn't update after upgrade
```bash
drush cr
drush entity:updates
drush updb -y
```

### Tabs don't appear
- Install Field Group module: `composer require drupal/field_group`
- Configure tab groups in form display settings

### Layout issues
- Clear caches: `drush cr`
- Check theme compatibility
- Review browser console for CSS conflicts

### Empty titles or comments display oddly
The CSS includes placeholder styling (—) for empty values. Override in your theme if needed.

## Performance Considerations

### For Large Numbers of Assignments
- The field uses database indexes on target_type and target_id
- Consider limiting the number of values if you have thousands of assignments
- Use Views for complex queries across multiple nodes

### Caching
- Field values are cached with the parent entity
- Clear cache after programmatic updates

## Permissions

Standard Drupal field permissions apply:
- **Edit own [field]**: Users can edit assignments on their own content
- **Edit any [field]**: Users can edit assignments on any content  
- **View [field]**: Users can view assignments

Configure via **People > Permissions** or Field Permissions module.

## Support and Contributing

### Documentation
- [Drupal Field API](https://www.drupal.org/docs/drupal-apis/entity-api/fieldtypes-fieldwidgets-and-fieldformatters)
- [Field Group Module](https://www.drupal.org/project/field_group)

### Known Issues
- None currently reported

### Feature Requests
Submit via your project's issue tracker

## License

GPL-2.0-or-later

## Credits

Enhanced workflow management for Drupal 10/11 with title and comment support.
