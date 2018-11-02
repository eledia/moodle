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
 * Remove the configuration setting
 *
 * @package    local_eledia_defaultblocks
 * @author      Andreas Grabs<andreas.grabs@eledia.de>
 * @copyright   2014 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $hinttext = get_string('defaultblocks_hint', 'local_eledia_defaultblocks');
    $hinttext .= get_string('possible_blocks_are', 'local_eledia_defaultblocks');
    $hinttext .= '<div><ul>';

    $blocks = $PAGE->blocks->get_installed_blocks();
    foreach ($blocks as $block) {
        if (!blocks_name_allowed_in_format($block->name, 'course')) {
            continue;
        }
        if (!$block->visible) {
            continue;
        }
        $hinttext .= '<li><strong>'.$block->name.'</strong> ('.get_string('pluginname', 'block_'.$block->name).')</li>';
    }
    $hinttext .= '</ul></div>';

    $settings = new admin_settingpage('local_eledia_defaultblocks', get_string('pluginname', 'local_eledia_defaultblocks'));
    $ADMIN->add('courses', $settings);

    $settings->add(new admin_setting_configtext('defaultblocks_override',
                        get_string('defaultblocks', 'local_eledia_defaultblocks'),
                        $hinttext, '', PARAM_RAW, 80));

}
