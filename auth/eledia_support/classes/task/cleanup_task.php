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

namespace auth_eledia_support\task;

defined('MOODLE_INTERNAL') || die();

class cleanup_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_process', 'auth_eledia_support');
    }

    /**
     * Do the job.
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/user/lib.php');

        $eledia_support_users = $DB->get_records('user', array('auth' => 'eledia_support',
            'confirmed' => 1,
            'deleted' => 0));
        $timeout = time() - get_config('auth_eledia_support', 'account_expiration') * 24 * 60 * 60;

        foreach ($eledia_support_users as $user) {
            if ($user->lastaccess == 0) {
                // Never logged in so check for timecreated.
                if ($user->timecreated < $timeout) {
                    \user_delete_user($user);
                }
                continue;
            }
            if ($user->lastaccess < $timeout) {
                // Timeout.
                \user_delete_user($user);
            }
        }
    }
}
