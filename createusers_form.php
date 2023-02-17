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

/**
 * Concorsi - Users creation form.
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Concorsi - Users creation form.
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class createusers_form extends moodleform {

    /**
     * The createusers form definition.
     */
    public function definition() {
        global $DB, $PAGE;

        $mform = $this->_form;

        $courseid = $this->_customdata['courseid'];

        if (!empty($courseid)) {
            $course = $DB->get_record('course', array('id' => $courseid));

            $mform->addElement('text', 'name', get_string('publicexamname', 'local_concorsi'));
            $mform->setDefault('name', $course->shortname);
            $mform->setType('name', PARAM_ALPHANUM);

            $mform->addElement('date_selector', 'date', get_string('publicexamdate', 'local_concorsi'));
            $mform->setDefault('date', $course->startdate);

            $mform->addElement('text', 'users', get_string('numberofusers', 'local_concorsi'));
            $mform->setDefault('users', 0);
            $mform->setType('users', PARAM_INT);

            $roles = get_assignable_roles($PAGE->context, ROLENAME_BOTH);
            $configroles = explode(',', get_config('local_concorsi', 'roles'));
            $enabledroles = array();
            foreach ($roles as $roleid => $role) {
                if (in_array($roleid, $configroles)) {
                    $enabledroles[$roleid] = $role;
                }
            }
            $mform->addElement('select', 'role', get_string('role', 'local_concorsi'), $enabledroles);
            $mform->addElement('hidden', 'course', $courseid);
            $mform->setType('course', PARAM_INT);
            $mform->addElement('hidden', 'action', 'add');
            $mform->setType('action', PARAM_ALPHA);

            $mform->addElement('submit', 'submitbutton', get_string('addusers', 'local_concorsi'));
        }
    }
}
