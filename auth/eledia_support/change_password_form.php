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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');// It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

class login_change_password_form extends moodleform {

    public function definition() {
        global $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('changepassword').' (global)', '');

        // Visible elements.
        $mform->addElement('static', 'username', get_string('username'), $USER->username);

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }

        // Define the typical password fields (old-, new- and new again password).
        $mform->addElement('password', 'oldpassword', get_string('oldpassword'));
        $mform->addRule('oldpassword', get_string('required'), 'required', null, 'client');
        $mform->setType('oldpassword', PARAM_RAW);

        $mform->addElement('password', 'newpassword1', get_string('newpassword'));
        $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword1', PARAM_RAW);

        $mform->addElement('password', 'newpassword2', get_string('newpassword').' ('.get_string('again').')');
        $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword2', PARAM_RAW);

        $this->add_action_buttons(true);
    }

    /**
     * Perform extra password change validation.
     * Are the checks ok, so the password will be change just here
     */
    public function validation($data, $files) {
        global $CFG, $USER;
        $errors = parent::validation($data, $files);

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
            return $errors;
        }

        if ($data['oldpassword'] == $data['newpassword1']) {
            $errors['newpassword1'] = get_string('mustchangepassword');
            $errors['newpassword2'] = get_string('mustchangepassword');
            return $errors;
        }

        // Load the appropriate auth plugin.
        $userauth = get_auth_plugin('eledia_support');

        if (!$userauth->user_login($USER->username, $data['oldpassword'])) {
            $errors['oldpassword'] = get_string('wrong_current_password', 'auth_eledia_support');
            return $errors;
        }

        $errmsg = '';// Prevents eclipse warnings.
        if (!$userauth->check_password_policy($data['newpassword1'])) {

            if (!empty($CFG->passwordpolicy)) {
                $msg = print_password_policy();
            } else {
                $msg = get_string('error');
            }
            $errors['newpassword1'] = $msg;
            $errors['newpassword2'] = $msg;
            return $errors;
        }

        $changeresult = $userauth->changepassword($USER->username, $data['oldpassword'], $data['newpassword1']);
        if ($changeresult->error) {
            switch($changeresult->info) {
                case 'found':
                    $errors['newpassword1'] = get_string("password_used_in_history", "auth_eledia_support");
                    break;
                case 'time':
                    $errors['newpassword1'] = get_string("password_changed_in_short_time", "auth_eledia_support");
                    break;
                case 'nologin':
                    $errors['oldpassword'] = get_string("login_failed", "auth_eledia_support");
                    break;
                case 'norights':
                    $errors['newpassword1'] = get_string("norights_to_change_password", "auth_eledia_support");
                    break;
                case 'unknown':
                    $errors['newpassword1'] = get_string("user_unknown", "auth_eledia_support");
                    break;
                default:
                    $errors['oldpassword'] = get_string('error');
                    $errors['newpassword1'] = get_string('error');
                    $errors['newpassword2'] = get_string('error');
            }
            return $errors;
        }
        return $errors;
    }
}
