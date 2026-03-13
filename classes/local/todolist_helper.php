<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace bookingextension_todolist\local;

use mod_booking\booking_option;

/**
 * Helper methods for todo list logic.
 *
 * @package     bookingextension_todolist
 * @copyright   2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class todolist_helper {
    /**
     * Parse textarea value into trimmed non-empty lines.
     *
     * @param string $rawtext
     * @return array
     */
    public static function parse_lines(string $rawtext): array {
        $lines = preg_split('/\R/u', $rawtext);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $result[] = $line;
        }

        return $result;
    }

    /**
     * Get todo items for an option.
     *
     * @param int $optionid
     * @return array
     */
    public static function get_items_for_option(int $optionid): array {
        global $DB;

        return $DB->get_records('bookingextension_todolist_item', ['optionid' => $optionid], 'sortorder ASC, id ASC');
    }

    /**
     * Return only item texts from records in display order.
     *
     * @param array $records
     * @return array
     */
    public static function texts_from_records(array $records): array {
        return array_values(array_map(static fn($record) => trim((string)$record->text), $records));
    }

    /**
     * Check if option has any completed items.
     *
     * @param int $optionid
     * @return bool
     */
    public static function has_completed_items(int $optionid): bool {
        global $DB;

        return $DB->record_exists('bookingextension_todolist_item', ['optionid' => $optionid, 'status' => 1]);
    }

    /**
     * Check if all todo items are completed.
     * Returns false when there are no items.
     *
     * @param int $optionid
     * @return bool
     */
    public static function todolist_completed(int $optionid): bool {
        global $DB;

        $all = $DB->count_records('bookingextension_todolist_item', ['optionid' => $optionid]);
        if ($all === 0) {
            return false;
        }

        $completed = $DB->count_records('bookingextension_todolist_item', ['optionid' => $optionid, 'status' => 1]);
        return $all === $completed;
    }

    /**
     * Sync booking_options.todoliststatus with item completion state.
     *
     * @param int $optionid
     * @return void
     */
    public static function refresh_option_todolist_status(int $optionid): void {
        global $DB;

        $status = self::todolist_completed($optionid) ? 1 : 0;
        $DB->set_field('booking_options', 'todoliststatus', $status, ['id' => $optionid]);
    }

    /**
     * Save textarea list by replacing all existing records.
     *
     * @param int $optionid
     * @param array $lines
     * @param int $userid
     * @return void
     */
    public static function replace_items(int $optionid, array $lines, int $userid): void {
        global $DB;

        $DB->delete_records('bookingextension_todolist_item', ['optionid' => $optionid]);

        $time = time();
        foreach ($lines as $index => $line) {
            $record = (object)[
                'optionid' => $optionid,
                'text' => $line,
                'sortorder' => $index,
                'status' => 0,
                'completed_by' => null,
                'completed_at' => null,
                'created_by' => $userid,
                'created_at' => $time,
            ];
            $DB->insert_record('bookingextension_todolist_item', $record);
        }

        self::refresh_option_todolist_status($optionid);
    }

    /**
     * Build render context for Mustache template.
     *
     * @param int $optionid
     * @param bool $cancheck
     * @param int $cmid
     * @return array
     */
    public static function get_template_context(int $optionid, bool $cancheck, int $cmid): array {
        $records = self::get_items_for_option($optionid);
        $items = [];

        foreach ($records as $record) {
            $checked = ((int)$record->status === 1);
            $items[] = [
                'id' => (int)$record->id,
                'optionid' => $optionid,
                'text' => format_string((string)$record->text),
                'checked' => $checked,
                'checkedattr' => $checked ? 'checked' : '',
                'readonly' => !$cancheck,
            ];
        }

        return [
            'cmid' => $cmid,
            'optionid' => $optionid,
            'cancheck' => $cancheck,
            'readonly' => !$cancheck,
            'hasitems' => !empty($items),
            'items' => $items,
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Render todo list as plain text for placeholders.
     *
     * @param int $optionid
     * @return string
     */
    public static function format_as_plaintext(int $optionid): string {
        $records = self::get_items_for_option($optionid);
        if (empty($records)) {
            return '';
        }

        $lines = [];
        foreach ($records as $record) {
            $prefix = ((int)$record->status === 1) ? "[x]" : "[ ]";
            $lines[] = $prefix . ' ' . trim((string)$record->text);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Check if todo list is enabled for option.
     *
     * @param int $optionid
     * @return bool
     */
    public static function is_enabled_for_option(int $optionid): bool {
        return (bool)booking_option::get_value_of_json_by_key($optionid, 'enable_todolist');
    }
}
