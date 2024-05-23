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
 * Manage usercards files
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(__DIR__ . '/filesedit_form.php');
require_once($CFG->dirroot . '/repository/lib.php');

// Current context.
$contextid = required_param('contextid', PARAM_INT);

// File parameters.
$component = optional_param('component', null, PARAM_COMPONENT);
$filearea = optional_param('filearea', null, PARAM_AREA);
$itemid = optional_param('itemid', null, PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

list($context, $course) = get_context_info_array($contextid);

$query = ['contextid' => $contextid, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid];
$url = new moodle_url('/local/concorsi/filesedit.php', $query);

require_login($course);
require_capability('local/concorsi:manageusers', $context);

$PAGE->set_url($url);
$PAGE->set_context($context);

if ($context->contextlevel == CONTEXT_COURSE) {
    $course = get_course($context->instanceid);
    $PAGE->set_heading($course->fullname);
}

$title = get_string('managefiles', 'local_concorsi');
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('course');

$browser = get_file_browser();

$data = new stdClass();
$options = ['subdirs' => 0, 'maxfiles' => -1, 'accepted_types' => '*', 'return_types' => FILE_INTERNAL];
file_prepare_standard_filemanager($data, 'files', $options, $contextid, $component, $filearea, $itemid);
$form = new files_edit_form(null, ['data' => $data,
                                   'contextid' => $contextid,
                                   'filearea' => $filearea,
                                   'component' => $component,
                                   'itemid' => $itemid,
                                   'returnurl' => $returnurl,
                                  ]
                           );

if ($form->is_cancelled()) {
    redirect($returnurl);
}

$data = $form->get_data();
if ($data) {
    $formdata = file_postupdate_standard_filemanager($data, 'files', $options, $contextid, $component, $filearea, $itemid);
    redirect($returnurl);
}

echo $OUTPUT->header();

echo $OUTPUT->container_start();
echo $OUTPUT->heading($title);
$form->display();
echo $OUTPUT->container_end();

echo $OUTPUT->footer();
