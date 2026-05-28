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

namespace aiprovider_openrouter\form;

/**
 * Generate/summarise text action settings form for OpenRouter provider.
 *
 * @package    aiprovider_openrouter
 * @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_generate_text_form extends action_form {
    #[\Override]
    protected function definition(): void {
        parent::definition();
        $mform = $this->_form;

        $mform->addElement(
            'text',
            'model',
            get_string("action:{$this->actionname}:model", 'aiprovider_openrouter'),
            'maxlength="255" size="40"',
        );
        $mform->setType('model', PARAM_TEXT);
        $mform->addRule('model', null, 'required', null, 'client');
        $mform->setDefault('model', $this->actionconfig['model'] ?? 'openrouter/auto');

        $mform->addElement(
            'text',
            'endpoint',
            get_string("action:{$this->actionname}:endpoint", 'aiprovider_openrouter'),
            'maxlength="255" size="60"',
        );
        $mform->setType('endpoint', PARAM_URL);
        $mform->addRule('endpoint', null, 'required', null, 'client');
        $mform->setDefault('endpoint', $this->actionconfig['endpoint'] ?? 'https://openrouter.ai/api/v1/chat/completions');

        $mform->addElement(
            'textarea',
            'systeminstruction',
            get_string("action:{$this->actionname}:systeminstruction", 'aiprovider_openrouter'),
            'wrap="virtual" rows="5" cols="60"',
        );
        $mform->setType('systeminstruction', PARAM_TEXT);
        $defaultinstruction = !empty($this->actionconfig['systeminstruction'])
            ? $this->actionconfig['systeminstruction']
            : $this->action::get_system_instruction();
        $mform->setDefault('systeminstruction', $defaultinstruction);

        if ($this->returnurl) {
            $mform->addElement('hidden', 'returnurl', $this->returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_TEXT);

        $mform->addElement('hidden', 'provider', $this->providername);
        $mform->setType('provider', PARAM_TEXT);

        $mform->addElement('hidden', 'providerid', $this->providerid);
        $mform->setType('providerid', PARAM_INT);

        $this->set_data($this->actionconfig);
    }
}
