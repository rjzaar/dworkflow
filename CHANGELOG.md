# Changelog

All notable changes to the DWorkflow module will be documented in this file.

## [1.0.0] - 2025-10-30

### Added
- Initial release of DWorkflow module
- Config entity type for Workflow Lists
- Proper entity reference fields for assigning users and/or groups
- Multi-value form widget with type selector for each entry
- AJAX-powered add/remove buttons for assignments
- Support for assigning both users AND groups with explicit type selection
- Resource location tagging using taxonomy
- Quick Edit form for rapid workflow modification
- Node assignment form with dedicated tab
- Workflow information display on content pages
- Settings form for enabling content types and selecting vocabulary
- Automatic field creation for enabled content types
- Visual badges for user/group distinction in displays
- Complete API with helper methods for programmatic access
- Install hook to create default vocabulary and example terms
- Uninstall hook to clean up fields (preserves vocabulary)
- CSS styling for workflow information display
- Twig template for workflow information rendering
- Three permission levels: administer, assign, view
- Full Open Social/Group module integration
- Comprehensive documentation and examples

### Technical Features
- Uses proper Drupal core entity reference with custom widget
- No contrib module dependencies
- Config entity storage for exportability
- Created/changed timestamps on all workflows
- Field storage and field config management
- Custom list builder with operations
- Entity autocomplete widgets for each type
- AJAX form callbacks for dynamic entry management
- Backward compatible helper methods
- Clean uninstall process

### Implementation Approach
- Multi-value widget with type selector per entry
- Proper entity reference autocomplete for each type
- AJAX add/remove functionality
- Explicit type storage in configuration
- Native Drupal form API
- No external dependencies beyond core
