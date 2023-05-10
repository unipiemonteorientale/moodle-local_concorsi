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
 * Concorsi user management
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/createusers_form.php');
require_once(__DIR__ . '/locallib.php');

$courseid = required_param('course', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUM);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('coursemisconf');
}

require_login($course);

$urlquery = array();
if (!empty($course->id)) {
    $urlquery['course'] = $course->id;
}

$url = new moodle_url('/local/concorsi/manageusers.php', $urlquery);
$PAGE->set_url($url);

$context = context_course::instance($course->id);
require_capability('local/concorsi:manageusers', $context);

$PAGE->set_context($context);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('course');

$config = get_config('local_concorsi');

$component = 'local_concorsi';
$filearea = 'usercards';

switch ($action) {
    case 'add':
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/pdflib.php');

        $name = required_param('name', PARAM_ALPHANUM);
        $date = required_param_array('date', PARAM_INT);
        $users = required_param('users', PARAM_INT);
        $roleid = required_param('role', PARAM_INT);

        if ($users > 0) {
            $manual = null;
            $instance = null;

            if (enrol_is_enabled('manual')) {
                $manual = enrol_get_plugin('manual');
                if ($instances = enrol_get_instances($course->id, false)) {
                    foreach ($instances as $instance) {
                        if ($instance->enrol === 'manual' && $instance->status != ENROL_INSTANCE_DISABLED) {
                            // Found the manual enrol instance of this course.
                            break;
                        }
                    }
                }
            }

            // We use only manual enrol plugin here, if it is disabled no user is created and no enrol is done.
            if (($manual != null) && ($instance != null)) {
                $doc = new pdf;
                $doc->setPrintHeader(false);
                $doc->setPrintFooter(false);
                $firstname = $name;
                $lastname = implode('-', $date);
                $base = 0;
                $existingusers = $DB->get_records('user', array('firstname' => $firstname, 'lastname' => $lastname));
                foreach ($existingusers as $existinguser) {
                    $base = max($base, intval($existinguser->idnumber));
                }
                for ($i = 1; $i <= $users; $i++) {
                    $user = new stdClass();
                    do {
                        $user->username = local_concorsi_generate_username($config->usernamelength);
                    } while ($DB->record_exists('user', array('username' => $user->username)));
                    $user->password = generate_password($config->passwordlength);

                    $doc = local_concorsi_add_user_card($doc, $user, $course, $roleid);

                    $user->firstname = $firstname;
                    $user->lastname = $lastname;
                    $user->idnumber = $base + $i;
                    $user->email = $user->username . '@' . $config->emaildomain;
                    $user->emailstop = 1;
                    $user->confirmed = 1;
                    $user->mnethostid = $CFG->mnet_localhost_id;

                    $userid = user_create_user($user);

                    $manual->enrol_user($instance, $userid, $roleid, time(), 0);

                }

                $rolename = $DB->get_field('role', 'shortname', array('id' => $roleid));
                $filename = clean_param($firstname . '_' . $lastname . '_' . $rolename . '_' . time()  . '.pdf', PARAM_FILE);
                if ($config->localstore) {
                    $tempdir = make_temp_directory('core_plugin/local_concorsi') . '/';
                    $filepath = $tempdir . $filename;

                    $doc->Output($filepath, 'F');

                    $fileinfo = [
                        'contextid' => $context->id,
                        'component' => $component,
                        'filearea' => $filearea,
                        'itemid' => $course->id,
                        'filepath' => '/',
                        'filename' => $filename,
                    ];
                    $fs = get_file_storage();
                    $fs->create_file_from_pathname($fileinfo, $filepath);
                } else {
                    $doc->Output($filename, 'D');
                }
            }
        }
        break;
    case 'delete':
        $filename = required_param('file', PARAM_FILE);

        $fs = get_file_storage();
        if ($file = $fs->get_file($context->id, $component, $filearea, $course->id, '/', $filename)) {
            $file->delete();
        } else {
            throw new \moodle_exception('filenotfound');
        }
    break;
    default:
        // Print form.
}

// Print the page header.
$strmanageusers = get_string('manageusers', 'local_concorsi');

$PAGE->set_title($strmanageusers);
$PAGE->set_heading($strmanageusers);

echo $OUTPUT->header();

$createusers = new createusers_form(null, array('courseid' => $course->id));
echo html_writer::start_tag('div', array('class' => 'createusers'));
$createusers->display();
echo html_writer::end_tag('div');

local_concorsi_display_usercards_files($course->id, $context->id, $component, $filearea);

echo $OUTPUT->footer();
