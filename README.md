# moodle-bookingextension_todolist

Booking extension: Todo list

A `mod_booking` subplugin that adds a shared todo list (checklist) to booking options,
e.g. to track preparation tasks for an event.

## Features

- **Per-option checklist**: enable the todo list in the booking option form and enter
  one item per line.
- **Check items on the option view page**: users with the check capability can tick and
  untick items via AJAX; who completed an item and when is stored.
- **Safe editing**: unchanged items keep their checked state when the list is edited or
  reordered. Changed lines are reset. If completed items exist, saving a changed list
  requires an explicit confirmation.
- **Booking rules**: the events `todolist_item_checked`, `todolist_item_unchecked` and
  `todolist_completed` can trigger booking rules.
- **Rule type "Before a date (only with incomplete todo list)"**: works like the
  "days before" rule but only fires for options whose todo list is not yet complete.
- **Placeholder `{todolist}`**: renders the list as plain text with `[x]` / `[ ]` markers.
- **Booking history**: checking, unchecking and completing the list are logged.
- **CSV import**: columns `enable_todolist` (0/1) and `todolist_items` (newline-separated).
  Missing columns keep the stored values.

## Capabilities

| Capability | Default roles | Purpose |
|---|---|---|
| `bookingextension/todolist:viewtodolist` | student, teacher, editingteacher, manager | View the list on the option view page |
| `bookingextension/todolist:checktodolist` | teacher, editingteacher, manager | Check / uncheck items |
| `bookingextension/todolist:edittodolist` | editingteacher, manager | Edit the list in the booking option form |

## Settings

Site administration > Plugins > Activity modules > Booking > Todo list:

- **Enable todo list extension** (`bookingextension_todolist/enableglobally`, default on).

## Installing manually

Put the contents of this directory into:

    {your/moodle/dirroot}/mod/booking/bookingextension/todolist

Then complete installation via:

    Site administration > Notifications

Or run:

    php admin/cli/upgrade.php

## License

2026 Wunderbyte GmbH <info@wunderbyte.at>

GNU GPL v3 or later.
