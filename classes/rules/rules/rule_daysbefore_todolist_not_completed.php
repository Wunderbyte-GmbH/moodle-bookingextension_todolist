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

namespace bookingextension_todolist\rules\rules;

use mod_booking\booking_rules\rules\rule_daysbefore;
use MoodleQuickForm;
use stdClass;

/**
 * Rule: notify before date but only for incomplete todo lists.
 *
 * @package     bookingextension_todolist
 * @copyright   2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule_daysbefore_todolist_not_completed extends rule_daysbefore {
    /** @var string */
    protected $rulename = 'rule_daysbefore_todolist_not_completed';

    /** @var string */
    protected $rulenamestringid = 'ruledaysbefore_todolist_not_completed';

    /** @var string */
    public $todoliststatusfilter = 'not_completed';

    /**
     * Rule name in select list.
     *
     * @param bool $localized
     * @return string
     */
    public function get_name_of_rule(bool $localized = true): string {
        return $localized ? get_string($this->rulenamestringid, 'bookingextension_todolist') : $this->rulename;
    }

    /**
     * Reuse daysbefore form and remove the todolist status chooser,
     * because this rule is fixed to incomplete todo lists.
     *
     * @param MoodleQuickForm $mform
     * @param array $repeateloptions
     * @param array $ajaxformdata
     * @return void
     */
    public function add_rule_to_mform(MoodleQuickForm &$mform, array &$repeateloptions, array $ajaxformdata = []) {
        parent::add_rule_to_mform($mform, $repeateloptions, $ajaxformdata);
        if (method_exists($mform, 'removeElement')) {
            $mform->removeElement('rule_daysbefore_todoliststatus');
        }
        $mform->addElement(
            'static',
            'rule_daysbefore_todolist_not_completed_desc',
            '',
            get_string('ruledaysbefore_todolist_not_completed_desc', 'bookingextension_todolist')
        );
    }

    /**
     * Persist rule with a hard-coded todolist filter.
     *
     * @param stdClass $data
     * @return int
     */
    public function save_rule(stdClass &$data): int {
        $data->rule_daysbefore_todoliststatus = 'not_completed';
        return parent::save_rule($data);
    }
}
