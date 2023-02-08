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
 * Strings for component 'local_concorsi', language 'en'
 *
 * @package   local_concorsi
 * @copyright 2023 and above Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Default strings.
$string['pluginname'] = 'Public Exams';

// Availability & privacy strings.
$string['concorsi:manageusers'] = 'Can manage public exam users';
$string['privacy:metadata'] = 'The Public exams plugin create random users credential and assign student role to current course. It does not store any data itself.';

// Settings strings.
$string['roles'] = 'Roles';
$string['configroles'] = 'Public exam managers could enrol autocreated users with the selected roles';
$string['usernamelength'] = 'Username length';
$string['configusernamelength'] = 'The length of the random generated usernames';
$string['passwordlength'] = 'Password length';
$string['configpasswordlength'] = 'The length of the random generated passwords';
$string['emaildomain'] = 'Email domain';
$string['configemaildomain'] = 'Users fake email domain. Not used for sending real email';
$string['usercardtemplate'] = 'Usercard template';
$string['configusercardtemplate'] = 'Usercard template is used to create the pdf file with users credentials. You can use also HTML tags';
$string['localstore'] = 'Store credentials file';
$string['configlocalstore'] = 'Store credential files in Moodle filesystem';

// Plugin interface strings.
$string['manageusers'] = 'Manage public exam users';
$string['addusers'] = 'Add users';
$string['publicexamname'] = 'Public exam name';
$string['publicexamdate'] = 'Public exam date';
$string['numberofusers'] = 'Number of candidates';
$string['role'] = 'Role';
$string['usercardfiles'] = 'Usercard files';
$string['managefiles'] = 'Manage usercard files';
