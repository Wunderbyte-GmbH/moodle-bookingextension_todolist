<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Class todolist.
 *
 * @package     bookingextension_todolist
 * @copyright   2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace bookingextension_todolist;

use context_module;
use mod_booking\plugininfo\bookingextension;
use mod_booking\plugininfo\bookingextension_interface;
use bookingextension_todolist\local\todolist_helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/booking/bookingextension/todolist/lib.php');

/**
 * Skeleton booking extension implementation.
 */
class todolist extends bookingextension implements bookingextension_interface {
    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function get_plugin_name(): string {
        return get_string('pluginname', 'bookingextension_todolist');
    }

    /**
     * Check if this extension adds option fields.
     *
     * @return bool
     */
    public function contains_option_fields(): bool {
        return true;
    }

    /**
     * Return metadata for added option fields.
     *
     * @return array
     */
    public function get_option_fields_info_array(): array {
        return [
            'todolist' => [
                'name' => 'todolist',
                'class' => 'bookingextension_todolist\\option\\fields\\todolist',
                'id' => MOD_BOOKING_OPTION_FIELD_TODOLIST,
            ],
        ];
    }

    /**
     * Returns event keys that are allowed for booking rules.
     *
     * @return array
     */
    public static function get_allowedruleeventkeys(): array {
        return [
            'todolist_item_checked',
            'todolist_item_unchecked',
            'todolist_completed',
        ];
    }

    /**
     * Load plugin settings in site administration.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig
     * @return void
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig): void {
        // No global settings required for v1.
    }

    /**
     * Adds settings singleton data.
     *
     * @param int $optionid
     * @return object
     */
    public static function load_data_for_settings_singleton(int $optionid): object {
        global $DB;

        $json = (string)$DB->get_field('booking_options', 'json', ['id' => $optionid]);
        $enabled = self::get_enabled_from_json($json);

        return (object)[
            'items' => $DB->get_records('bookingextension_todolist_item', ['optionid' => $optionid], 'sortorder ASC, id ASC'),
            'enabled' => $enabled,
        ];
    }

    /**
     * Adds data to optionview description template.
     *
     * @param object $settings
     * @return array
     */
    public static function set_template_data_for_optionview(object $settings): array {
        global $OUTPUT, $PAGE;

        $enabled = self::get_enabled_from_json((string)($settings->json ?? ''));
        if (empty($enabled)) {
            return [];
        }

        $context = context_module::instance((int)$settings->cmid);
        if (!has_capability('bookingextension/todolist:viewtodolist', $context)) {
            return [];
        }

        $cancheck = has_capability('bookingextension/todolist:checktodolist', $context);
        $templatecontext = todolist_helper::get_template_context((int)$settings->id, $cancheck, (int)$settings->cmid);
        $html = $OUTPUT->render_from_template('bookingextension_todolist/todolist', $templatecontext);

        if ($cancheck) {
            $PAGE->requires->js_call_amd('bookingextension_todolist/todolist', 'init');
        }

        return [[
            'key' => 'bookingextension_todolist',
            'value' => $html,
            'label' => 'todolist',
            'description' => get_string('todolist', 'bookingextension_todolist'),
        ]];
    }

    /**
     * Returns the enable flag from booking option JSON.
     *
     * @param string $json
     * @return int
     */
    private static function get_enabled_from_json(string $json): int {
        if ($json === '') {
            return 0;
        }

        $jsonobject = json_decode($json);
        if (!is_object($jsonobject) || !isset($jsonobject->enable_todolist)) {
            return 0;
        }

        return (int)$jsonobject->enable_todolist;
    }
}
