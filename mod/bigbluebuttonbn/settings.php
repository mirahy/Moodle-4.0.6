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
 * Settings for BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010-2017 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once(dirname(__FILE__).'/locallib.php');



if ($ADMIN->fulltree) {
    // Configuration for BigBlueButton.
    $renderer = new \mod_bigbluebuttonbn\settings\renderer($settings);

    // Renders general warning message for settings.
    bigbluebuttonbn_settings_general_warning($renderer);
    // Renders general settings.
    bigbluebuttonbn_settings_general($renderer);

    if ( !isset($CFG->bigbluebuttonbn['bigbluebuttonbn_detect_mobile']) ) {
        $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_detect_mobile',
            get_string( 'config_detect_mobile', 'bigbluebuttonbn' ),
            get_string( 'config_detect_mobile_description', 'bigbluebuttonbn' ), BIGBLUEBUTTONBN_MOBILE_DETECT));
    }

    // Evaluates if recordings are enabled for the Moodle site.
    if (\mod_bigbluebuttonbn\locallib\config::recordings_enabled()) {
        // Renders settings for record feature.
        bigbluebuttonbn_settings_record($renderer);
        // Renders settings for import recordings.
        bigbluebuttonbn_settings_importrecordings($renderer);
        // Renders settings for showing recordings.
        bigbluebuttonbn_settings_showrecordings($renderer);
    }
    // Renders settings for meetings.
    bigbluebuttonbn_settings_waitmoderator($renderer);
    bigbluebuttonbn_settings_voicebridge($renderer);
    bigbluebuttonbn_settings_preupload($renderer);
    bigbluebuttonbn_settings_userlimit($renderer);
    bigbluebuttonbn_settings_duration($renderer);
    bigbluebuttonbn_settings_participants($renderer);
    bigbluebuttonbn_settings_formats_allowed($renderer);
    bigbluebuttonbn_settings_notifications($renderer);
    bigbluebuttonbn_settings_clienttype($renderer);
    // Renders settings for extended capabilities.
    bigbluebuttonbn_settings_extended($renderer);
    // Renders format option settings.
}
