# Workflow Assignment for Open Social

This guide covers using the Workflow Assignment module specifically with Open Social distribution.

## Open Social Compatibility

✅ **Fully Compatible** with Open Social 12.x and 13.x (Drupal 10)

The module is designed from the ground up to work perfectly with Open Social's architecture:
- Native Group module integration
- Pre-configured for Open Social content types (topic, event, book)
- Respects Open Social's permission system
- Works within group contexts

## Why Use This with Open Social?

Open Social is built for communities, and this module extends it by providing:

1. **Project-Based Workflows** - Organize community projects with assigned teams
2. **Resource Management** - Tag and track where project resources are located
3. **Dynamic Teams** - Add/remove users and groups as projects evolve
4. **Transparency** - Everyone can see who's working on what and where resources are

## Installation for Open Social

### Method 1: Custom Module Directory

```bash
# Navigate to your Open Social root
cd /path/to/your/opensocial

# Copy module to custom modules
cp -r workflow_assignment web/modules/custom/

# Enable the module
drush en workflow_assignment -y
drush cr
```

### Method 2: Profile Modules (for distributions)

```bash
# Copy to Open Social profile custom modules
cp -r workflow_assignment web/profiles/contrib/social/modules/custom/
drush en workflow_assignment -y
drush cr
```

## Configuration for Open Social

### 1. Initial Setup

Go to **Configuration > Workflow > Workflow Assignment**

**Recommended Content Types for Open Social:**
- ✅ Topic (discussions and content)
- ✅ Event (community events)
- ✅ Book (documentation)
- ✅ Page (if using standard pages)

**Resource Vocabulary:**
- Keep default: `resource_locations`

### 2. Create Resource Location Tags

Navigate to **Structure > Taxonomy > Resource Locations**

**Example tags for Open Social communities:**
- Google Drive - Community Resources
- Shared Calendar
- Trello - Projects Board
- Slack Channel - #project-name
- GitHub Repository
- Video Archive
- Photo Gallery
- Community Wiki

### 3. Understanding Groups in Workflows

Open Social uses the Group module extensively. When you assign a **Group** to a workflow:
- All group members effectively have access to the workflow
- Respects group membership and roles
- Perfect for team-based projects

## Use Cases for Open Social

### Use Case 1: Community Event Planning

```
Workflow: "Summer Festival 2025"

Assigned Groups:
  - Event Planning Committee
  - Volunteers Group
  - Marketing Team

Resource Locations:
  - Google Drive - Summer Festival Folder
  - Shared Calendar - Festival Schedule
  - Trello - Festival Tasks

Assigned To:
  - Event: "Summer Festival Registration"
  - Topic: "Volunteer Coordination"
  - Topic: "Festival Budget Discussion"
  - Book: "Festival Planning Guide"
```

**Workflow:** As new volunteers join, add them to the Volunteers Group, and they automatically see workflow assignments.

### Use Case 2: Working Groups & Committees

```
Workflow: "Policy Review Q1 2025"

Assigned Users:
  - committee_chair
  - policy_advisor
  - community_manager

Assigned Groups:
  - Governance Committee

Resource Locations:
  - Google Docs - Policy Drafts
  - Confluence - Policy Wiki

Assigned To:
  - Topic: "Member Feedback on Policy Draft"
  - Book: "Policy Revision History"
  - Event: "Policy Review Meeting"
```

### Use Case 3: Project Collaboration

```
Workflow: "Website Redesign Project"

Assigned Groups:
  - Web Team
  - Design Group
  - Content Contributors

Resource Locations:
  - GitHub - /community-site
  - Figma - Design Files
  - Google Drive - Content Assets

Assigned To:
  - Topic: "Design Discussion"
  - Topic: "Technical Requirements"
  - Event: "Design Review Workshop"
```

### Use Case 4: Knowledge Base Management

```
Workflow: "Documentation Sprint"

Assigned Users:
  - documentation_lead
  - technical_writer

Assigned Groups:
  - Documentation Team
  - Subject Matter Experts

Resource Locations:
  - Confluence Space
  - GitHub Wiki
  - Video Tutorials Folder

Assigned To:
  - Book: "User Guide"
  - Book: "Admin Documentation"
  - Topic: "Documentation Feedback"
```

## Best Practices for Open Social

### 1. Leverage Groups Effectively

**Do:**
- Assign workflows to groups when possible for easier management
- Use group visibility settings in conjunction with workflows
- Create dedicated groups for major projects

**Don't:**
- Assign individual users when a group would work better
- Forget that group membership changes affect workflow access

### 2. Resource Location Strategy

**Organize by:**
- Project/campaign
- Resource type (docs, media, code)
- Access level (public, members-only)

**Example Structure:**
```
Resource Locations Taxonomy:
├── Google Drive
│   ├── Public Resources
│   ├── Member Resources
│   └── Committee Resources
├── GitHub
│   ├── Community Website
│   ├── Documentation
│   └── Tools & Scripts
├── Communication
│   ├── Slack Channels
│   ├── Mailing Lists
│   └── Video Conferencing
└── Planning
    ├── Trello Boards
    ├── Shared Calendars
    └── Meeting Notes
```

### 3. Workflow Naming Conventions

Use clear, descriptive names:
- Include timeframe: "Q1 2025 Marketing"
- Include project type: "Event Planning - Summer Festival"
- Include team: "Web Team - Site Redesign"

### 4. On-the-Fly Updates

**Quick Edit is perfect for:**
- Adding new team members mid-project
- Updating resource locations
- Adjusting group assignments
- Rapid team restructuring

**When to use Quick Edit:**
- During meetings for immediate updates
- When onboarding new members
- When resources move or change
- For emergency team adjustments

## Permissions in Open Social Context

### Recommended Permission Setup

**Community Managers / Site Admins:**
- ✅ Administer workflow lists
- ✅ Assign workflow lists to content
- ✅ View workflow list assignments

**Group Managers / Project Leads:**
- ✅ Assign workflow lists to content
- ✅ View workflow list assignments

**Regular Members:**
- ✅ View workflow list assignments (if appropriate)

**Configuration:**
Go to **Configuration > People > Permissions** and set accordingly.

## Integration with Open Social Features

### With Groups

The module automatically integrates with Open Social groups:
- Detects Group module presence
- Shows all groups in workflow assignment forms
- Respects group membership for access
- Works with group content

### With Content Types

**Topics** - Perfect for assigning discussion workflows
- Project discussions
- Brainstorming sessions
- Feedback collection

**Events** - Ideal for event planning workflows
- Event committees
- Volunteer coordination
- Resource tracking

**Books** - Great for documentation workflows
- Collaborative writing
- Knowledge base projects
- Guide creation

### With Social Features

**Activity Streams:**
- Workflow assignments appear in activity
- Changes to workflows can be tracked
- Team members see updates

**Notifications:**
- Members get notified about workflow content
- Updates to assigned workflows visible in feeds

## Workflow for Community Managers

### Step-by-Step: Setting Up a Community Project

1. **Create the Workflow**
   - Go to Structure > Workflow Lists
   - Create "Community Newsletter Q1"
   - Add description and details

2. **Assign the Team**
   - Add individual editors
   - Add "Newsletter Team" group
   - Add "Content Contributors" group

3. **Tag Resources**
   - Google Drive - Newsletter Folder
   - Mailchimp - Newsletter Campaigns
   - Canva - Design Templates

4. **Assign to Content**
   - Create topics for newsletter discussions
   - Create events for planning meetings
   - Assign workflow to all related content

5. **Manage On-the-Fly**
   - Use Quick Edit to add/remove contributors
   - Update resource locations as needed
   - Adjust team as project evolves

### Monitoring Workflows

**View all workflows:**
- Structure > Workflow Lists
- See users, groups, and resource counts
- Quick access to edit or quick edit

**View content assignments:**
- Visit any content with workflow
- Check "Workflow Information" section
- See current team and resources

## Advanced Usage

### Multi-Workflow Strategy

Some content might benefit from multiple workflows:

**Example: Major Campaign**
- Assign "Marketing Campaign Q1" workflow to main content
- Assign "Event Planning" workflow to related event
- Assign "Content Creation" workflow to supporting topics

This gives different teams access to different content while maintaining organization.

### Seasonal Workflows

Create reusable workflow templates:
- "Q1 Marketing" / "Q2 Marketing" etc.
- "Summer Events" / "Winter Events"
- "Monthly Newsletter - January" pattern

Clone and adjust as needed.

## Troubleshooting Open Social Specific Issues

### Groups Not Showing in Workflow Form

1. Verify Group module is enabled
2. Check that groups exist (People > Groups)
3. Clear caches: `drush cr`
4. Verify you have permission to see groups

### Workflow Not Visible to Group Members

1. Check content is actually in the group
2. Verify group members have "view workflow lists" permission
3. Ensure workflow is assigned to content
4. Check group visibility settings

### Performance with Large Communities

For communities with 1000+ users:
- Use groups instead of individual user assignments
- Limit workflow list count where possible
- Consider using views to filter content by workflow
- Cache appropriately

## Migration from Other Systems

If migrating from another workflow or project system:

1. **Export your data** (users, groups, resource locations)
2. **Create taxonomy terms** for all resource locations
3. **Create workflow lists** matching your projects
4. **Assign en masse** using batch operations or custom scripts
5. **Notify community** about new system

## Community Engagement

**Announce workflows to your community:**
- Create a topic explaining the new system
- Post in relevant groups
- Add to community documentation
- Train group managers and project leads

**Encourage adoption:**
- Start with one pilot project
- Get feedback from users
- Iterate and improve
- Scale to other projects

## Support Resources

- **Open Social Documentation:** https://www.drupal.org/docs/getting-started/drupal-distributions/distribution-documentation/open-social
- **Group Module:** https://www.drupal.org/project/group
- **Community Support:** Open Social forums and issue queue

## Summary

The Workflow Assignment module adds powerful project and resource management capabilities to Open Social, enabling:
- ✅ Dynamic team assignments
- ✅ Resource location tracking
- ✅ On-the-fly workflow modifications
- ✅ Perfect integration with groups
- ✅ Enhanced community collaboration

Perfect for managing community projects, events, documentation efforts, and any collaborative work within your Open Social site!
