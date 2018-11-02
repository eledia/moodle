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
 * Authentication Plugin: eledia_support
 *
 * Auth Plugin for moodle systems
 *
 * Doesn't actually authenticate users trying to login
 * by the standard moodle method, but tells them to go
 * to the moodle Login page
 *
 * Exception: If the config option for local logins is
 * enabled, users can login locally. This plugin uses
 * its parents "user_login" method in this case, i.e.
 * "auth/eledia_support" must be fully configured and functional
 * for local logins to be possible.
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
require_once($CFG->libdir.'/authlib.php');

/**
 *
 */
class auth_plugin_eledia_support extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'eledia_support';
        $this->config = get_config('auth_eledia_support');
    }

    /**
     * Checks if direct login is enabled and authenticates the user
     * using the "parent::user_login" method
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    public function user_login ($username, $password) {
        global $CFG, $DB;
        if ($DB->record_exists('user', array('username' => $username, 'auth' => 'eledia_support'))) {
            notice(get_string('nologinpossible', 'auth_eledia_support'), $CFG->wwwroot);
        }
        return false;
    }

    /**
     * Additional configuration for this plugin.
     *
     */
    public function config_form($config, $err, $user_fields) {
        include('config.html');
    }

    public function validate_form($form, &$err) {

        if (isset($form->redirectafterlogouturl)) {

            $clean_url = clean_param($form->redirectafterlogouturl, PARAM_URL);
            if ($clean_url != $form->redirectafterlogouturl) {
                $err['redirectafterlogouturl'] = get_string('error_redirectafterlogouturl', 'auth_eledia_support');
            }
        }
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    public function process_config($config) {
        // Set to defaults if undefined.
        if (!isset($config->secretkey)) {
            $config->secretkey = null;
        }

        if (!isset($config->redirectafterlogouturl)) {
            $config->redirectafterlogouturl = null;
        }

        // Save settings.
        set_config('secretkey', $config->secretkey, 'auth_eledia_support');
        set_config('redirectafterlogouturl', $config->redirectafterlogouturl, 'auth_eledia_support');
        return true;
    }

    /**
     * Hook for overriding behaviour of login page.
     *
     * This method replaces the old "login.php" file in the plugins directory
     * which was used as an entry point for eledia_support-SSO Logins. The SSO-Agent must
     * now redirect the user to the normal "/login/index.php" page, where this
     * Hook will be called and then be able to process the params which come
     * from the SSO-Agent.
     *
     * @global object
     * @global object
     */
    public function loginpage_hook() {
        global $DB, $CFG, $SESSION;

        $config = get_config('auth_eledia_support');

        // We dont want to use this auth plugin without a secret key.
        if (empty($config->secretkey)) {
            return false;
        }
        if (empty($CFG->email_support_secretkey)) {
            return false;
        }

        // Parameters are made optional, so moodle won't say what params might be missing.
        $expire = optional_param('expire', '', PARAM_RAW); // Checked but pretty much useless in itself.
        $idnumber = optional_param('idnumber', '', PARAM_RAW); // Important.
        $callerhash = optional_param('callerhash', '', PARAM_ALPHANUM); // Important.
        $target_page = optional_param('target_page', '', PARAM_URL);
        $username = optional_param('username', '', PARAM_RAW);
        $firstname = optional_param('firstname', '', PARAM_RAW);
        $lastname = optional_param('lastname', '', PARAM_RAW);
        $email = optional_param('email', '', PARAM_RAW);

        // Check if all neccessary params are present.
        if (empty($expire) || empty($idnumber)|| empty($callerhash)) {
            if ($CFG->debugdisplay == true && $CFG->debug == DEBUG_DEVELOPER) {
                echo "Debuginfo: error - params incomplete expire:$expire idnumber:$idnumber callerhash:$callerhash";
            }
            return false;
        }

        // Check if the login has expired.
        if ($expire <= time()) {
            $event = \auth_eledia_support\event\auth_eledia_support_error::create(array(
                'objectid' => 0,
                'context' => CONTEXT_SYSTEM::instance(),
                'userid' => 0,
                'other' => array('errortype' => 'error - $expire < current time',
                    'errormsg' => 'query string: '.$_SERVER['QUERY_STRING']),
            ));
            $event->trigger();
            if ($CFG->debugdisplay == true && $CFG->debug == DEBUG_DEVELOPER) {
                echo "Debuginfo: error - $expire < current time";
            }
            return false;
        }

        // Validate the callerhash.
        $clienthash = md5($idnumber.$expire.$config->secretkey.
                $username.$firstname.$lastname.$email.$CFG->email_support_secretkey);
        if ($clienthash != $callerhash) {
            $event = \auth_eledia_support\event\auth_eledia_support_error::create(array(
                'objectid' => 0,
                'context' => CONTEXT_SYSTEM::instance(),
                'userid' => 0,
                'other' => array('errortype' => 'error - invalid callerhash',
                    'errormsg' => 'query string: '.$_SERVER['QUERY_STRING']),
            ));
            $event->trigger();
            if ($CFG->debugdisplay == true && $CFG->debug == DEBUG_DEVELOPER) {
                echo "Debuginfo: error - invalid callerhash:$callerhash clienthash:$clienthash";
            }
            return false;
        }

        // Decode idnumber.
        $idnumber = $this->decode_param($idnumber);

        // Check if the user exists.
        $user_local = $DB->get_record('user', array('idnumber' => $idnumber));
        if (!$user_local) {
            // Decode params and build up user object.
            $user = new stdClass();
            $user->idnumber = $idnumber;
            $user->username = $this->decode_param($username);
            $user->firstname = $this->decode_param($firstname);
            $user->lastname = $this->decode_param($lastname);
            $user->email = $this->decode_param($email);
            $user = $this->eledia_support_create_support_user($user);
            exit; // Should never eb reached.
        } else {
            $user = $user_local;
        }

        // Check if user has "eledia_support" as auth method.
        if ($user->auth != 'eledia_support') {
            $event = \auth_eledia_support\event\auth_eledia_support_error::create(array(
                'objectid' => 0,
                'context' => CONTEXT_SYSTEM::instance(),
                'userid' => 0,
                'other' => array('errortype' => 'error- wrong auth method',
                    'errormsg' => 'query string: '.$_SERVER['QUERY_STRING']),
            ));
            $event->trigger();
            if ($CFG->debugdisplay == true && $CFG->debug == DEBUG_DEVELOPER) {
                echo "Debuginfo: error - user does not have eledia_support as auth method";
            }
            return false;
        }

        // No further authentication needed, since we did that by checking the callerhash.
        $user = get_complete_user_data('idnumber', $user->idnumber);
        complete_user_login($user);
        if (!empty($target_page)) {
            redirect($target_page);
        } else {
            redirect($CFG->wwwroot);
        }
        return; // Never reached.
    }


    public function logoutpage_hook() {
        global $USER, $redirect;

        if ($USER->auth == 'eledia_support' && !empty($this->config->redirectafterlogouturl)) {
            $redirect = $this->config->redirectafterlogouturl;
        }
    }

    public function decode_param($param) {
        $search = array('.', '_', '-');
        $replace = array('+', '/', '=');
        $param = str_replace($search, $replace, $param);
        $param = base64_decode($param);
        return $param;
    }

    public function eledia_support_create_support_user($user) {
        global $CFG, $PAGE, $OUTPUT;
        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
        require_once($CFG->dirroot.'/user/editlib.php');

        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        $user->id = user_create_user($user, false, false);
        $user = signup_setup_new_user($user);
        $user->auth = 'eledia_support';
        user_update_user($user, false, false);

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();

        // Add admin role.
        $admins = array();
        foreach (explode(',', $CFG->siteadmins) as $admin) {
            $admin = (int)$admin;
            if ($admin) {
                $admins[$admin] = $admin;
            }
        }
        $admins[$user->id] = $user->id;
        set_config('siteadmins', implode(',', $admins));

        // Send confirmation email.
        if (! $this->send_confirmation_email($user)) {
            print_error('auth_emailnoemail', 'auth_eledia_support');
        }

        $emailconfirm = get_string('emailconfirm', 'auth_eledia_support');
        $PAGE->navbar->add($emailconfirm);
        $PAGE->set_title($emailconfirm);
        $PAGE->set_heading($PAGE->course->fullname);
        echo $OUTPUT->header();
        notice(get_string('emailconfirmsent', 'auth_eledia_support', $user->email), "$CFG->wwwroot/index.php");
    }

    /**
     * Send email to specified address with confirmation text and activation link.
     *
     * @param stdClass $user A {@link $USER} object
     * @param string $confirmationurl user confirmation URL
     * @return bool Returns true if mail was sent OK and false if there was an error.
     */
    public function send_confirmation_email($user) {
        global $CFG, $DB;

        // Check for present setting.
        $confirm_email = get_config('auth_eledia_support', 'confirmation_email');

        // Login user after creation when email confirm is truned off.
        if (empty($confirm_email)) {
            $DB->set_field("user", "confirmed", 1, array("id" => $user->id));

            // Check if user has "eledia_support" as auth method.
            if ($user->auth != 'eledia_support') {
                $event = \auth_eledia_support\event\auth_eledia_support_error::create(array(
                    'objectid' => 0,
                    'context' => CONTEXT_SYSTEM::instance(),
                    'userid' => 0,
                    'other' => array('errortype' => 'error- wrong auth method',
                        'errormsg' => 'query string: '.$_SERVER['QUERY_STRING']),
                ));
                $event->trigger();
                if ($CFG->debugdisplay == true && $CFG->debug == DEBUG_DEVELOPER) {
                    echo "Debuginfo: error - user does not have eledia_support as auth method";
                }
                return false;
            }

            // No further authentication needed, since we did that by checking the callerhash.
            $user = get_complete_user_data('idnumber', $user->idnumber);
            complete_user_login($user);
            // Redirect to site.
            redirect("$CFG->wwwroot/index.php");
        }

        $site = get_site();
        $supportuser = core_user::get_support_user();

        $data = new stdClass();
        $data->firstname = fullname($user);
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();
        $data->email = $user->email;

        $subject = get_string('emailconfirmationsubject', 'auth_eledia_support', format_string($site->fullname));

        // Perform normal url encoding of the username first.
        $username = urlencode($user->username);
        // Prevent problems with trailing dots not being included as part of link in some mail clients.
        $username = str_replace('.', '%2E', $username);

        $data->link = $CFG->wwwroot.'/auth/eledia_support/confirm.php?data='.$user->secret.'/'.$username;

        $message     = get_string('emailconfirmation', 'auth_eledia_support', $data);
        $messagehtml = text_to_html(get_string('emailconfirmation', 'auth_eledia_support', $data), false, false, true);

        // Send email to confirm adress.
        $confirm_user = new stdClass();
        $confirm_user->mailformat = 1;  // Always send HTML version as well.
        $confirm_user->id          = guest_user()->id;
        $confirm_user->username    = 'confirm_user';
        $confirm_user->lang        = current_language();
        $confirm_user->firstaccess = time();
        $confirm_user->mnethostid  = $CFG->mnet_localhost_id;
        $confirm_user->email       = $confirm_email;

        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        return email_to_user($confirm_user, $supportuser, $subject, $message, $messagehtml);
    }

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    public function can_confirm() {
        return true;
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    public function user_confirm($username, $confirmsecret) {
        global $DB;
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret == $confirmsecret && $user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->secret == $confirmsecret) {   // They have provided the secret key to get in.
                $DB->set_field("user", "confirmed", 1, array("id" => $user->id));
                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }
}
