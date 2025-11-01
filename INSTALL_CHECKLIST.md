# Installation Checklist

Use this checklist to ensure successful installation and configuration of the DWorkflow module.

## Pre-Installation

- [ ] **Drupal version check**: Confirm you're running Drupal 10.x or 11.x
- [ ] **Backup database**: `drush sql:dump > backup-$(date +%Y%m%d).sql`
- [ ] **Backup files**: Copy existing dworkflow module (if updating)
- [ ] **Check permissions**: Ensure you have admin permissions

## Installation Steps

### For New Installations

- [ ] Copy module to `/modules/custom/dworkflow/`
- [ ] Enable module: `drush en dworkflow -y`
- [ ] Clear caches: `drush cr`
- [ ] Verify module is enabled: `drush pml | grep dworkflow`

### For Existing Installations (Updates)

- [ ] Backup database (see above)
- [ ] Deploy updated module files
- [ ] Run database updates: `drush updb -y`
- [ ] Clear all caches: `drush cr`
- [ ] Verify field schema: `drush entity:updates`

## Field Configuration

- [ ] Navigate to: Structure > Content types > [Your Type] > Manage fields
- [ ] Click "Add field"
- [ ] Select field type: "Workflow Assignment"
- [ ] Enter field label (e.g., "Workflow Assignments")
- [ ] Set cardinality to "Unlimited" (recommended)
- [ ] Save field settings
- [ ] Configure form display (optional)
- [ ] Configure display settings (optional)
- [ ] Save configuration

## Tab Setup (Optional but Recommended)

- [ ] Install Field Group: `composer require drupal/field_group`
- [ ] Enable Field Group: `drush en field_group -y`
- [ ] Go to: Structure > Content types > [Type] > Manage form display
- [ ] Click "Add group" at bottom
- [ ] Label: "Workflow", Format: "Tabs"
- [ ] Click "Create group"
- [ ] Drag workflow field into Workflow group
- [ ] Configure group settings (optional)
- [ ] Save form display

## Verification Tests

### Basic Functionality

- [ ] Create or edit content with workflow field
- [ ] Can add assignment with title
- [ ] Can add assignment with comment
- [ ] Type selector shows "User" and "Group" (if Group module enabled)
- [ ] Assignee autocomplete works
- [ ] Can add multiple assignments
- [ ] Can save content successfully

### Display Tests

- [ ] View published content
- [ ] Assignments display in table format
- [ ] Headers show: Title | Type | Assignee | Comment
- [ ] All fields visible on same row
- [ ] Type badges show correct colors (USER=blue, GROUP=green)
- [ ] Assignee links work
- [ ] Empty titles/comments show placeholder (—)

### Tab Tests (if configured)

- [ ] Workflow tab appears in edit form
- [ ] Can switch between tabs
- [ ] Workflow fields work within tab
- [ ] Tab persists after saving

### Responsive Tests

- [ ] Test on desktop browser
- [ ] Test on mobile device (or browser dev tools)
- [ ] Layout stacks properly on mobile
- [ ] All fields remain accessible

### Update Tests (for existing installations)

- [ ] Existing assignments still display
- [ ] Existing data preserved (users/groups intact)
- [ ] Can edit existing assignments
- [ ] Can add title to existing assignments
- [ ] Can add comment to existing assignments
- [ ] No data loss occurred

## Post-Installation

### Documentation Review

- [ ] Read README.md for full documentation
- [ ] Review TABS_SETUP.md for tab options
- [ ] Check UPDATE.md for migration details
- [ ] See VISUAL_EXAMPLES.md for layout reference
- [ ] Review IMPLEMENTATION_SUMMARY.md for changes

### User Training

- [ ] Inform content editors about new features
- [ ] Explain title field purpose
- [ ] Explain comment field usage
- [ ] Demonstrate tab navigation (if applicable)
- [ ] Provide examples of good assignments

### Customization (Optional)

- [ ] Override template if needed
- [ ] Add custom CSS styling
- [ ] Configure field permissions
- [ ] Set up Views for workflow dashboards
- [ ] Configure notifications (if desired)

## Troubleshooting

If you encounter issues:

### Module Won't Enable

- [ ] Check Drupal version compatibility
- [ ] Verify file permissions
- [ ] Check error logs: `drush watchdog:show`
- [ ] Clear all caches: `drush cr`

### Fields Don't Appear

- [ ] Verify module is enabled
- [ ] Clear caches: `drush cr`
- [ ] Check field was added to content type
- [ ] Verify user has permission to edit field

### Update Hook Fails

- [ ] Check database credentials
- [ ] Verify backup exists
- [ ] Review error message
- [ ] Try manual column addition (see UPDATE.md)
- [ ] Contact support with error details

### AJAX Doesn't Work

- [ ] Clear all caches: `drush cr`
- [ ] Check browser console for errors
- [ ] Verify jQuery is loaded
- [ ] Test in different browser
- [ ] Disable conflicting modules temporarily

### Tab Doesn't Appear

- [ ] Verify Field Group module is enabled
- [ ] Check group was created correctly
- [ ] Ensure field is dragged into group
- [ ] Save form display settings
- [ ] Clear caches

### Styling Issues

- [ ] Clear CSS/JS aggregation caches
- [ ] Check browser dev tools for CSS conflicts
- [ ] Verify library is loading: `dworkflow/dworkflow`
- [ ] Override template if needed
- [ ] Add custom CSS to theme

## Success Criteria

Installation is successful when:

- [x] Module enabled without errors
- [x] Field can be added to content types
- [x] Assignments can be created with title and comment
- [x] Display shows all fields in row format
- [x] AJAX type selector works
- [x] Entity autocomplete works
- [x] Data persists correctly
- [x] (Optional) Tabs display properly
- [x] No console errors
- [x] Responsive layout works

## Rollback Plan (If Needed)

If you need to rollback:

1. **Restore database backup**:
   ```bash
   drush sql:drop -y
   drush sqlc < backup-YYYYMMDD.sql
   ```

2. **Restore old module files**:
   ```bash
   cp -r dworkflow-backup /path/to/modules/custom/dworkflow
   ```

3. **Clear caches**:
   ```bash
   drush cr
   ```

## Getting Help

If you need assistance:

1. Check documentation in this package
2. Review Drupal.org Field API documentation
3. Check module error logs
4. Search Drupal Stack Exchange
5. Review browser console errors

## Completion

Once all checklist items are complete:

- [ ] Mark installation as successful
- [ ] Document any customizations made
- [ ] Update internal documentation
- [ ] Train content team
- [ ] Monitor for issues in first week

---

**Installation Date**: _____________

**Installed By**: _____________

**Drupal Version**: _____________

**Notes**: 
_____________________________________________
_____________________________________________
_____________________________________________

---

Congratulations! Your DWorkflow module is ready to use. 🎉
