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
 * Strings for component aiprovider_openrouter, language 'en'.
 *
 * @package    aiprovider_openrouter
* @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
* @copyright based on work by 2024 Matt Porritt <matt.porritt@moodle.com> aiprovider_openai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:generate_image:endpoint'] = 'API endpoint';
$string['action:generate_image:model'] = 'AI model';
$string['action:generate_image:model_desc'] = 'The model used to generate images from user prompts.';
$string['action:generate_text:endpoint'] = 'API endpoint';
$string['action:generate_text:model'] = 'AI model';
$string['action:generate_text:model_desc'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_desc'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:endpoint'] = 'API endpoint';
$string['action:summarise_text:model'] = 'AI model';
$string['action:summarise_text:model_desc'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_desc'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'OpenRouter API key';
$string['apikey_desc'] = 'Generate an API key from <a href="https://openrouter.ai/keys">OpenRouter</a> and paste it here to enable requests. The key must have access to the models you configure below.';
$string['enableglobalratelimit'] = 'Set site-wide rate limit';
$string['enableglobalratelimit_desc'] = 'Limit the number of requests that the OpenRouter API provider can receive across the entire site every hour.';
$string['enableuserratelimit'] = 'Set user rate limit';
$string['enableuserratelimit_desc'] = 'Limit the number of requests each user can make to the OpenRouter API provider every hour.';
$string['globalratelimit'] = 'Maximum number of site-wide requests';
$string['globalratelimit_desc'] = 'The number of site-wide requests allowed per hour.';
$string['globalratelimitexceeded'] = 'Global rate limit exceeded';
$string['httpreferer'] = 'HTTP-Referer header';
$string['httpreferer_desc'] = 'OpenRouter requires requests to include the HTTP-Referer header that identifies your site. Use a fully qualified URL such as https://example.com.';
$string['invalidaspectratio'] = 'Invalid aspect ratio: {$a}';
$string['pluginname'] = 'OpenRouter API provider';
$string['privacy:metadata'] = 'The OpenRouter API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_openrouter:externalpurpose'] = 'This information is sent to the OpenRouter API in order for a response to be generated. Your OpenRouter account settings may change how OpenRouter stores and retains this data. No user data is explicitly sent to OpenRouter or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_openrouter:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_openrouter:numberimages'] = 'When generating images the number of images used in the response.';
$string['privacy:metadata:aiprovider_openrouter:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['privacy:metadata:aiprovider_openrouter:responseformat'] = 'When generating images the format of the response.';
$string['userratelimit'] = 'Maximum number of requests per user';
$string['userratelimit_desc'] = 'The number of requests allowed per hour, per user.';
$string['userratelimitexceeded'] = 'User rate limit exceeded';
$string['xtitle'] = 'X-Title header';
$string['xtitle_desc'] = 'Optional: Send a friendly name for your site in the X-Title header. This appears in the OpenRouter dashboard.';
