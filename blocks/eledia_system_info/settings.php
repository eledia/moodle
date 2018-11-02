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

defined('MOODLE_INTERNAL') || die();
if ($ADMIN->fulltree) {

    $configs = array();
    $configs[] = new admin_setting_heading('block_eledia_system_info_header', '',
            get_string('configure_description', 'block_eledia_system_info'));

    $configs[] = new admin_setting_configtextarea('username', get_string('username'), '', '', PARAM_RAW, '40', '3');
    $configs[] = new admin_setting_configtextarea('firstname', get_string('firstname'), '', '', PARAM_RAW, '40', '3');
    $configs[] = new admin_setting_configtextarea('lastname', get_string('lastname'), '', '', PARAM_RAW, '40', '3');
    $configs[] = new admin_setting_configtextarea('email', get_string('email'), '', '', PARAM_RAW, '40', '3');
    $configs[] = new admin_setting_configtextarea('idnumber', get_string('idnumber'), '', '', PARAM_RAW, '40', '3');
    $configs[] = new admin_setting_configtextarea('auth', get_string('authentication'), '', '', PARAM_RAW, '40', '3');

    foreach ($configs as $config) {
        $config->plugin = 'block_eledia_system_info';
        $settings->add($config);
    }

}