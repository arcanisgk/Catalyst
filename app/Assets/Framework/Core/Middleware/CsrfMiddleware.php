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
 * CsrfMiddleware component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Middleware;

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\Response;
use Catalyst\Framework\Core\Response\JsonResponse;
use Catalyst\Helpers\Security\CsrfProtection;
use Closure;
use Exception;

class CsrfMiddleware extends CoreMiddleware
{
    /**
     * Routes that are exempt from CSRF validation
     */
    protected array $except = [
        // Add paths that don't need CSRF validation (like API webhooks)
        // '/api/webhooks/*'
    ];

    /**
     * Flag to track if validation has already been performed
     */
    private static bool $validationPerformed = false;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function process(Request $request, Closure $next): Response
    {

        // Only validate state-changing requests
        if ($this->shouldValidateRequest($request) && !self::$validationPerformed) {
            $token = $this->getTokenFromRequest($request);


            // Log para depuración
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
                $this->logger->debug('CSRF validation attempt', [
                    'token_received' => $token,
                    'validation_result' => $token && CsrfProtection::getInstance()->validateToken($token)
                ]);
            }

            if (!$token || !CsrfProtection::getInstance()->validateToken($token)) {
                $this->log('CSRF token validation failed', [
                    'ip' => $this->getClientIp(),
                    'uri' => $request->getUri(),
                    'method' => $request->getMethod()
                ]);

                // Return appropriate error response based on request type
                if ($this->expectsJson($request) || $this->isAjaxRequest($request)) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'CSRF token mismatch'
                    ], 403);
                }

                // Otherwise redirect back with error
                // This assumes you have a redirect response class
                // return new RedirectResponse('/error/403', ['error' => 'CSRF token verification failed']);

                // Simple response for now
                return new Response('CSRF token mismatch. Please try again.', 403);
            }
        }

        return $this->passToNext($request, $next);
    }

    /**
     * Determine if the request should be validated
     *
     * @param Request $request The request to check
     * @return bool
     */
    protected function shouldValidateRequest(Request $request): bool
    {
        // Only validate state-changing requests
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return false;
        }

        // Check if the request URI is in the except list
        $uri = $request->getUri();
        foreach ($this->except as $pattern) {
            if ($pattern === $uri) {
                return false;
            }

            // Handle wildcard patterns
            if (str_contains($pattern, '*')) {
                $pattern = str_replace('*', '.*', $pattern);
                if (preg_match('#^' . $pattern . '$#', $uri)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the CSRF token from the request
     *
     * @param Request $request The request
     * @return string|null The token or null if not found
     * @throws Exception
     */
    protected function getTokenFromRequest(Request $request): ?string
    {
        // Check POST parameter first
        $token = $request->post('csrf_token');
        if ($token) {
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
                $this->logger->debug('CSRF token found in POST', [
                    'token' => $token,
                    'session_tokens' => $_SESSION['catalyst_csrf_tokens'] ?? 'not_set'
                ]);
            }
            return $token;
        }

        // Check header next (for AJAX requests)
        $headers = $request->getHeaders();
        $token = $headers['X-CSRF-TOKEN'] ?? null;
        if ($token) {
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
                $this->logger->debug('CSRF token found in header', [
                    'token' => $token,
                    'session_tokens' => $_SESSION['catalyst_csrf_tokens'] ?? 'not_set'
                ]);
            }
            return $token;
        }

        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
            $this->logger->debug('No CSRF token found in request', [
                'post_params' => $request->getAllPost(),
                'headers' => $headers,
                'session_tokens' => $_SESSION['catalyst_csrf_tokens'] ?? 'not_set'
            ]);
        }

        return null;
    }

}
