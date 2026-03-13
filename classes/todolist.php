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

use admin_setting_heading;
use admin_settingpage;
use mod_booking\plugininfo\bookingextension;
use mod_booking\plugininfo\bookingextension_interface;

defined('MOODLE_INTERNAL') || die();

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
        return false;
    }

    /**
     * Return metadata for added option fields.
     *
     * @return array
     */
    public function get_option_fields_info_array(): array {
        return [];
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
        $settings = new admin_settingpage(
            'bookingextension_todolist_settings',
            get_string('pluginname', 'bookingextension_todolist'),
            'moodle/site:config',
            $this->is_enabled() === false
        );

        $settings->add(new admin_setting_heading(
            'bookingextension_todolist_heading',
            get_string('todolist:heading', 'bookingextension_todolist'),
            get_string('todolist:heading_desc', 'bookingextension_todolist')
        ));

        $adminroot->add('modbookingfolder', $settings);
    }

    /**
     * Adds settings singleton data.
     *
     * @param int $optionid
     * @return object
     */
    public static function load_data_for_settings_singleton(int $optionid): object {
        return (object)[];
    }

    /**
     * Adds data to optionview description template.
     *
     * @param object $settings
     * @return array
     */
    public static function set_template_data_for_optionview(object $settings): array {
        return [];
    }
}
