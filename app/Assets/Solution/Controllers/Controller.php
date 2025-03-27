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

namespace Catalyst\Solution\Controllers;

use Catalyst\Assets\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\JsonResponse;
use Catalyst\Framework\Core\Response\RedirectResponse;
use Catalyst\Framework\Core\Response\Response;
use Catalyst\Framework\Core\Response\ViewResponse;
use Catalyst\Framework\Core\Route\Router;
use Catalyst\Framework\Core\Session\FlashMessage;
use Catalyst\Framework\Core\Translation\TranslationManager;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Base Controller class with common functionality for route handlers
 *
 * Provides common methods and functionality for controller classes to
 * minimize code duplication and standardize controller responses.
 *
 * @package Catalyst\Solution\Controllers;
 */
abstract class Controller
{
    /**
     * The current request instance
     *
     * @var Request|null
     */
    protected ?Request $request = null;

    /**
     * The logger instance
     *
     * @var Logger|null
     */
    protected ?Logger $logger = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Set the current request instance
     *
     * @param Request $request Request instance
     * @return self For method chaining
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get the current request instance
     *
     * @return Request|null Request instance
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * Create a view response
     *
     * @param string $view View name
     * @param array $data View data
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return ViewResponse The view response
     */
    protected function view(
        string $view,
        array  $data = [],
        int    $status = 200,
        array  $headers = []
    ): ViewResponse
    {
        // Allow controllers to add request information to views
        if ($this->request) {
            // Add request data to view if needed
        }

        // Create and return ViewResponse instance
        // ViewFactory is used internally by ViewResponse
        return new ViewResponse($view, $data, $status, $headers);
    }

    /**
     * Create a view response with layout
     *
     * @param string $view View name
     * @param array $data View data
     * @param string $layout Layout name
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return ViewResponse The view response
     */
    protected function viewWithLayout(
        string $view,
        array  $data = [],
        string $layout = 'default',
        int    $status = 200,
        array  $headers = []
    ): ViewResponse
    {
        // Use the ViewResponse factory method which leverages ViewFactory internally
        return ViewResponse::withLayout($view, $data, $layout, $status, $headers);
    }

    /**
     * Create a JSON response
     *
     * @param mixed $data Data to include in response
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse The JSON response
     */
    protected function json(
        mixed $data = null,
        int   $status = 200,
        array $headers = []
    ): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Create a successful JSON response
     *
     * @param mixed $data Data to include in response
     * @param string|null $message Success message
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse The JSON response
     */
    protected function jsonSuccess(
        mixed   $data = null,
        ?string $message = null,
        int     $status = 200,
        array   $headers = []
    ): JsonResponse
    {
        return JsonResponse::api($data, true, $message, $status, $headers);
    }

    /**
     * Create an error JSON response
     *
     * @param string $message Error message
     * @param mixed $errors Error details
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse The JSON response
     */
    protected function jsonError(
        string $message,
        mixed  $errors = null,
        int    $status = 400,
        array  $headers = []
    ): JsonResponse
    {
        return JsonResponse::error($message, $errors, $status, $headers);
    }

    /**
     * Create a validation error JSON response
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return JsonResponse The JSON response
     */
    protected function jsonValidationError(
        array  $errors,
        string $message = 'Validation failed',
        int    $status = 422,
        array  $headers = []
    ): JsonResponse
    {
        return JsonResponse::validation($errors, $message, $status, $headers);
    }

    /**
     * Create a redirect response
     *
     * @param string $url URL to redirect to
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return RedirectResponse The redirect response
     */
    protected function redirect(
        string $url,
        int    $status = 302,
        array  $headers = []
    ): RedirectResponse
    {
        return Response::redirect($url, $status, $headers);
    }

    /**
     * Create a redirect response to a named route
     *
     * @param string $name Route name
     * @param array $parameters Route parameters
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return RedirectResponse The redirect response
     */
    protected function redirectToRoute(
        string $name,
        array  $parameters = [],
        int    $status = 302,
        array  $headers = []
    ): RedirectResponse
    {
        $url = Router::getInstance()->url($name, $parameters);
        return $this->redirect($url, $status, $headers);
    }

    /**
     * Get a translation for the given key
     *
     * @param string $key Translation key (format: group.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string The translated text
     * @throws Exception
     */
    protected function trans(string $key, array $replacements = [], ?string $language = null): string
    {
        try {
            return TranslationManager::getInstance()->get($key, $replacements, $language);
        } catch (Exception $e) {
            // Log the error
            $this->logger?->error('Translation error in controller', [
                'key' => $key,
                'controller' => static::class,
                'error' => $e->getMessage()
            ]);

            // Return the key as fallback
            return $key;
        }
    }

    /**
     * Get a translation with pluralization based on count
     *
     * @param string $key Translation key base
     * @param int $count Count for determining pluralization
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string The translated text
     * @throws Exception
     */
    protected function transChoice(string $key, int $count, array $replacements = [], ?string $language = null): string
    {
        try {
            return TranslationManager::getInstance()->choice($key, $count, $replacements, $language);
        } catch (Exception $e) {
            // Log the error
            $this->logger?->error('Translation choice error in controller', [
                'key' => $key,
                'count' => $count,
                'controller' => static::class,
                'error' => $e->getMessage()
            ]);

            // Return the key as fallback
            return $key;
        }
    }

    /**
     * Check if the request is an AJAX request
     *
     * @return bool True if an AJAX request
     */
    protected function isAjax(): bool
    {
        if ($this->request) {
            // Use request object if available
            $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
            $requestedWith = $headers['X-Requested-With'] ?? ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
            return strtolower($requestedWith) === 'xmlhttprequest';
        }
        return false;
    }

    /**
     * Check if request expects a JSON response
     *
     * @return bool True if JSON expected
     */
    protected function expectsJson(): bool
    {
        if ($this->request) {
            // Check X-Requested-With header (standard AJAX indicator)
            $requestedWith = $this->request->getHeaders('X-Requested-With');
            if ($requestedWith && strtolower($requestedWith) === 'xmlhttprequest') {
                return true;
            }

            // Check Accept header for application/json
            $accept = $this->request->getHeaders('Accept');
            if ($accept && str_contains($accept, 'application/json')) {
                return true;
            }

            // Check Content-Type for application/json (indicates JSON request body)
            $contentType = $this->request->getHeaders('Content-Type');
            if ($contentType && str_contains($contentType, 'application/json')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log an informational message
     *
     * @param string $message Message to log
     * @param array $context Context data
     * @return void
     * @throws Exception
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->logger?->info($message, array_merge([
            'controller' => static::class
        ], $context));
    }

    /**
     * Log an error message
     *
     * @param string $message Message to log
     * @param array $context Context data
     * @return void
     * @throws Exception
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->logger?->error($message, array_merge([
            'controller' => static::class
        ], $context));
    }

    /**
     * Log a debug message
     *
     * @param string $message Message to log
     * @param array $context Context data
     * @return void
     * @throws Exception
     */
    protected function logDebug(string $message, array $context = []): void
    {
        $this->logger?->debug($message, array_merge([
            'controller' => static::class
        ], $context));
    }

    /**
     * Log a warning message
     *
     * @param string $message Message to log
     * @param array $context Context data
     * @return void
     * @throws Exception
     */
    protected function logWarning(string $message, array $context = []): void
    {
        $this->logger?->warning($message, array_merge([
            'controller' => static::class
        ], $context));
    }

    /**
     * Get the flash message helper
     *
     * @return FlashMessage The flash message helper
     */
    protected function flash(): FlashMessage
    {
        return new FlashMessage();
    }

    /**
     * Add a success flash message
     *
     * @param string $message The message to flash
     * @return self For method chaining
     */
    protected function flashSuccess(string $message): self
    {
        $this->flash()->success($message);
        return $this;
    }

    /**
     * Add an error flash message
     *
     * @param string $message The message to flash
     * @return self For method chaining
     */
    protected function flashError(string $message): self
    {
        $this->flash()->error($message);
        return $this;
    }

    /**
     * Add a warning flash message
     *
     * @param string $message The message to flash
     * @return self For method chaining
     */
    protected function flashWarning(string $message): self
    {
        $this->flash()->warning($message);
        return $this;
    }

    /**
     * Add an info flash message
     *
     * @param string $message The message to flash
     * @return self For method chaining
     */
    protected function flashInfo(string $message): self
    {
        $this->flash()->info($message);
        return $this;
    }

    /**
     * Crea una respuesta API estandarizada
     *
     * @param bool $success Indica si la operación fue exitosa
     * @param string $message Mensaje para el usuario
     * @param mixed $data Datos adicionales a incluir
     * @param int $status Código HTTP
     * @param array $headers Cabeceras HTTP
     * @return JsonResponse Respuesta JSON estandarizada
     */
    protected function apiResponse(
        bool   $success,
        string $message,
        mixed  $data = null,
        int    $status = 200,
        array  $headers = [],
        bool   $noFlash = true    // Default to true to avoid duplicates
    ): JsonResponse
    {
        // Only save flash message if explicitly requested for API requests
        if (!$this->expectsJson() || !$noFlash) {
            if ($success) {
                $this->flashSuccess($message);
            } else {
                $this->flashError($message);
            }
        }

        return new JsonResponse([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'noFlash' => $noFlash
        ], $status, $headers);
    }
}