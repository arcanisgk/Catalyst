<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

namespace Catalyst\Assets\Framework\Core\Http;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Request class for handling HTTP request data
 *
 * Provides methods for accessing and sanitizing request data
 * from various sources ($_GET, $_POST, $_REQUEST, etc.)
 *
 * @package Catalyst\Helpers\Http;
 */
class Request
{
    use SingletonTrait;

    /**
     * @var array
     */
    private array $get = [];

    /**
     * @var array
     */
    private array $post = [];

    /**
     * @var array
     */
    private array $cookie = [];

    /**
     * @var array
     */
    private array $files = [];

    /**
     * @var array
     */
    private array $server = [];

    /**
     * @var string|null
     */
    private ?string $inputContent = null;

    /**
     * @var string|mixed
     */
    private string $requestMethod;

    /**
     * @var string|mixed
     */
    private string $contentType;

    /**
     * Constructor
     * @throws Exception
     */
    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $this->cleanSuperGlobals();
        $this->parseInputContent();
    }

    /**
     * Clean and store superglobal values
     *
     * @return self
     * @throws Exception
     */
    public function cleanSuperGlobals(): self
    {
        // Store original values in protected properties
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->cookie = $_COOKIE ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];

        // Sanitize all GET parameters
        foreach ($this->get as $key => $value) {
            $this->get[$key] = $this->sanitizeInput($value);
        }

        // Sanitize all POST parameters
        foreach ($this->post as $key => $value) {
            $this->post[$key] = $this->sanitizeInput($value);
        }

        // Only log in development for debugging purposes
        if (IS_DEVELOPMENT) {
            Logger::getInstance()->debug('Request parameters processed', [
                'method' => $this->requestMethod,
                'get_count' => count($this->get),
                'post_count' => count($this->post)
            ]);
        }

        return $this;
    }

    /**
     * Parse the raw input content based on content type
     *
     * @return self
     */
    public function parseInputContent(): self
    {
        $this->inputContent = file_get_contents('php://input');

        // If content type is JSON, parse it
        if (str_contains($this->contentType, 'application/json')) {
            $jsonData = json_decode($this->inputContent, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                foreach ($jsonData as $key => $value) {
                    $this->post[$key] = $this->sanitizeInput($value);
                }
            }
        }

        return $this;
    }

    /**
     * Get request method
     *
     * @return string Request method (GET, POST, etc.)
     */
    public function getMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * Get value from GET parameters
     *
     * @param string $key Parameter name
     * @param mixed $default Default value if parameter doesn't exist
     * @return mixed Parameter value or default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get value from POST parameters
     *
     * @param string $key Parameter name
     * @param mixed $default Default value if parameter doesn't exist
     * @return mixed Parameter value or default
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get all GET parameters
     *
     * @return array All GET parameters
     */
    public function getAllGet(): array
    {
        return $this->get;
    }

    /**
     * Get all POST parameters
     *
     * @return array All POST parameters
     */
    public function getAllPost(): array
    {
        return $this->post;
    }

    /**
     * Get raw input content
     *
     * @return string|null Raw input content
     */
    public function getContent(): ?string
    {
        return $this->inputContent;
    }

    /**
     * Sanitize input recursively
     *
     * @param mixed $input Input to sanitize
     * @return mixed Sanitized input
     */
    private function sanitizeInput(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map(function ($value) {
                return $this->sanitizeInput($value);
            }, $input);
        } elseif (is_string($input)) {
            // Basic sanitization - you may want to enhance this
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }

        return $input;
    }

    /**
     * Get the current request URI
     *
     * @return string Request URI
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}
