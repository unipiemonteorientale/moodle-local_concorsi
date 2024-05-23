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
 * Settings for the Concorsi.
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $page = new admin_settingpage('local_concorsi', new lang_string('pluginname', 'local_concorsi'));

    $coursecontext = context_course::instance(SITEID);
    $roles = get_assignable_roles($coursecontext, ROLENAME_BOTH);
    $default = array();
    $page->add(new admin_setting_configmultiselect('local_concorsi/roles',
                new lang_string('roles', 'local_concorsi'),
                new lang_string('configroles', 'local_concorsi'),
                $default, $roles));

    $choices = [];
    for ($i = 8; $i <= 20; $i++) {
        $choices[$i] = $i;
    }
    $page->add(new admin_setting_configselect('local_concorsi/usernamelength',
                new lang_string('usernamelength', 'local_concorsi'),
                new lang_string('configusernamelength', 'local_concorsi'),
                '8', $choices));

    $page->add(new admin_setting_configselect('local_concorsi/passwordlength',
                new lang_string('passwordlength', 'local_concorsi'),
                new lang_string('configpasswordlength', 'local_concorsi'),
                '8', $choices));

    $choices = [];
    for ($i = 2; $i <= 15; $i++) {
        $choices[$i] = $i;
    }
    $page->add(new admin_setting_configselect('local_concorsi/idnumberlength',
                new lang_string('idnumberlength', 'local_concorsi'),
                new lang_string('configidnumberlength', 'local_concorsi'),
                '4', $choices));

    $page->add(new admin_setting_configtext('local_concorsi/emaildomain',
                new lang_string('emaildomain', 'local_concorsi'),
                new lang_string('configemaildomain', 'local_concorsi'),
                'example.com'));

    $page->add(new admin_setting_configtextarea('local_concorsi/usercardtemplate',
                new lang_string('usercardtemplate', 'local_concorsi'),
                new lang_string('configusercardtemplate', 'local_concorsi'),
                '[[username]] - [[password]]'));

    $yesno = array(0 => new lang_string('no'), 1 => new lang_string('yes'));
    $page->add(new admin_setting_configselect('local_concorsi/localstore',
                new lang_string('localstore', 'local_concorsi'),
                new lang_string('configlocalstore', 'local_concorsi'),
                '0', $yesno));

    $ADMIN->add('localplugins', $page);
}

