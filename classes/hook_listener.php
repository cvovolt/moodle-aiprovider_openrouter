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

namespace aiprovider_openrouter;

use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for OpenRouter provider.
 *
 * @package    aiprovider_openrouter
 * @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the OpenRouter instance setup form.
     *
     * @param after_ai_provider_form_hook $hook
     */
    public static function set_form_definition_for_aiprovider_openrouter(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_openrouter') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'aiprovider_openrouter'),
            ['size' => 75],
        );
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');
        $mform->addElement(
            'static',
            'apikey_help',
            '',
            get_string('apikey_desc', 'aiprovider_openrouter'),
        );

        $mform->addElement(
            'text',
            'httpreferer',
            get_string('httpreferer', 'aiprovider_openrouter'),
            ['size' => 60],
        );
        $mform->setType('httpreferer', PARAM_URL);
        $mform->addElement(
            'static',
            'httpreferer_help',
            '',
            get_string('httpreferer_desc', 'aiprovider_openrouter'),
        );

        $mform->addElement(
            'text',
            'xtitle',
            get_string('xtitle', 'aiprovider_openrouter'),
            ['size' => 60],
        );
        $mform->setType('xtitle', PARAM_TEXT);
        $mform->addElement(
            'static',
            'xtitle_help',
            '',
            get_string('xtitle_desc', 'aiprovider_openrouter'),
        );
    }
}
