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

namespace Catalyst;

use Catalyst\Framework\Core\Response;
use Catalyst\Framework\Core\Route\Router;
use Catalyst\Framework\Exceptions\MethodNotAllowedException;
use Catalyst\Framework\Exceptions\RouteNotFoundException;
use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Http\Request;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**
 * Kernel - Core application bootstrapper
 *
 * Responsible for initializing, configuring and running
 * the Catalyst PHP Framework application.
 *
 * @package App;
 */
class Kernel
{
    use SingletonTrait;

    /**
     * @var bool Flag indicating if the framework has been bootstrapped
     */
    protected bool $bootstrapped = false;

    /**
     * @var Logger The logger instance
     */
    protected Logger $logger;

    /**
     * @var Request The HTTP request instance
     */
    protected Request $request;

    /**
     * Protected constructor to prevent creating a new instance of the Kernel
     * Use getInstance() instead
     */
    public function __construct()
    {
        // The constructor is intentionally minimal as most initialization
        // happens in the bootstrap method
    }

    /**
     * Bootstrap the framework
     *
     * Initializes all core components and prepares the application to run
     *
     * @return self
     * @throws Exception
     */
    public function bootstrap(): self
    {
        if ($this->bootstrapped) {
            return $this;
        }

        try {
            // Get references to already initialized components
            $this->logger = Logger::getInstance();
            $this->request = Request::getInstance();

            // Log bootstrap start
            $this->logger->info('Kernel bootstrap started', [
                'environment' => defined('GET_ENVIRONMENT') ? GET_ENVIRONMENT : 'unknown',
            ]);

            // Initialize router (load the router initialization script)
            require_once PD . DS . 'app' . DS . 'Assets' . DS . 'resources' . DS . 'loaders' . DS . 'init-router.php';

            // Initialize session handler (when implemented)
            // $this->session = Session::getInstance();

            // Initialize database connection (when implemented)
            // $this->database = Database::getInstance();

            // Initialize view engine (when implemented)
            // $this->view = View::getInstance();

            // Register shutdown function
            register_shutdown_function([$this, 'handleShutdown']);

            // Mark as bootstrapped
            $this->bootstrapped = true;

            // Log bootstrap completion
            $this->logger->info('Kernel bootstrap completed');

            return $this;
        } catch (Exception $e) {
            $this->logger->error('Kernel bootstrap failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Run the application
     *
     * Process the request and generate a response
     *
     * @return void
     * @throws Exception If the application hasn't been bootstrapped
     */
    public function run(): void
    {
        if (!$this->bootstrapped) {
            throw new Exception('Application must be bootstrapped before running');
        }

        try {
            $this->logger->info('Application execution started');

            // Get the router instance
            $router = Router::getInstance();

            // Dispatch the request through the router
            $response = $router->dispatch($this->request);

            // Send response to client
            $response->send();

        } catch (RouteNotFoundException $e) {
            // Handle 404 errors
            $this->logger->warning('Route not found', [
                'uri' => $this->request->getUri(),
                'method' => $this->request->getMethod()
            ]);

            // Show 404 page
            $this->handle404Error($e);

        } catch (MethodNotAllowedException $e) {
            // Handle 405 errors
            $this->logger->warning('Method not allowed', [
                'uri' => $this->request->getUri(),
                'method' => $this->request->getMethod(),
                'allowed_methods' => $e->getAllowedMethods()
            ]);

            // Send 405 response with allowed methods header
            $this->handle405Error($e);

        } catch (Exception $e) {
            $this->logger->error('Application execution failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle exceptions (to be improved)
            if (IS_DEVELOPMENT) {
                // Show detailed error in development
                throw $e;
            } else {
                // Show user-friendly error in production
                $this->showErrorPage($e);
            }
        }
    }

    /**
     * Handle a 404 Not Found error
     *
     * @param RouteNotFoundException $e The exception
     * @return void
     */
    protected function handle404Error(RouteNotFoundException $e): void
    {
        if (IS_DEVELOPMENT) {
            // Show detailed error in development
            $content = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <title>Page Not Found</title>
                <style>
                    body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; color: #333; }
                    .container { max-width: 800px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 10px; }
                    h1 { color: #d23d24; }
                    .message { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; }
                    .uri { font-family: monospace; background: #eee; padding: 10px; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>404 - Page Not Found</h1>
                    <div class="message">' . htmlspecialchars($e->getMessage()) . '</div>
                    <p>The requested URL was: <span class="uri">' . htmlspecialchars($this->request->getUri()) . '</span></p>
                    <p>Check that the URL is correct or go back to the <a href="/">homepage</a>.</p>
                </div>
            </body>
            </html>';

            Response::notFound($content)->send();
        } else {
            // Simple error page in production
            Response::notFound($this->getProductionErrorContent('Page Not Found'))->send();
        }
    }

    /**
     * Handle a 405 Method Not Allowed error
     *
     * @param MethodNotAllowedException $e The exception
     * @return void
     */
    protected function handle405Error(MethodNotAllowedException $e): void
    {
        $allowedMethods = $e->getAllowedMethods();

        if (IS_DEVELOPMENT) {
            // Show detailed error in development
            $content = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <title>Method Not Allowed</title>
                <style>
                    body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; color: #333; }
                    .container { max-width: 800px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 10px; }
                    h1 { color: #d23d24; }
                    .message { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; }
                    .uri { font-family: monospace; background: #eee; padding: 10px; border-radius: 5px; }
                    .methods { font-family: monospace; background: #d1e7dd; color: #0f5132; padding: 5px 10px; border-radius: 3px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>405 - Method Not Allowed</h1>
                    <div class="message">' . htmlspecialchars($e->getMessage()) . '</div>
                    <p>The requested URL was: <span class="uri">' . htmlspecialchars($this->request->getUri()) . '</span></p>
                    <p>Request method: <strong>' . htmlspecialchars($this->request->getMethod()) . '</strong></p>
                    <p>Allowed methods: ' . implode(', ', array_map(function ($method) {
                    return '<span class="methods">' . htmlspecialchars($method) . '</span>';
                }, $allowedMethods)) . '</p>
                </div>
            </body>
            </html>';

            Response::methodNotAllowed($allowedMethods, $content)->send();
        } else {
            // Simple error page in production
            Response::methodNotAllowed($allowedMethods, $this->getProductionErrorContent('Method Not Allowed'))->send();
        }
    }

    /**
     * Handle application shutdown
     *
     * Performs cleanup operations when the application terminates
     *
     * @return void
     * @throws Exception
     */
    public function handleShutdown(): void
    {
        // Check for fatal errors
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->logger->error('Fatal error during execution', [
                'error' => $error
            ]);
        }

        // Close database connections (when implemented)
        // $this->database->disconnect();

        // Perform other cleanup tasks

        $this->logger->info('Application shutdown completed');
    }

    /**
     * Display welcome page during development
     *
     * @return void
     */
    protected function showWelcomePage(): void
    {
        // This is a placeholder. Eventually you might want to create a proper welcome view.
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Welcome to Catalyst Framework</title>
            <style>
                body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; color: #333; }
                .container { max-width: 800px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 10px; }
                h1 { color: #d23d24; }
                p { line-height: 1.6; }
                .version { color: #777; font-size: 0.9em; }
                .footer { margin-top: 30px; font-size: 0.8em; color: #777; text-align: center; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Welcome to Catalyst Framework</h1>
                <p>Congratulations! Your application is running successfully.</p>
                <p class="version">PHP Version: ' . PHP_VERSION . ' | Catalyst Version: 1.0.0</p>
                <p>To start building your application:</p>
                <ol>
                    <li>Set your project configuration</li>
                    <li>Set your enterprise information</li>
                    <li>Click and Go Home</li>
                </ol>
                                <p>For documentation and more information, visit <a href="https://catalyst.lh-2.net">catalyst.lh-2.net</a>.</p>
                <div class="footer">© ' . date('Y') . ' Catalyst Framework</div>
            </div>
        </body>
        </html>';
    }

    /**
     * Display error page
     *
     * @param Exception $exception The exception that occurred
     * @return void
     */
    protected function showErrorPage(Exception $exception): void
    {
        // Simple error page. This could be improved to use a view template.
        http_response_code(500);
        echo $this->getProductionErrorContent('Application Error');
    }

    /**
     * Get generic error content for production environment
     *
     * @param string $title Error title
     * @return string HTML content
     */
    protected function getProductionErrorContent(string $title): string
    {
        return '<!DOCTYPE html>
        <html lang="en">
        <head>
            <title>' . htmlspecialchars($title) . '</title>
            <style>
                body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; color: #333; }
                .container { max-width: 800px; margin: 0 auto; background: #f9f9f9; padding: 30px; border-radius: 10px; }
                h1 { color: #d23d24; }
                .error-code { color: #777; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>' . htmlspecialchars($title) . '</h1>
                <p>Sorry, an error occurred while processing your request.</p>
                <p class="error-code">Error ID: ' . uniqid('err-') . '</p>
            </div>
        </body>
        </html>';
    }
}
