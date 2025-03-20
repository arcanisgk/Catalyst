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

use Catalyst\Framework\Core\Middleware\DebugMiddleware;
use Catalyst\Framework\Core\Middleware\RequestThrottlingMiddleware;
use Catalyst\Framework\Core\Middleware\SecurityHeadersMiddleware;
use Catalyst\Framework\Core\Route\Router;
use App\Assets\Helpers\Log\Logger;

// Prevent duplicate execution
if (defined('INIT_ROUTER_EXECUTED')) {
    return;
} else {
    define('INIT_ROUTER_EXECUTED', true);
}

try {
    // Get router singleton instance
    $router = Router::getInstance();

    // Log router initialization
    Logger::getInstance()->debug('Router initialization started');

    // Load route definitions from route files
    $routeFiles = [

        // Main routes file is required
        PD . DS . 'app' . DS . 'Assets' . DS . 'resources' . DS . 'routes' . DS . 'web.php',

        // API routes are optional
        PD . DS . 'app' . DS . 'Assets' . DS . 'resources' . DS . 'routes' . DS . 'api.php',

        // Admin routes are optional
        PD . DS . 'app' . DS . 'Assets' . DS . 'resources' . DS . 'routes' . DS . 'admin.php',
    ];

    // Load each route file if it exists
    foreach ($routeFiles as $routeFile) {
        if (file_exists($routeFile)) {
            Logger::getInstance()->debug('Loading route file', ['file' => $routeFile]);
            require_once $routeFile;
        }
    }

    // Set up global middleware - these will be applied to all routes
    $globalMiddleware = [];

    // Add middleware based on environment and configuration
    if (IS_PRODUCTION) {
        // Production middleware
        $globalMiddleware[] = SecurityHeadersMiddleware::class;
        $globalMiddleware[] = RequestThrottlingMiddleware::class;
    } else {
        // Development middleware
        $globalMiddleware[] = DebugMiddleware::class;
    }


    // Apply global middleware to router
    foreach ($globalMiddleware as $middleware) {
        if (class_exists($middleware)) {
            $router->addMiddleware($middleware);
            Logger::getInstance()->debug('Global middleware added', ['middleware' => $middleware]);
        }
    }

    // Cache routes in production if not already cached
    if (IS_PRODUCTION && !file_exists(PD . DS . 'cache' . DS . 'routes.cache.php')) {
        $router->cacheRoutes();
        Logger::getInstance()->info('Routes cached for production');
    }

    Logger::getInstance()->debug('Router initialization completed');

} catch (Exception $e) {
    // Log error during router initialization
    if (class_exists('\\App\\Assets\\Helpers\\Log\\Logger')) {
        Logger::getInstance()->error('Router initialization failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    // Only display error in development mode
    if (IS_DEVELOPMENT) {
        echo "Router initialization error: " . $e->getMessage();
    }
}
