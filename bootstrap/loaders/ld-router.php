<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
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
 */


use Catalyst\Framework\Core\Middleware\CsrfMiddleware;
use Catalyst\Framework\Core\Middleware\DebugMiddleware;
use Catalyst\Framework\Core\Middleware\RequestThrottlingMiddleware;
use Catalyst\Framework\Core\Middleware\SecurityHeadersMiddleware;
use Catalyst\Framework\Core\Route\Router;
use Catalyst\Helpers\Log\Logger;


if (!defined('INITIALIZED_ROUTER')) {

    try {

        $router = Router::getInstance();

        Logger::getInstance()->debug('Router initialization started');

        $globalMiddleware = [];

        if (IS_PRODUCTION) {
            $globalMiddleware[] = SecurityHeadersMiddleware::class;
            $globalMiddleware[] = RequestThrottlingMiddleware::class;
            $globalMiddleware[] = CsrfMiddleware::class;
        } else {
            $globalMiddleware[] = SecurityHeadersMiddleware::class;
            $globalMiddleware[] = RequestThrottlingMiddleware::class;
            $globalMiddleware[] = CsrfMiddleware::class;
            $globalMiddleware[] = DebugMiddleware::class;
        }

        foreach ($globalMiddleware as $middleware) {
            if (class_exists($middleware)) {
                $router->addMiddleware($middleware);
                Logger::getInstance()->debug('Global middleware added', ['middleware' => $middleware]);
            }
        }

        $routeFiles = [
            realpath(implode(DS, [PD, 'bootstrap', 'routes', 'web.php'])),
            //realpath(implode(DS, [PD, 'bootstrap', 'routes', 'api.php'])),
            //realpath(implode(DS, [PD, 'bootstrap', 'routes', 'admin.php'])),
        ];

        foreach ($routeFiles as $routeFile) {
            if (file_exists($routeFile)) {
                Logger::getInstance()->debug('Loading route file', ['file' => $routeFile]);
                require_once $routeFile;
            }
        }

        if (IS_PRODUCTION && !file_exists(implode(DS, [PD, 'cache', 'routes.cache.php']))) {
            $router->cacheRoutes();
            Logger::getInstance()->info('Routes cached for production');
        }

        Logger::getInstance()->debug('Router initialization completed');

    } catch (Exception $e) {
        // Log error during router initialization
        if (class_exists('\\Catalyst\\Helpers\\Log\\Logger')) {
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

    define('INITIALIZED_ROUTER', true);
}