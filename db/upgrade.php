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
 * Upgrade script for the OpenRouter AI provider.
 *
 * @package    aiprovider_openrouter
 * @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute OpenRouter AI provider upgrades.
 *
 * @param int $oldversion The currently installed version.
 * @return bool
 */
function xmldb_aiprovider_openrouter_upgrade(int $oldversion): bool {
    if ($oldversion < 2024100701) {
        $legacyorgid = get_config('aiprovider_openrouter', 'orgid');
        $httpreferer = get_config('aiprovider_openrouter', 'httpreferer');
        if (!empty($legacyorgid) && empty($httpreferer)) {
            set_config('httpreferer', $legacyorgid, 'aiprovider_openrouter');
        }
        upgrade_plugin_savepoint(true, 2024100701, 'aiprovider', 'openrouter');
    }

    if ($oldversion < 2025041400) {
        upgrade_plugin_savepoint(true, 2025041400, 'aiprovider', 'openrouter');
    }

    return true;
}
