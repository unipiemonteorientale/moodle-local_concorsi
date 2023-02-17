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
 * Concorsi library code.
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generate a random username
 *
 * @param int $length Requested username length
 *
 * @return string Random username
 */
function local_concorsi_generate_username($length = 8) {
    // The list of used characters cleaned of similar symbols.
    $chars = '2345789acdefghjkmnopqrstuvwxyz';
    $charslen = strlen($chars);
    $username = '';
    for ($i = 0; $i < $length; $i++) {
        $username .= $chars[random_int(0, $charslen - 1)];
    }
    return $username;
}

/**
 * Add a user credential card to pdf
 *
 * @param pdf $doc Pdf object
 * @param stdClass $user User object
 * @param stdClass $course Course object
 *
 * @return void
 */
function local_concorsi_add_user_card($doc, $user, $course) {
    if (!empty($user)) {
        $doc->AddPage();

        $search = array('[[course_fullname]]');
        $replace = array(format_string($course->fullname));
        foreach ($user as $field => $value) {
            $search[] = '[[' . $field . ']]';
            $replace[] = format_string($value);
        }

        $htmltemplate = get_config('local_concorsi', 'usercardtemplate');
        $html = str_ireplace($search, $replace, $htmltemplate);

        $doc->writeHTML($html, true, false, true, false, '');

        $doc->lastPage();
    }

    return $doc;
}

/**
 * Display usercard files for the course
 *
 * @param int $courseid Course id
 * @param int $contextid Context id
 * @param string $component Component name
 * @param string $filearea Filearea name
 *
 * @return void
 */
function local_concorsi_display_usercards_files($courseid, $contextid, $component, $filearea) {
    global $OUTPUT, $PAGE;

    $fs = get_file_storage();
    $files = $fs->get_area_files($contextid, $component, $filearea, $courseid);
    if (!empty($files)) {
        echo html_writer::tag('h3', new lang_string('usercardfiles', 'local_concorsi'));
        echo html_writer::start_tag('ul', array('class' => 'usercardfiles'));
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename != '.') {
                $urldownload = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $filename,
                    true
                );
                $query = array('course' => $courseid, 'file' => $filename, 'action' => 'delete');
                $urldelete = new moodle_url('/local/concorsi/manageusers.php', $query);
                $downloadlink = html_writer::tag('a', $filename, array('href' => $urldownload));
                $icon = $OUTPUT->action_icon($urldelete, new pix_icon('t/delete', get_string('delete')));
                $deletelink = html_writer::tag('a', $icon, array('href' => $urldelete));
                echo html_writer::tag('li', $downloadlink . '&nbsp;' . $deletelink);
            }
        }
        echo html_writer::end_tag('ul');
    }
}

