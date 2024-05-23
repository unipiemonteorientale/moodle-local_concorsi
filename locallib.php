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

// Redefine Password policy constants with no misreading characters.
define ('CONCORSI_LOWER', 'acdefghjkmnpqrstuvwxyz');
define ('CONCORSI_UPPER', 'ACEFHJKLMNPRTUVWXY');
define ('CONCORSI_DIGITS', '2345789');
define ('CONCORSI_NONALPHANUM', '?+/*#&$=');

/**
 * Generate a random username
 *
 * @param int $length Requested username length
 *
 * @return string Random username
 */
function local_concorsi_generate_username($length = 8) {
    // The list of used characters cleaned of similar symbols.
    $chars = CONCORSI_LOWER . CONCORSI_DIGITS;
    $charslen = strlen($chars);
    $username = '';
    for ($i = 0; $i < $length; $i++) {
        $username .= $chars[random_int(0, $charslen - 1)];
    }
    return $username;
}

/**
 * Returns a randomly generated password of length $maxlen.
 *
 * @param int $maxlen The maximum size of the password being generated.
 * @return string The generated password.
 */
function local_concorsi_generate_password($maxlen=10) {
    global $CFG;

    if (empty($CFG->passwordpolicy)) {
        $fillers = CONCORSI_DIGITS;
        $wordlist = file($CFG->wordlist);
        $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
        $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
        $filler1 = $fillers[rand(0, strlen($fillers) - 1)];
        $password = $word1 . $filler1 . $word2;
    } else {
        $minlen = !empty($CFG->minpasswordlength) ? $CFG->minpasswordlength : 0;
        $digits = $CFG->minpassworddigits;
        $lower = $CFG->minpasswordlower;
        $upper = $CFG->minpasswordupper;
        $nonalphanum = $CFG->minpasswordnonalphanum;
        $total = $lower + $upper + $digits + $nonalphanum;
        // Var minlength should be the greater one of the two ( $minlen and $total ).
        $minlen = $minlen < $total ? $total : $minlen;
        // Var maxlen can never be smaller than minlen.
        $maxlen = $minlen > $maxlen ? $minlen : $maxlen;
        $additional = $maxlen - $total;

        // Make sure we have enough characters to fulfill
        // complexity requirements.
        $passworddigits = CONCORSI_DIGITS;
        while ($digits > strlen($passworddigits)) {
            $passworddigits .= CONCORSI_DIGITS;
        }
        $passwordlower = CONCORSI_LOWER;
        while ($lower > strlen($passwordlower)) {
            $passwordlower .= CONCORSI_LOWER;
        }
        $passwordupper = CONCORSI_UPPER;
        while ($upper > strlen($passwordupper)) {
            $passwordupper .= CONCORSI_UPPER;
        }
        $passwordnonalphanum = CONCORSI_NONALPHANUM;
        while ($nonalphanum > strlen($passwordnonalphanum)) {
            $passwordnonalphanum .= CONCORSI_NONALPHANUM;
        }

        // Now mix and shuffle it all.
        $password = str_shuffle (substr(str_shuffle ($passwordlower), 0, $lower) .
                                 substr(str_shuffle ($passwordupper), 0, $upper) .
                                 substr(str_shuffle ($passworddigits), 0, $digits) .
                                 substr(str_shuffle ($passwordnonalphanum), 0 , $nonalphanum) .
                                 substr(str_shuffle ($passwordlower .
                                                     $passwordupper .
                                                     $passworddigits .
                                                     $passwordnonalphanum), 0 , $additional));
    }

    return substr ($password, 0, $maxlen);
}

/**
 * Add a user credential card to pdf
 *
 * @param pdf $doc Pdf object
 * @param stdClass $user User object
 * @param stdClass $course Course object
 * @param int $roleid User role id
 *
 * @return void
 */
function local_concorsi_add_user_card($doc, $user, $course, $roleid) {
    global $DB;

    if (!empty($user)) {
        $doc->AddPage();

        $search = ['[[course_fullname]]'];
        $replace = [format_string($course->fullname)];
        $search[] = '[[role]]';
        $role = $DB->get_field('role', 'name', ['id' => $roleid]);
        $replace[] = format_string($role);
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
    if (!empty($files) && (count($files) > 1)) {
        echo html_writer::tag('h3', new lang_string('usercardfiles', 'local_concorsi'));
        echo html_writer::start_tag('ul', ['class' => 'usercardfiles']);
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
                $query = ['course' => $courseid, 'file' => $filename, 'action' => 'delete'];
                $urldelete = new moodle_url('/local/concorsi/manageusers.php', $query);
                $downloadlink = html_writer::tag('a', $filename, ['href' => $urldownload]);
                $icon = $OUTPUT->action_icon($urldelete, new pix_icon('t/delete', get_string('delete')));
                $deletelink = html_writer::tag('a', $icon, ['href' => $urldelete]);
                echo html_writer::tag('li', $downloadlink . '&nbsp;' . $deletelink);
            }
        }
        echo html_writer::end_tag('ul');
    }
}

