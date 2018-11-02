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
global $CFG;

$idnumber = 'eledia_auth_test2'; // Set field for Test user.
$username = 'test_admin2';
$firstname = 'test2';
$lastname = 'admin2';
$email = 'a.test1004@eledia.de';

$expire = time() + 5;
$secretkey = '123456789';// Configure in plugin.
$secretkey2 = 'abcdefg';// Configure in config.php.
$platform = $CFG->wwwroot.'/login/index.php';
$target_page = $CFG->wwwroot.'/course/view.php?id=2';// Enter test course id here.

$idnumber = eledia_support_encode_param($idnumber);
$username = eledia_support_encode_param($username);
$firstname = eledia_support_encode_param($firstname);
$lastname = eledia_support_encode_param($lastname);
$email = eledia_support_encode_param($email);

$callerhash = md5($idnumber.$expire.$secretkey.
                $username.$firstname.$lastname.$email.$secretkey2);

$link = $platform.'?idnumber='.$idnumber.'&expire='.$expire.'&callerhash='.
        $callerhash.'&username='.$username.'&firstname='.$firstname.'&lastname='
        .$lastname.'&email='.$email.'&target_page='.$target_page;

echo '<a href="'.$link.'">Login Test</a>';

function eledia_support_encode_param($param) {
    $search = array('+', '/', '=');
    $replace = array('.', '_', '-');
    $param = base64_encode($param);
    $param = str_replace($search, $replace, $param);
    return $param;
}
