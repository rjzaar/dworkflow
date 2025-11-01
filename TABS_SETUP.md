# Quick Start: Setting Up Workflow in a Separate Tab

This guide will help you quickly set up your DWorkflow field in a separate tab on your content type.

## Prerequisites

- DWorkflow module installed and enabled
- Workflow Assignment field added to your content type

## Step 1: Install Field Group Module

```bash
composer require drupal/field_group
drush en field_group -y
drush cr
```

## Step 2: Create a Workflow Tab

1. **Navigate to Form Display**
   - Go to: `Structure > Content types > [Your Content Type] > Manage form display`
   - Example: `/admin/structure/types/manage/article/form-display`

2. **Add a New Group**
   - Scroll to the bottom of the page
   - Click **"Add group"** button

3. **Configure the Group**
   - **Label**: Enter "Workflow" (or your preferred name)
   - **Format**: Select one of:
     - **"Tabs"** - For horizontal tabs (recommended)
     - **"Vertical tabs"** - For sidebar tabs
     - **"Accordion"** - For collapsible sections
   - Click **"Create group"**

4. **Move Field into Tab**
   - Find your workflow assignment field in the field list
   - **Drag and drop** it into the "Workflow" group you just created
   - The field should now be indented under the Workflow group

5. **Configure Tab Settings (Optional)**
   - Click the **gear icon** next to the Workflow group
   - Available settings:
     - **Description**: Add helper text
     - **Required**: Make tab required
     - **Classes**: Add custom CSS classes
     - **Display**: Control visibility
   - Click **"Update"**

6. **Save Form Display**
   - Click **"Save"** at the bottom of the page

## Step 3: Verify

1. **Edit or Create Content**
   - Go to edit an existing node or create new content
   - You should see a **"Workflow"** tab

2. **Add Workflow Assignments**
   - Click the Workflow tab
   - Add assignments with:
     - Title (e.g., "Content Review")
     - Type (User or Group)
     - Assignee (search and select)
     - Comment (optional notes)

## Alternative: Configure Display Tab

You can also create a tab for the **view display** (not just the edit form):

1. Go to: `Structure > Content types > [Type] > Manage display`
2. Follow the same steps as above
3. This creates a tab when viewing published content

## Example Tab Configuration

### For Article Content Type

**Content structure with tabs:**
```
┌─ Content ─────────────┐
│ Title                 │
│ Body                  │
│ Tags                  │
└───────────────────────┘

┌─ Workflow ────────────┐
│ Title    | Type | Assignee    | Comment          │
│ Review   | User | editor_jane | Check grammar    │
│ Approve  | User | manager     | Final sign-off   │
└───────────────────────┘

┌─ Publishing ──────────┐
│ Published             │
│ Promoted to front     │
└───────────────────────┘
```

## Multiple Field Groups

You can create multiple groups/tabs:

```yaml
Tabs:
  ├─ Content (default tab)
  │  └─ Body, Title, Tags
  ├─ Workflow
  │  └─ Workflow Assignments
  ├─ Metadata
  │  └─ Author, Created Date
  └─ Publishing Options
     └─ Published, Sticky, Promoted
```

## Using Vertical Tabs

For a sidebar layout, use **Vertical tabs**:

1. When creating the group, select **"Vertical tabs"** as format
2. The tabs will appear on the left side
3. Good for forms with many sections

## Using Accordion

For collapsible sections without tabs:

1. Select **"Accordion"** as format
2. Each section can be expanded/collapsed
3. Good for optional sections

## Advanced: Nested Groups

You can nest groups within groups:

1. Create a parent group (e.g., "Tabs")
2. Create child groups (e.g., "Workflow", "Publishing")
3. Drag child groups into parent group
4. Drag fields into child groups

## Permissions

The Field Group module respects field permissions:
- If users can't edit the workflow field, they won't see the tab
- If users can only view, the tab appears as read-only

## Styling

### Custom CSS

Add custom styling to your theme:

```css
/* Workflow tab styling */
.field-group-tabs-wrapper .horizontal-tab-button-workflow {
  background-color: #007bff;
  color: white;
}

/* Active workflow tab */
.field-group-tabs-wrapper .horizontal-tab-button-workflow.is-selected {
  background-color: #0056b3;
}
```

### Tab Icons

You can add icons using CSS:

```css
.horizontal-tab-button-workflow::before {
  content: "📋 ";
  margin-right: 0.5em;
}
```

## Troubleshooting

### Tab doesn't appear
- Clear caches: `drush cr`
- Verify Field Group module is enabled
- Check field permissions

### Field not showing in tab
- Ensure field is dragged into the group
- Check field is not disabled
- Verify field has proper permissions

### Multiple tabs not working
- Make sure all groups use the same format (all "Tabs" or all "Vertical tabs")
- Groups should be at the same level (not nested)

## Common Configurations

### Editorial Workflow Setup
```
Tabs:
├─ Content (Article content)
├─ Workflow (Assignments with titles and comments)
├─ SEO (Meta tags, descriptions)
└─ Publishing (Publication settings)
```

### Project Management Setup
```
Tabs:
├─ Details (Project information)
├─ Team (Workflow assignments)
├─ Timeline (Dates and milestones)
└─ Documents (File attachments)
```

### Document Approval Setup
```
Vertical Tabs:
├─ Document (Main content)
├─ Review Chain (Workflow assignments)
├─ Attachments (Supporting files)
└─ History (Revision log)
```

## Next Steps

- Configure field display for viewing content
- Set up Views to display workflow assignments
- Create custom workflows with Rules or Workbench modules
- Add conditional logic with Conditional Fields module

## Additional Resources

- [Field Group Documentation](https://www.drupal.org/docs/contributed-modules/field-group)
- [Creating Better Forms](https://www.drupal.org/docs/user_guide/en/structure-form-editing.html)
- DWorkflow README.md for full module documentation
