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

use GuzzleHttp\Psr7\Request;

/**
 * Test OpenRouter provider methods.
 *
 * @package    aiprovider_openrouter
* @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
* @copyright based on work by 2024 Matt Porritt <matt.porritt@moodle.com> aiprovider_openai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_openrouter\provider
 */
final class provider_test extends \advanced_testcase {
    /**
     * Test get_action_list
     */
    public function test_get_action_list(): void {
        $provider = new provider();
        $actionlist = $provider->get_action_list();
        $this->assertIsArray($actionlist);
        $this->assertCount(3, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_image::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
    }

    /**
     * Test generate_userid.
     */
    public function test_generate_userid(): void {
        $provider = new provider();
        $userid = $provider->generate_userid(1);

        // Assert that the generated userid is a string of proper length.
        $this->assertIsString($userid);
        $this->assertEquals(64, strlen($userid));
    }

    /**
     * Test add_authentication_headers.
     */
    public function test_add_authentication_headers(): void {
        $this->resetAfterTest();

        set_config('apikey', 'secret-key', 'aiprovider_openrouter');
        set_config('httpreferer', 'https://example.com', 'aiprovider_openrouter');
        set_config('xtitle', 'Example Moodle', 'aiprovider_openrouter');

        $provider = new provider();
        $request = new Request('GET', 'https://openrouter.ai/api/v1/chat/completions');
        $updatedrequest = $provider->add_authentication_headers($request);

        $this->assertEquals('Bearer secret-key', $updatedrequest->getHeaderLine('Authorization'));
        $this->assertEquals('https://example.com', $updatedrequest->getHeaderLine('HTTP-Referer'));
        $this->assertEquals('Example Moodle', $updatedrequest->getHeaderLine('X-Title'));
    }

    /**
     * Test is_request_allowed.
     */
    public function test_is_request_allowed(): void {
        $this->resetAfterTest();

        // Set plugin config rate limiter settings.
        set_config('enableglobalratelimit', 1, 'aiprovider_openrouter');
        set_config('globalratelimit', 5, 'aiprovider_openrouter');
        set_config('enableuserratelimit', 1, 'aiprovider_openrouter');
        set_config('userratelimit', 3, 'aiprovider_openrouter');

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $aspectratio = 'square';
        $quality = 'hd';
        $numimages = 1;
        $style = 'vivid';
        $action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style,
        );
        $provider = new provider();

        // Make 3 requests, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        // The 4th request for the same user should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(get_string('userratelimitexceeded', 'aiprovider_openrouter'), $result['errormessage']);

        // Change user id to make a request for a different user, should pass (4 requests for global rate).
        $action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: 2,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style,
        );
        $this->assertTrue($provider->is_request_allowed($action));

        // Make a 5th request for the global rate limit, it should be allowed.
        $this->assertTrue($provider->is_request_allowed($action));

        // The 6th request should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(get_string('globalratelimitexceeded', 'aiprovider_openrouter'), $result['errormessage']);
    }

    /**
     * Test is_provider_configured.
     */
    public function test_is_provider_configured(): void {
        $this->resetAfterTest();

        // No configured values.
        $provider = new \aiprovider_openrouter\provider();
        $this->assertFalse($provider->is_provider_configured());

        // Properly configured values.
        set_config('apikey', '123', 'aiprovider_openrouter');
        $provider = new \aiprovider_openrouter\provider();
        $this->assertTrue($provider->is_provider_configured());
    }
}
