<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Kernel.php
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
 * Kernel component for the Catalyst Framework
 *
 */

namespace Catalyst;

use Catalyst\Framework\Core\Exceptions\MethodNotAllowedException;
use Catalyst\Framework\Core\Exceptions\RouteNotFoundException;
use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\Response;
use Catalyst\Framework\Core\Route\Router;
use Catalyst\Framework\Core\Session\SessionManager;
use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**
 * Kernel - Core application bootstrapper
 *
 * Responsible for initializing, configuring and running
 * the Catalyst PHP Framework application.
 *
 * @package   Catalyst
 * @subpackage Core
 * @version   1.0.0
 * @since     1.0.0
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

            SessionManager::getInstance()->init();

            // Initialize router (load the router initialization script)
            //app/bootstrap/loaders/ld-router.php
            //require_once realpath(implode(DS, [PD, 'app', 'bootstrap', 'loaders', 'ld-router.php']));
            //require_once PD . DS . 'app' . DS . 'Assets' . DS . 'resources' . DS . 'loaders' . DS . 'ld-router.php';

            // Initialize session handler (when implemented)
            // $this->session = Session::getInstance();

            // Initialize database connection (when implemented)
            // $this->database = Database::getInstance();

            // Initialize view engine (when implemented)
            // $this->view = View::getInstance();


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

            if (IS_DEVELOPMENT || IS_PRODUCTION && !IS_CONFIGURED) {
                $this->logger->info('Application execution started');

                // Get the router instance
                $router = Router::getInstance();

                // Dispatch the request through the router
                $response = $router->dispatch($this->request);

                // Send response to client
                $response->send();

            } else {
                $this->showWelcome();
            }

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
                $this->showErrorPage();
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
        $templatePath = implode(DS, [PD, 'bootstrap', 'templates', 'error', '404.php']);

        if (IS_DEVELOPMENT) {
            $data = [
                'message' => $e->getMessage(),
                'uri' => $this->request->getUri()
            ];

            ob_start();
            if (file_exists($templatePath)) {
                extract($data);
                include $templatePath;
            } else {
                echo "<h1>404 - Page Not Found</h1>";
                echo "<p>{$e->getMessage()}</p>";
                echo "<p>MailTemplate file not found: $templatePath</p>";
            }
            $content = ob_get_clean();

            Response::notFound($content)->send();
        } else {
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
        $templatePath = implode(DS, [PD, 'bootstrap', 'templates', 'error', '405.php']);

        if (IS_DEVELOPMENT) {
            $data = [
                'message' => $e->getMessage(),
                'uri' => $this->request->getUri()
            ];

            ob_start();
            if (file_exists($templatePath)) {
                extract($data);
                include $templatePath;
            } else {
                echo "<h1>405 - Method not allowed</h1>";
                echo "<p>{$e->getMessage()}</p>";
                echo "<p>MailTemplate file not found: $templatePath</p>";
            }
            $content = ob_get_clean();

            Response::notFound($content)->send();
        } else {
            Response::notFound($this->getProductionErrorContent('Method not allowed'))->send();
        }
    }

    /**
     * Display welcome page
     *
     * @return void
     * @throws Exception
     */
    protected function showWelcome(): void
    {
        $templatePath = implode(DS, [PD, 'bootstrap', 'templates', 'welcome.php']);
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            $this->logger->error('Welcome template not found', ['path' => $templatePath]);
            echo "<h1>Welcome to Catalyst Framework</h1>";
            echo "<p>MailTemplate file not found: $templatePath</p>";
        }
    }

    /**
     * Display error page
     *
     * @return void
     */
    protected function showErrorPage(): void
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
