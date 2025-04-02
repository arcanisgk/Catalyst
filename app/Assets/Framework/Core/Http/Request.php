<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
 * @see       https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2025
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Framework
 * @filesource
 *
 * @link      https://catalyst.dock Local development URL
 *
 * Request component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Http;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Request class for handling HTTP request data
 *
 * Provides methods for accessing and sanitizing request data
 * from various sources ($_GET, $_POST, $_REQUEST, etc.)
 *
 * @package Catalyst\Framework\Core\Http;
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

    /**
     * Get value from SERVER parameters
     *
     * @param string $key Parameter name
     * @param mixed $default Default value if parameter doesn't exist
     * @return mixed Parameter value or default
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Get all SERVER parameters
     *
     * @return array All SERVER parameters
     */
    public function getAllServer(): array
    {
        return $this->server;
    }

    /**
     * Get HTTP headers from the request
     *
     * @param string|null $name Specific header name to retrieve (optional)
     * @return array|string|null All headers or specific header value if name provided
     */
    public function getHeaders(?string $name = null): array|string|null
    {
        // Use apache_request_headers() if available
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            // Fallback to manual extraction from $_SERVER
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    // Convert HTTP_ACCEPT_LANGUAGE to Accept-Language
                    $headerName = str_replace('_', '-', substr($key, 5));
                    $headerName = ucwords(strtolower($headerName), '-');
                    $headers[$headerName] = $value;
                } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'])) {
                    // Special case for these headers which don't have HTTP_ prefix
                    $headerName = str_replace('_', '-', $key);
                    $headerName = ucwords(strtolower($headerName), '-');
                    $headers[$headerName] = $value;
                }
            }
        }

        // Normalize header names to have consistent capitalization
        $normalizedHeaders = [];
        foreach ($headers as $key => $value) {
            $normalizedKey = str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($key))));
            $normalizedHeaders[$normalizedKey] = $value;
        }

        // Return specific header if requested
        if ($name !== null) {
            $normalizedName = str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($name))));
            return $normalizedHeaders[$normalizedName] ?? null;
        }

        return $normalizedHeaders;
    }

    /**
     * Get the current domain of the application
     *
     * @param Request $request The current request
     * @return string The current domain
     */
    public function getCurrentDomain(Request $request): string
    {
        // Try to get domain from HTTP_HOST
        $host = $request->server('HTTP_HOST', '');

        // Remove port if present
        $domain = preg_replace('/:\d+$/', '', $host);

        // If empty, try SERVER_NAME
        if (empty($domain)) {
            $domain = $request->server('SERVER_NAME', '');
        }

        // If still empty, fallback to a default
        if (empty($domain)) {
            // You might want to define a default domain or throw an exception here
            $domain = 'localhost';
        }

        return $domain;
    }


    /**
     * Get the client IP address
     *
     * @param bool $trustProxy Whether to trust proxy headers (default: true)
     * @return string Client IP address
     */
    public function getClientIp(bool $trustProxy = true): string
    {
        // If we don't trust proxy headers, just return REMOTE_ADDR
        if (!$trustProxy) {
            return $this->server('REMOTE_ADDR', '0.0.0.0');
        }

        // Check for various proxy headers in order of reliability
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            $ip = $this->server($header);

            if ($ip) {
                // HTTP_X_FORWARDED_FOR can contain multiple IPs separated by commas
                // In this case, the first IP is the original client
                if ($header === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }

                // Validate IP format
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        // Default fallback
        return '0.0.0.0';
    }

}
