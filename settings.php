<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     aiprovider_openrouter
 * @copyright   2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_ai\admin\admin_settingspage_provider;

defined('MOODLE_INTERNAL') || die();

global $CFG;

if ($hassiteconfig) {
    // Provider specific settings heading.
    $settings = new admin_settingspage_provider(
        'aiprovider_openrouter',
        new lang_string('pluginname', 'aiprovider_openrouter'),
        'moodle/site:config',
        true,
    );

    $settings->add(new admin_setting_heading(
        'aiprovider_openrouter/general',
        new lang_string('settings', 'core'),
        '',
    ));

    // Setting to store OpenRouter API key.
    $settings->add(new admin_setting_configpasswordunmask(
        'aiprovider_openrouter/apikey',
        new lang_string('apikey', 'aiprovider_openrouter'),
        new lang_string('apikey_desc', 'aiprovider_openrouter'),
        '',
    ));

    // Setting to store HTTP-Referer header value (required by OpenRouter).
    $settings->add(new admin_setting_configtext(
        'aiprovider_openrouter/httpreferer',
        new lang_string('httpreferer', 'aiprovider_openrouter'),
        new lang_string('httpreferer_desc', 'aiprovider_openrouter'),
        $CFG->wwwroot ?? '',
        PARAM_URL,
    ));

    // Setting to store X-Title header value (recommended by OpenRouter).
    $settings->add(new admin_setting_configtext(
        'aiprovider_openrouter/xtitle',
        new lang_string('xtitle', 'aiprovider_openrouter'),
        new lang_string('xtitle_desc', 'aiprovider_openrouter'),
        get_config('moodle', 'sitename') ?: 'Moodle',
        PARAM_TEXT,
    ));

    // Setting to enable/disable global rate limiting.
    $settings->add(new admin_setting_configcheckbox(
        'aiprovider_openrouter/enableglobalratelimit',
        new lang_string('enableglobalratelimit', 'aiprovider_openrouter'),
        new lang_string('enableglobalratelimit_desc', 'aiprovider_openrouter'),
        0,
    ));

    // Setting to set how many requests per hour are allowed for the global rate limit.
    // Should only be enabled when global rate limiting is enabled.
    $settings->add(new admin_setting_configtext(
        'aiprovider_openrouter/globalratelimit',
        new lang_string('globalratelimit', 'aiprovider_openrouter'),
        new lang_string('globalratelimit_desc', 'aiprovider_openrouter'),
        100,
        PARAM_INT,
    ));
    $settings->hide_if('aiprovider_openrouter/globalratelimit', 'aiprovider_openrouter/enableglobalratelimit', 'eq', 0);

    // Setting to enable/disable user rate limiting.
    $settings->add(new admin_setting_configcheckbox(
        'aiprovider_openrouter/enableuserratelimit',
        new lang_string('enableuserratelimit', 'aiprovider_openrouter'),
        new lang_string('enableuserratelimit_desc', 'aiprovider_openrouter'),
        0,
    ));

    // Setting to set how many requests per hour are allowed for the user rate limit.
    // Should only be enabled when user rate limiting is enabled.
    $settings->add(new admin_setting_configtext(
        'aiprovider_openrouter/userratelimit',
        new lang_string('userratelimit', 'aiprovider_openrouter'),
        new lang_string('userratelimit_desc', 'aiprovider_openrouter'),
        10,
        PARAM_INT,
    ));
    $settings->hide_if('aiprovider_openrouter/userratelimit', 'aiprovider_openrouter/enableuserratelimit', 'eq', 0);
}
