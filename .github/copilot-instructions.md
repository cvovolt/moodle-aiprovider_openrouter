# OpenRouter AI Provider - Copilot Instructions

## Plugin Architecture

This is a **Moodle AI provider plugin** that integrates OpenRouter's API with Moodle 5.0+ Core AI framework. The plugin lives in `moodle/ai/provider/openrouter/` and follows Moodle's plugin structure.

**Core Components:**
- `classes/provider.php` - Main provider class extending `\core_ai\provider`. Handles authentication, rate limiting, and action registration.
- `classes/abstract_processor.php` - Base class for all action processors. Implements common API communication patterns.
- `classes/process_generate_text.php`, `process_generate_image.php`, `process_summarise_text.php` - Concrete processors for each AI action.

**Key Architecture Decisions:**
- **One processor per AI action**: Each AI capability (text generation, image generation, summarization) has its own processor class extending `abstract_processor`.
- **Template method pattern**: `abstract_processor` defines the API call flow; subclasses implement action-specific details (endpoint, model, request formatting).
- **User privacy**: User IDs are SHA256-hashed with site identifier before sending to OpenRouter (`generate_userid()` method).

## Moodle-Specific Conventions

### Component Naming
Plugin component is `aiprovider_openrouter` (follows pattern: `<plugintype>_<pluginname>`). Use this exact string for:
- Config calls: `get_config('aiprovider_openrouter', 'apikey')`
- Language strings: `get_string('pluginname', 'aiprovider_openrouter')`
- Database operations

### File Headers
Every PHP file requires Moodle GPL header with copyright attribution to both UMT and Matt Porritt:
```php
/**
 * @package    aiprovider_openrouter
 * @copyright  2025 e-Learning Team, Universiti Malaysia Terengganu <el@umt.edu.my>
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
```

### Namespaces
All classes use `namespace aiprovider_openrouter;` matching the component name. No subdirectories in namespace despite `classes/` folder.

## Configuration & Settings

Settings are per-action, allowing different models/endpoints for each capability:
- `action_generate_text_model` / `action_generate_text_endpoint` / `action_generate_text_systeminstruction`
- `action_generate_image_model` / `action_generate_image_endpoint`
- `action_summarise_text_model` / `action_summarise_text_endpoint` / `action_summarise_text_systeminstruction`

Default values in `settings.php` use `admin_settingspage_provider` (not standard `admin_settingpage`). Rate limiting uses Moodle's built-in `core_ai\rate_limiter`.

## Testing Patterns

Run tests from Moodle root: `vendor/bin/phpunit --testsuite aiprovider_openrouter_testsuite`

Alternative (direct path - may not work): `vendor/bin/phpunit ai/provider/openrouter/tests`

**Test Structure:**
- Use `\advanced_testcase` (not `\basic_testcase`) for tests requiring database resets
- Call `$this->resetAfterTest()` at test start when modifying config
- Fixture files in `tests/fixtures/` contain sample API responses (JSON)
- Load fixtures: `file_get_contents(self::get_fixture_path('aiprovider_openrouter', 'text_request_success.json'))`

**Testing Philosophy:**
- Mock HTTP responses using Guzzle's PSR-7 Response objects
- Test both success and error paths (see `handle_api_success()` / `handle_api_error()`)
- Verify authentication headers are correctly applied
- Check rate limiting logic (global and per-user)

**PHPUnit Setup (First Time):**
1. Install Composer dependencies: `composer install` (or use existing `vendor/bin/phpunit`)
2. Add to `config.php` before `require_once` line:
   ```php
   $CFG->phpunit_dataroot = '/tmp/moodledata_phpunit';
   $CFG->phpunit_prefix = 'zputor_';  // Must be ≤10 chars and unique
   ```
3. Initialize PHPUnit: `php admin/tool/phpunit/cli/init.php`
4. Run tests: `vendor/bin/phpunit --testsuite aiprovider_openrouter_testsuite`

**Common PHPUnit Issues:**
- **"db prefix too long"**: Prefix must be ≤10 characters including underscore
- **"Can not use database for testing"**: Prefix conflicts with existing tables - choose unique prefix or drop old test tables
- **"environment not initialised"**: Run `php admin/tool/phpunit/cli/init.php` after config changes
- **Plugin version mismatch**: Update `$plugin->requires` in `version.php` to match your Moodle version (e.g., 2024100700 for Moodle 4.5)

## API Integration

**OpenRouter-specific requirements:**
1. **Authorization**: `Bearer {apikey}` header
2. **HTTP-Referer**: Required by OpenRouter to identify requesting site
3. **X-Title**: Optional friendly name for dashboard display

Request construction in processors:
- Text actions: POST to `/chat/completions` with messages array (system + user roles)
- Image actions: POST to `/images/generations` with prompt, model, size, quality params
- All use Guzzle PSR-7 Request objects with empty URI (base_uri set in send options)

Response handling:
- 200 status → `handle_api_success()` parses response body
- Non-200 → `handle_api_error()` extracts error from `error.message` field
- Image responses download URL to Moodle draft file (`url_to_file()`)

## Common Pitfalls

1. **Config keys must match exactly**: `action_generate_text_model` not `generate_text_model`
2. **Empty URI in Request objects**: Set `base_uri` in `$client->send()` options, not Request constructor
3. **Message content normalization**: Text responses can be string OR array - use `normalise_message_content()` helper
4. **Rate limiter requires component name**: Use `\core\component::get_component_from_classname(get_class($this))`
5. **Version numbers**: Match plugin version in both `version.php` and upgrade function checks in `db/upgrade.php`
6. **Moodle version compatibility**: Set `$plugin->requires` to match target Moodle version (e.g., 2024100700 for 4.5+, 2025041400 for 5.0+)
7. **PHPUnit prefix length**: Database prefix in config must be ≤10 characters total
8. **Test environment reset**: After changing PHPUnit config, always run `php admin/tool/phpunit/cli/init.php` before tests

## Language Strings

All user-facing text in `lang/en/aiprovider_openrouter.php` using Moodle's lang string system. Action-specific strings follow pattern: `action:<actionname>:<setting>`.

## Development Workflow

1. Make changes to code
2. Run PHPUnit tests: `vendor/bin/phpunit ai/provider/openrouter/tests`
3. For config changes: Visit `Site administration → Notifications` to trigger upgrade
4. For new actions: Update `get_action_list()` in `provider.php` and create new processor class

When adding new processors, extend `abstract_processor` and implement:
- `get_endpoint()`, `get_model()`, `create_request_object()`, `handle_api_success()`
