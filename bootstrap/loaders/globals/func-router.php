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


use Catalyst\Assets\Framework\Core\Exceptions\RouteNotFoundException;
use Catalyst\Framework\Core\Response\JsonResponse;
use Catalyst\Framework\Core\Response\RedirectResponse;
use Catalyst\Framework\Core\Response\Response;
use Catalyst\Framework\Core\Response\ViewResponse;
use Catalyst\Framework\Core\Route\Router;
use Random\RandomException;

if (!function_exists('route')) {
    /**
     * Generate a URL to a named route
     *
     * @param string $name The name of the route
     * @param array $parameters Parameters for the route
     * @param bool $absolute Whether to generate an absolute URL
     * @return string The generated URL
     * @throws RouteNotFoundException If the route doesn't exist
     */
    function route(string $name, array $parameters = [], bool $absolute = false): string
    {
        return Router::getInstance()->url($name, $parameters, $absolute);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response to the given URL
     *
     * @param string $url The URL to redirect to
     * @param int $status The HTTP status code (default: 302)
     * @param array $headers Additional headers
     * @return RedirectResponse
     */
    function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return Response::redirect($url, $status, $headers);
    }
}

if (!function_exists('redirect_to_route')) {
    /**
     * Create a redirect response to a named route
     *
     * @param string $name The name of the route
     * @param array $parameters Parameters for the route
     * @param int $status The HTTP status code
     * @param array $headers Additional headers
     * @return RedirectResponse
     * @throws RouteNotFoundException If the route doesn't exist
     */
    function redirect_to_route(
        string $name,
        array  $parameters = [],
        int    $status = 302,
        array  $headers = []
    ): RedirectResponse
    {
        $url = route($name, $parameters);
        return redirect($url, $status, $headers);
    }
}

if (!function_exists('route_is')) {
    /**
     * Determine if the current request URL matches a pattern
     *
     * @param string $pattern Pattern to check against
     * @return bool
     */
    function route_is(string $pattern): bool
    {
        $request = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        if (($pos = strpos($request, '?')) !== false) {
            $request = substr($request, 0, $pos);
        }

        // Simple wildcard matching
        $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
        return (bool)preg_match('/^' . $pattern . '$/', $request);
    }
}

if (!function_exists('view')) {
    /**
     * Create a view response
     *
     * @param string $view View name
     * @param array $data View data
     * @param int $status HTTP status code
     * @param array $headers Response headers
     * @return ViewResponse
     */
    function view(
        string $view,
        array  $data = [],
        int    $status = 200,
        array  $headers = []
    ): ViewResponse
    {
        return new ViewResponse($view, $data, $status, $headers);
    }
}

if (!function_exists('view_with_layout')) {
    /**
     * Create a view response with a layout
     *
     * @param string $view View name
     * @param array $data View data
     * @param string $layout Layout name
     * @param int $status HTTP status code
     * @param array $headers Response headers
     * @return ViewResponse
     */
    function view_with_layout(
        string $view,
        array  $data = [],
        string $layout = 'default',
        int    $status = 200,
        array  $headers = []
    ): ViewResponse
    {
        return ViewResponse::withLayout($view, $data, $layout, $status, $headers);
    }
}

if (!function_exists('json')) {
    /**
     * Create a JSON response
     *
     * @param mixed $data The data to encode as JSON
     * @param int $status The HTTP status code
     * @param array $headers Array of HTTP headers
     * @param int $options JSON encoding options
     * @return JsonResponse
     */
    function json(
        mixed $data = null,
        int   $status = 200,
        array $headers = [],
        int   $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}

if (!function_exists('json_success')) {
    /**
     * Create a JSON success response
     *
     * @param mixed $data The data payload
     * @param string|null $message Optional success message
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse
     */
    function json_success(
        mixed   $data = null,
        ?string $message = null,
        int     $status = 200,
        array   $headers = []
    ): JsonResponse
    {
        return JsonResponse::api($data, true, $message, $status, $headers);
    }
}

if (!function_exists('json_error')) {
    /**
     * Create a JSON error response
     *
     * @param string $message Error message
     * @param mixed $errors Detailed error information
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse
     */
    function json_error(
        string $message,
        mixed  $errors = null,
        int    $status = 400,
        array  $headers = []
    ): JsonResponse
    {
        return JsonResponse::error($message, $errors, $status, $headers);
    }
}

if (!function_exists('route_url')) {
    /**
     * Generate a URL by concatenating segments
     *
     * @param string ...$segments URL segments
     * @return string The generated URL
     */
    function route_url(string ...$segments): string
    {
        $url = '';
        foreach ($segments as $segment) {
            $segment = trim($segment, '/');
            $url .= ($segment ? "/$segment" : '');
        }

        return $url ?: '/';
    }
}

if (!function_exists('current_route_url')) {
    /**
     * Get the current URL
     *
     * @param bool $withQueryString Include query string
     * @return string Current URL
     */
    function current_route_url(bool $withQueryString = false): string
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';

        if (!$withQueryString && ($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve a CSRF token for the current session
     *
     * @return string The CSRF token
     * @throws RandomException
     */
    function csrf_token(): string
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generate a new token if one doesn't exist
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a hidden input field containing the CSRF token
     *
     * @return string HTML input element with CSRF token
     * @throws RandomException
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Verify that the provided token matches the stored CSRF token
     *
     * @param string|null $token The token to verify
     * @return bool Whether the token is valid
     */
    function csrf_verify(?string $token = null): bool
    {
        // Get token from request if not provided
        if ($token === null) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Compare tokens
        return $token !== null &&
            isset($_SESSION['_csrf_token']) &&
            hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for an asset file
     *
     * @param string $path Path to the asset file
     * @param bool $absolute Whether to return an absolute URL
     * @return string The asset URL
     */
    function asset(string $path, bool $absolute = false): string
    {
        // Remove leading slash if present
        $path = ltrim($path, '/');

        // Base path for assets
        $basePath = '/assets/';

        // Build the URL
        $url = $basePath . $path;

        // Add domain for absolute URLs
        if ($absolute) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url = $protocol . '://' . $host . $url;
        }

        return $url;
    }
}