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
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class process text generation.
 *
 * @package    aiprovider_openrouter
 * @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_text extends abstract_processor {
    #[\Override]
    protected function get_endpoint(): UriInterface {
        $settings = $this->provider->actionconfig[$this->action::class]['settings'] ?? [];
        return new Uri($settings['endpoint'] ?? 'https://openrouter.ai/api/v1/chat/completions');
    }

    #[\Override]
    protected function get_model(): string {
        $settings = $this->provider->actionconfig[$this->action::class]['settings'] ?? [];
        return $settings['model'] ?? 'openrouter/auto';
    }

    #[\Override]
    protected function get_system_instruction(): string {
        $settings = $this->provider->actionconfig[$this->action::class]['settings'] ?? [];
        return $settings['systeminstruction'] ?? $this->action::get_system_instruction();
    }

    #[\Override]
    protected function create_request_object(string $userid): RequestInterface {
        // Create the user object.
        $userobj = new \stdClass();
        $userobj->role = 'user';
        $userobj->content = $this->action->get_configuration('prompttext');

        // Create the request object.
        $requestobj = new \stdClass();
        $requestobj->model = $this->get_model();
        $requestobj->user = $userid;

        // If there is a system string available, use it.
        $systeminstruction = $this->get_system_instruction();
        if (!empty($systeminstruction)) {
            $systemobj = new \stdClass();
            $systemobj->role = 'system';
            $systemobj->content = $systeminstruction;
            $requestobj->messages = [$systemobj, $userobj];
        } else {
            $requestobj->messages = [$userobj];
        }

        return new Request(
            method: 'POST',
            uri: '',
            body: json_encode($requestobj),
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
    }

    /**
     * Normalise assistant message content to a plain string.
     *
     * @param object $message The message payload.
     * @return string
     */
    private function normalise_message_content(object $message): string {
        if (!empty($message->content) && is_string($message->content)) {
            return $message->content;
        }

        $content = $message->content ?? '';
        $textparts = [];

        // Handle array based message payloads.
        if (is_array($content)) {
            foreach ($content as $part) {
                if (is_string($part)) {
                    $textparts[] = $part;
                } else if (is_object($part)) {
                    if (isset($part->text) && is_string($part->text)) {
                        $textparts[] = $part->text;
                    } else if (isset($part->content) && is_string($part->content)) {
                        $textparts[] = $part->content;
                    }
                } else if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                    $textparts[] = $part['text'];
                }
            }
        } else if (is_object($content)) {
            if (isset($content->text) && is_string($content->text)) {
                $textparts[] = $content->text;
            } else if (isset($content->content) && is_string($content->content)) {
                $textparts[] = $content->content;
            }
        }

        if (!empty($textparts)) {
            return implode('', $textparts);
        }

        return is_scalar($content) ? (string) $content : '';
    }

    /**
     * Handle a successful response from the external AI api.
     *
     * @param ResponseInterface $response The response object.
     * @return array The response.
     */
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody->getContents());

        $choice = $bodyobj->choices[0] ?? new \stdClass();
        $message = $choice->message ?? new \stdClass();
        $generatedcontent = $this->normalise_message_content($message);
        $usage = $bodyobj->usage ?? new \stdClass();

        return [
            'success' => true,
            'id' => $bodyobj->id ?? '',
            'fingerprint' => $bodyobj->system_fingerprint ?? '',
            'generatedcontent' => $generatedcontent,
            'finishreason' => $choice->finish_reason ?? '',
            'prompttokens' => $usage->prompt_tokens ?? 0,
            'completiontokens' => $usage->completion_tokens ?? 0,
        ];
    }
}
