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

$string['auth_eledia_supporttitle'] = 'eLeDia Support Login';
$string['auth_eledia_supportdescription'] = 'The authentication method is used by eLeDia for platform support. Disabling the method will block support access to your platform for eLeDia.
eLeDia support staff uses this method for automatic login. You can identify who from eLeDia staff had access to your site at which time. This option is conform with GDPR regulation.';
$string['account_expiration'] = 'Account expiration (days of inactivity)';
$string['account_expiration_desc'] = 'eLeDia support account can be deleted automatically. The default setting is 30 days.';
$string['auth_emailnoemail'] = 'Tried to send confirmation email but failed!';
$string['alreadyconfirmed'] = 'Registration has already been confirmed';

$string['confirmation_email'] = 'email address to confirm new account creation';
$string['confirmed'] = 'Support registration has been confirmed';
$string['confirmation_email_desc'] = 'Adding a mail address will notify the owner via mail when eLeDia creates a new account for support purposes.
The account will be blocked until it is confirmed. eLeDia support service can be delayed until confirmation when this feature is activated.';
$string['confirm_info_email_subject'] = 'Support registration has been confirmed';
$string['confirm_info_email_message'] = 'Hi {$a->fullname},

the new support account has been confirmed for {$a->sitename}.

{$a->admin}';

$string['emailconfirm'] = 'Confirm the support account';
$string['emailconfirmation'] = 'Hi,

a new support account has been requested at \'{$a->sitename}\'
using {$a->email}.
We can start with our support service when you have confirmed this user account.

To confirm the new account, please go to this web address:

{$a->link}

In most mail programs, this should appear as a blue link
which you can just click on. If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

If you need help, please contact the site administrator,
{$a->admin}';
$string['emailconfirmationsubject'] = '{$a}: support confirmation';
$string['emailconfirmsent'] = '<p>An email should have been sent to the confirmation address.</p>
   <p>The account now has to be confirmed.</p>
   <p>If you continue to have difficulty, contact the site administrator.</p>';
$string['error_redirectafterlogouturl'] = 'Seems like you entered an invalid URL. Please check your input and try again.';

$string['noauth'] = 'You couldn\'t be validated, wrong authentication found.';
$string['nologinpossible'] = 'Direct Login is forbidden for SSO authentification.';
$string['nouser'] = 'You couldn\'t be validated, user not found.';
$string['novalidation'] = 'You couldn\'t be validated, please try again.';
$string['notimevalidation'] = 'You couldn\'t be validated, secret key is expired.';

$string['pluginname'] = 'eLeDia Support Login';
$string['privacy:metadata'] = 'The eledia_support authentication plugin does not store any personal data.';

$string['redirectafterlogouturl'] = 'Redirect after logout';
$string['redirectafterlogouturl_description'] = 'You may redirect the user to a specific URL after he logged out. Please enter a full URL, e.g.: http://moodle.org. If no URL is defined no redirect will occur.';

$string['secretkey'] = 'Secret key for external.';
$string['secretkey_description'] = 'Used to encrypt external authentification.';

$string['task_process'] = 'cleanup timeout accounts';
