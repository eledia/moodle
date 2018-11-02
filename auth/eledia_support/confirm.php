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
 * Confirm self registered user.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require($CFG->dirroot.'/login/lib.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/eledia_support/auth.php');

$data = optional_param('data', '', PARAM_RAW);  // Formatted as:  secret/username.

$p = optional_param('p', '', PARAM_ALPHANUM);   // Old parameter:  secret.
$s = optional_param('s', '', PARAM_RAW);        // Old parameter:  username.
$redirect = optional_param('redirect', '', PARAM_LOCALURL);    // Where to redirect the browser once the user has been confirmed.

$PAGE->set_url('/auth/eledia_support/confirm.php');
$PAGE->set_context(context_system::instance());

if (!empty($data) || (!empty($p) && !empty($s))) {

    if (!empty($data)) {
        $dataelements = explode('/', $data, 2); // Stop after 1st slash. Rest is username. MDL-7647.
        $usersecret = $dataelements[0];
        $username   = $dataelements[1];
    } else {
        $usersecret = $p;
        $username   = $s;
    }

    $user = get_complete_user_data('username', $username);
    // Check for auth plugin.
    if (!$user->auth = 'eledia_support') {
        throw new moodle_exception('userautherror', 'auth_eledia_support');
    }

    $authplugin = new auth_plugin_eledia_support();
    $confirmed = $authplugin->user_confirm($username, $usersecret);

    if ($confirmed == AUTH_CONFIRM_ALREADY) {
        $PAGE->navbar->add(get_string("alreadyconfirmed", 'auth_eledia_support'));
        $PAGE->set_title(get_string("alreadyconfirmed", 'auth_eledia_support'));
        $PAGE->set_heading($COURSE->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<p>".get_string("alreadyconfirmed")."</p>\n";
        echo $OUTPUT->single_button(core_login_get_return_url(), get_string('courses'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    } else if ($confirmed == AUTH_CONFIRM_OK) {
        // The user has confirmed successfully.
        $PAGE->navbar->add(get_string("confirmed", 'auth_eledia_support'));
        $PAGE->set_title(get_string("confirmed", 'auth_eledia_support'));
        $PAGE->set_heading($COURSE->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<h3>".get_string("thanks").","."</h3>\n";
        echo "<p>".get_string("confirmed", 'auth_eledia_support')."</p>\n";
        echo $OUTPUT->single_button(core_login_get_return_url(), get_string('continue'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
// TODO email to user
        $site = get_site();
        $supportuser = core_user::get_support_user();

        $data = new stdClass();
        $data->fullname = fullname($user);
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        $subject     = get_string('confirm_info_email_subject', 'auth_eledia_support');
        $message     = get_string('confirm_info_email_message', 'auth_eledia_support', $data);
        $messagehtml = text_to_html($message, false, false, true);

        $user->mailformat = 1;  // Always send HTML version as well.

        email_to_user($user, $supportuser, $subject, $message, $messagehtml);

        exit;
    } else {
        print_error('invalidconfirmdata');
    }
} else {
    print_error("errorwhenconfirming");
}

redirect("$CFG->wwwroot/");
