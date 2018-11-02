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
 * Admin settings and defaults.
 *
 * @package     auth
 * @subpackage  eledia_support
 * @author      Benjamin Wolf <support@eledia.de>
 * @copyright   2018 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $configs = array();
    // Introductory explanation.
    $configs[] = new admin_setting_heading('auth_eledia_support_header', '',
            get_string('auth_eledia_supportdescription', 'auth_eledia_support'));

    $configs[] = new admin_setting_configtext('secretkey', get_string('secretkey', 'auth_eledia_support'),
            get_string('secretkey_description', 'auth_eledia_support'), '', PARAM_RAW, '40', '1');

    $configs[] = new admin_setting_configtext('redirectafterlogouturl',
            get_string('redirectafterlogouturl', 'auth_eledia_support'),
            get_string('redirectafterlogouturl_description', 'auth_eledia_support'), '', PARAM_RAW, '40', '1');
    // Email adress for account creation confirm.
    $configs[] = new admin_setting_configtext('confirmation_email', get_string('confirmation_email', 'auth_eledia_support'),
            get_string('confirmation_email_desc', 'auth_eledia_support'), '', PARAM_RAW, '45', '1');
    // Number of inactive days before account expires.
    $configs[] = new admin_setting_configtext('account_expiration', get_string('account_expiration', 'auth_eledia_support'),
            get_string('account_expiration_desc', 'auth_eledia_support'), '30', PARAM_RAW, '5', '1');

    foreach ($configs as $config) {
        $config->plugin = 'auth_eledia_support';
        $settings->add($config);
    }

}
