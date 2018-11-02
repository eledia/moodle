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
 *
 * @package     auth
 * @subpackage  eledia_support
 * @author      Benjamin Wolf <support@eledia.de>
 * @copyright   2018 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('change_password_form.php');

// NOTE: The passwordchange itself is done in the login_change_password_form class!!!

// Require proper login; guest user can not change password.
require_login();
if (!isloggedin() or isguestuser()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->wwwroot.'/login/change_password.php';
    }
    redirect(get_login_url());
}

// HTTPS is required in this page when $CFG->loginhttps enabled.
$PAGE->https_required();
$PAGE->set_url('/login/change_password.php');
$PAGE->set_context(CONTEXT_SYSTEM::instance());

$mform = new login_change_password_form();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/user/view.php?id='.$USER->id);
} else if ($data = $mform->get_data()) {
    // Here we only come to if the password is ok.
    $strpasswordchanged = get_string('passwordchanged');

    $fullname = fullname($USER, true);

    if (empty($SESSION->wantsurl) or $SESSION->wantsurl == $CFG->httpswwwroot.'/login/change_password.php') {
        $returnto = "$CFG->wwwroot/user/view.php?id=$USER->id";
    } else {
        $returnto = $SESSION->wantsurl;
    }

    $PAGE->navbar->add($fullname, new moodle_url('/user/view.php', array('id' => $USER->id)));
    $PAGE->navbar->add($strpasswordchanged);
    $PAGE->set_title($strpasswordchanged);
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();

    notice($strpasswordchanged, $returnto);

    echo $OUTPUT->footer();
    exit;
}

$strchangepassword = get_string('changepassword');

$fullname = fullname($USER, true);

$PAGE->navbar->add($fullname, new moodle_url('/user/view.php', array('id' => $USER->id)));
$PAGE->navbar->add($strchangepassword);
$PAGE->set_title($strchangepassword);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();
// Just print the form.
$mform->display();
echo $OUTPUT->footer();
