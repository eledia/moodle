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
 * Block Definition. The Block gives system informations about user count and moodledata filsize.
 *
 * @package    block
 * @subpackage eledia_system_info
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2013 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_eledia_system_info extends block_base {

    public function init() {
        $this->title   = get_string('title', 'block_eledia_system_info');
        $this->version = 2012110100;// Format yyyymmddvv.
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function get_content() {
        global $DB;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        if (has_capability('moodle/site:config', CONTEXT_SYSTEM::instance())) {

            $config = get_config('block_eledia_system_info');

            $filterset = false;
            $sql = "SELECT count(id) FROM {user} WHERE username != 'guest' AND deleted = 0 ";
            $params = array();
            foreach ($config as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                if ($key == 'version') {
                    continue;
                }
                $filter = array_map('trim', explode("\n", $value));
                list($qrypart, $filter_params) = $DB->get_in_or_equal($filter);
                $sql .= ' AND NOT('.$key.' '.$qrypart.')';
                $params = array_merge($params, $filter_params);
                $filterset = true;
            }
            if ($filterset) {
                $users = $DB->count_records_sql($sql, $params);
                $this->content->text .= get_string('message', 'block_eledia_system_info').$users.'<br />';
            } else {
                $users = $DB->count_records('user', array('deleted' => 0));
                $users--;
                $this->content->text .= get_string('message', 'block_eledia_system_info').$users.'<br />';
            }

            $files_sql = 'SELECT (
    SUM( mf2.filesize ) /1024 /1024 ) size_in_mb
FROM (
    SELECT DISTINCT mf.contenthash, mf.filesize
    FROM {files} mf
    )mf2';
            $file_sum = $DB->get_records_sql($files_sql);
            $file_sum = current($file_sum)->size_in_mb;
            $file_sum = round($file_sum, 2);
            $this->content->text .= get_string('file_sum', 'block_eledia_system_info').$file_sum.'MB';
        }
        return $this->content;
    }

    public function has_config() {
            return true;
    }
}
