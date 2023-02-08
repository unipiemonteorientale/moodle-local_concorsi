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
 * Concorsi lib
 *
 * @package    local_concorsi
 * @copyright  2023 and above Roberto Pinna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend Course Navigation block
 *
 * @param navigation_node $parentnode Navigation
 * @param stdClass $course Course object
 * @param context_course $context Context
 * @return void
 */
function local_concorsi_extend_navigation_course(navigation_node $parentnode, $course, context_course $context) {

    if (has_capability('local/concorsi:manageusers', $context)) {
        $manageuserstr = get_string('manageusers', 'local_concorsi');
        $url = new moodle_url('/local/concorsi/manageusers.php', array('course' => $course->id));
        $nodetype = navigation_node::NODETYPE_LEAF;
        $node = $parentnode->add($manageuserstr, $url, $nodetype, $manageuserstr, 'concorsi_manage');
    }
}

/**
 * Serve the files from the myplugin file areas.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_concorsi_pluginfile($course, $cm, $context, string $filearea, array $args,
                                   bool $forcedownload, array $options = []): bool {

    if ($context->contextlevel != CONTEXT_COURSE) {
        return false;
    }

    if ($filearea !== 'userscards') {
        return false;
    }

    require_login($course, true);

    if (!has_capability('local/concorsi:manageusers', $context)) {
        return false;
    }

    $itemid = array_shift($args);

    if ($filearea === 'userscards') {
        if ($course->id !== $itemid) {
            return false;
        }
    }

    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_concorsi', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    send_stored_file($file, 86200, 0, $forcedownload, $options);
}
