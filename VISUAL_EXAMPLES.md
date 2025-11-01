# DWorkflow - Visual Examples

This document provides visual examples of how the updated DWorkflow module looks and behaves.

## Form Widget (Edit Mode)

When editing content, each workflow assignment appears as a row with all fields:

```
┌────────────────────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                                        │
├────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│ ┌──────────────┬────────┬───────────────┬────────────────────────────────┐ │
│ │ Title        │ Type ▼ │ Assignee      │ Comment                        │ │
│ ├──────────────┼────────┼───────────────┼────────────────────────────────┤ │
│ │ Initial      │ User ▼ │ editor_jane ⌄ │ Review grammar and style       │ │
│ │ Review       │        │               │                                │ │
│ └──────────────┴────────┴───────────────┴────────────────────────────────┘ │
│                                                                              │
│ ┌──────────────┬────────┬───────────────┬────────────────────────────────┐ │
│ │ Technical    │ User ▼ │ tech_lead ⌄   │ Verify all code examples       │ │
│ │ Review       │        │               │                                │ │
│ └──────────────┴────────┴───────────────┴────────────────────────────────┘ │
│                                                                              │
│ ┌──────────────┬────────┬───────────────┬────────────────────────────────┐ │
│ │ Final        │ Group ▼│ Editorial  ⌄  │ Ready for publication decision │ │
│ │ Approval     │        │ Board         │                                │ │
│ └──────────────┴────────┴───────────────┴────────────────────────────────┘ │
│                                                                              │
│ [+ Add another item]                                                         │
└────────────────────────────────────────────────────────────────────────────┘
```

## Display View (View Mode)

When viewing published content, assignments display in a clean table format:

```
┌────────────────────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                                        │
├────────────────────────────────────────────────────────────────────────────┤
│ Title          │ Type   │ Assignee           │ Comment                     │
├────────────────┼────────┼────────────────────┼─────────────────────────────┤
│ Initial Review │ [USER] │ Jane Smith         │ Review grammar and style    │
├────────────────┼────────┼────────────────────┼─────────────────────────────┤
│ Technical      │ [USER] │ Bob Johnson        │ Verify all code examples    │
│ Review         │        │                    │                             │
├────────────────┼────────┼────────────────────┼─────────────────────────────┤
│ Final Approval │ [GROUP]│ Editorial Board    │ Ready for publication       │
│                │        │                    │ decision                    │
└────────────────┴────────┴────────────────────┴─────────────────────────────┘
```

## With Field Group Tabs (Edit Mode)

When using Field Group module with tabs:

```
┌────────────────────────────────────────────────────────────────────────────┐
│  Article Title: "How to Use DWorkflow"                                     │
├────────────────────────────────────────────────────────────────────────────┤
│  [Content] [Workflow] [Publishing] [SEO]                                   │
│   ─────────────────                                                         │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐  │
│  │ WORKFLOW TAB                                                          │  │
│  ├─────────────────────────────────────────────────────────────────────┤  │
│  │                                                                       │  │
│  │ Workflow Assignments:                                                 │  │
│  │                                                                       │  │
│  │ Title          Type   Assignee         Comment                       │  │
│  │ ──────────────────────────────────────────────────────────────────── │  │
│  │ [Text input]   [▼]    [Autocomplete]   [Text input................] │  │
│  │                                                                       │  │
│  │ [Text input]   [▼]    [Autocomplete]   [Text input................] │  │
│  │                                                                       │  │
│  │ [+ Add another item]                                                  │  │
│  │                                                                       │  │
│  └─────────────────────────────────────────────────────────────────────┘  │
│                                                                             │
│  [Save] [Preview] [Delete]                                                 │
└────────────────────────────────────────────────────────────────────────────┘
```

## Example Use Cases

### Editorial Workflow Example

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                                      │
├──────────────────────────────────────────────────────────────────────────┤
│ Title            │ Type   │ Assignee      │ Comment                      │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Copy Edit        │ [USER] │ Sarah Parker  │ Check AP style guide         │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Fact Check       │ [USER] │ Mike Wilson   │ Verify all sources           │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Legal Review     │ [USER] │ Jennifer Lee  │ Check for legal issues       │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Senior Review    │ [GROUP]│ Senior Staff  │ Final editorial approval     │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Publishing       │ [USER] │ Tom Anderson  │ Schedule for Monday 9am      │
└──────────────────┴────────┴───────────────┴──────────────────────────────┘
```

### Project Management Example

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                                      │
├──────────────────────────────────────────────────────────────────────────┤
│ Title            │ Type   │ Assignee      │ Comment                      │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Backend Dev      │ [USER] │ Alice Chen    │ API endpoints & database     │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Frontend Dev     │ [USER] │ Bob Smith     │ React components             │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ UI/UX Design     │ [USER] │ Carol Kim     │ Wireframes by Friday         │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ QA Testing       │ [GROUP]│ QA Team       │ Full regression suite        │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ DevOps Deploy    │ [GROUP]│ DevOps Team   │ Production deployment        │
└──────────────────┴────────┴───────────────┴──────────────────────────────┘
```

### Document Approval Example

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                                      │
├──────────────────────────────────────────────────────────────────────────┤
│ Title            │ Type   │ Assignee      │ Comment                      │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Legal Review     │ [USER] │ Legal Counsel │ Contract terms & conditions  │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Finance Review   │ [USER] │ CFO           │ Budget approval needed       │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ HR Review        │ [USER] │ HR Director   │ Employee policy compliance   │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Executive        │ [USER] │ CEO           │ Final authorization required │
│ Approval         │        │               │                              │
└──────────────────┴────────┴───────────────┴──────────────────────────────┘
```

## Mobile Responsive View

On mobile devices, the layout stacks vertically:

```
┌───────────────────────────────┐
│ Workflow Assignments          │
├───────────────────────────────┤
│                               │
│ ┌───────────────────────────┐ │
│ │ Initial Review            │ │
│ │ ───────────────────────── │ │
│ │ Type: [USER]              │ │
│ │ Assignee: Jane Smith      │ │
│ │ Comment: Review grammar   │ │
│ │          and style        │ │
│ └───────────────────────────┘ │
│                               │
│ ┌───────────────────────────┐ │
│ │ Technical Review          │ │
│ │ ───────────────────────── │ │
│ │ Type: [USER]              │ │
│ │ Assignee: Bob Johnson     │ │
│ │ Comment: Verify code      │ │
│ └───────────────────────────┘ │
│                               │
│ ┌───────────────────────────┐ │
│ │ Final Approval            │ │
│ │ ───────────────────────── │ │
│ │ Type: [GROUP]             │ │
│ │ Assignee: Editorial Board │ │
│ │ Comment: Publication OK   │ │
│ └───────────────────────────┘ │
│                               │
└───────────────────────────────┘
```

## Badge Colors

The type badges use distinct colors:

- **USER Badge**: Blue background with white text `[USER]`
- **GROUP Badge**: Green background with white text `[GROUP]`

Example:
```
[USER]  ← Blue (#007bff)
[GROUP] ← Green (#28a745)
```

## Empty States

### No Assignments Yet

```
┌────────────────────────────────────────────────────────────┐
│ Workflow Assignments                                        │
├────────────────────────────────────────────────────────────┤
│ No workflow assignments have been added yet.                │
└────────────────────────────────────────────────────────────┘
```

### Empty Title or Comment

When title or comment is not filled, a placeholder dash (—) appears:

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Title            │ Type   │ Assignee      │ Comment                      │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ —                │ [USER] │ Jane Smith    │ —                            │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ Tech Review      │ [USER] │ Bob Johnson   │ —                            │
├──────────────────┼────────┼───────────────┼──────────────────────────────┤
│ —                │ [GROUP]│ Editorial     │ Ready for publication        │
│                  │        │ Board         │                              │
└──────────────────┴────────┴───────────────┴──────────────────────────────┘
```

## AJAX Type Selector

When the type dropdown changes, the assignee autocomplete updates instantly:

### User Selected
```
Type: [User ▼]
Assignee: [Start typing user name... ⌄]
         ↓ (shows user suggestions)
```

### Group Selected
```
Type: [Group ▼]
Assignee: [Start typing group name... ⌄]
         ↓ (shows group suggestions)
```

## Vertical Tabs Layout

Alternative layout using vertical tabs in sidebar:

```
┌────────┬───────────────────────────────────────────────────┐
│Content │ Article Title: "How to Use DWorkflow"            │
│        │                                                   │
│Workflow│ Body:                                             │
│  ◀     │ [Rich text editor...........................]      │
│        │ [.................................]                │
│Meta    │                                                   │
│        │ Tags: [workflow, drupal, tutorial]               │
│Publish │                                                   │
└────────┴───────────────────────────────────────────────────┘

(When "Workflow" is clicked)

┌────────┬───────────────────────────────────────────────────┐
│Content │ WORKFLOW ASSIGNMENTS                              │
│        │                                                   │
│Workflow│ Title     Type  Assignee    Comment              │
│  ◀     │ ───────────────────────────────────────────────  │
│        │ [........][▼]   [........]  [.................]  │
│Meta    │                                                   │
│        │ [........][▼]   [........]  [.................]  │
│Publish │                                                   │
│        │ [+ Add another item]                             │
└────────┴───────────────────────────────────────────────────┘
```

## Accordion Layout

Collapsible sections without tabs:

```
┌────────────────────────────────────────────────────────────┐
│ ▼ Content ▼                                                 │
│   Title: [...........................................]      │
│   Body: [Rich text editor..........................]       │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ ▶ Workflow ▶                                               │
└────────────────────────────────────────────────────────────┘
         ↓ (Click to expand)
┌────────────────────────────────────────────────────────────┐
│ ▼ Workflow ▼                                               │
│   Workflow Assignments:                                     │
│   Title     Type  Assignee    Comment                      │
│   [........][▼]   [........]  [.................]          │
│   [+ Add another item]                                      │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ ▶ Publishing Options ▶                                     │
└────────────────────────────────────────────────────────────┘
```

## Print View

Optimized for printing:

```
═══════════════════════════════════════════════════════════════
              ARTICLE: How to Use DWorkflow
═══════════════════════════════════════════════════════════════

Workflow Assignments
───────────────────────────────────────────────────────────────
Title            Type    Assignee          Comment
───────────────────────────────────────────────────────────────
Initial Review   USER    Jane Smith        Review grammar and
                                           style

Technical        USER    Bob Johnson       Verify all code
Review                                     examples

Final Approval   GROUP   Editorial Board   Ready for publication
                                           decision
───────────────────────────────────────────────────────────────
```

## Comparison: Before vs After

### BEFORE (Old Version)
```
Assigned Users and Groups:
• [USER] editor_jane
• [USER] tech_lead_bob
• [GROUP] Editorial Board
```

### AFTER (New Version)
```
Workflow Assignments:

Title            Type    Assignee          Comment
──────────────────────────────────────────────────────────────
Initial Review   [USER]  Jane Smith        Review grammar and
                                           style

Technical        [USER]  Bob Johnson       Verify all code
Review                                     examples

Final Approval   [GROUP] Editorial Board   Ready for
                                           publication
```

---

**Key Visual Improvements:**

1. ✅ Table-like structure with clear headers
2. ✅ All information on same row for easy scanning
3. ✅ Visual hierarchy with proper spacing
4. ✅ Color-coded type badges
5. ✅ Responsive design for mobile
6. ✅ Print-friendly layout
7. ✅ Empty state handling
8. ✅ Tab/group integration
