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

namespace Catalyst\Framework\Core\Providers;

use Catalyst\Framework\Core\Translation\TranslationManager;
use Catalyst\Framework\Core\View\LayoutManager;
use Catalyst\Framework\Core\View\ViewFactory;
use Catalyst\Framework\Core\View\ViewFinder;
use Catalyst\Framework\Core\View\ViewRenderer;
use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * ViewServiceProvider class for bootstrapping the view subsystem
 *
 * Centralizes the initialization and configuration of view components,
 * ensuring they're properly set up and integrated with the rest of the framework.
 *
 * @package Catalyst\Framework\Core\Providers;
 */
class ViewServiceProvider
{
    use SingletonTrait;

    /**
     * Whether the provider has been bootstrapped
     *
     * @var bool
     */
    private bool $bootstrapped = false;

    /**
     * Logger instance
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Bootstrap the view subsystem components
     *
     * @return bool Success status
     */
    public function bootstrap(): bool
    {
        if ($this->bootstrapped) {
            return true;
        }

        try {
            $this->logger->debug('Bootstrapping view subsystem');

            // Initialize and configure the ViewFinder
            $this->configureViewFinder();

            // Initialize and configure the LayoutManager
            $this->configureLayoutManager();

            // Initialize the ViewRenderer
            ViewRenderer::getInstance();

            // Initialize and configure the TranslationManager
            $this->configureTranslationManager();

            // Initialize and configure the ViewFactory
            $this->configureViewFactory();

            // Create default layout if needed
            $this->ensureDefaultLayoutExists();

            $this->bootstrapped = true;
            $this->logger->debug('View subsystem bootstrapped successfully');

            return true;
        } catch (Exception $e) {
            $this->logger->error('Failed to bootstrap view subsystem', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Configure the ViewFinder component
     *
     * @return void
     * @throws Exception
     */
    protected function configureViewFinder(): void
    {
        $viewFinder = ViewFinder::getInstance();

        // Only add theme path if the constant is defined
        if (defined('THEME_PATH') && is_dir(THEME_PATH)) {
            $viewFinder->addPath('theme', THEME_PATH);
        }

        // Log configured paths
        $this->logger->debug('ViewFinder paths configured', [
            'paths' => $viewFinder->getPaths()
        ]);
    }


    /**
     * Configure the LayoutManager component
     *
     * @return void
     */
    protected function configureLayoutManager(): void
    {
        $layoutManager = LayoutManager::getInstance();

        // Only set default layout if the constant is defined
        if (defined('DEFAULT_LAYOUT')) {
            $layoutManager->setDefaultLayout(DEFAULT_LAYOUT);
        }

        $this->logger->debug('LayoutManager configured', [
            'defaultLayout' => $layoutManager->getDefaultLayout()
        ]);
    }

    /**
     * Configure the TranslationManager component
     *
     * @return void
     */
    protected function configureTranslationManager(): void
    {
        $translationManager = TranslationManager::getInstance();

        // Initialize with default configuration
        $translationManager->initialize([
            'defaultLanguage' => defined('DEF_LANG') ? DEF_LANG : 'en',
            'currentLanguage' => $this->determineCurrentLanguage(),
            'useCache' => IS_PRODUCTION
        ]);

        $this->logger->debug('TranslationManager configured', [
            'language' => $translationManager->getLanguage(),
            'defaultLanguage' => $translationManager->getDefaultLanguage()
        ]);
    }

    /**
     * Configure the ViewFactory component
     *
     * @return void
     */
    protected function configureViewFactory(): void
    {
        $viewFactory = ViewFactory::getInstance();
        $translationManager = TranslationManager::getInstance();

        // Share common data with all views
        $viewFactory->shareMany([
            'appName' => defined('APP_NAME') ? APP_NAME : 'Catalyst',
            'appVersion' => defined('APP_VERSION') ? APP_VERSION : '1.0',
            'currentYear' => date('Y'),
            'baseUrl' => $this->determineBaseUrl(),
            'currentLanguage' => $translationManager->getLanguage(),
            'availableLanguages' => $translationManager->getAvailableLanguages()
        ]);

        $this->logger->debug('ViewFactory configured with shared data');
    }

    /**
     * Determine the current application base URL
     *
     * @return string Base URL
     */
    protected function determineBaseUrl(): string
    {
        // Get from config if available
        if (defined('APP_URL')) {
            return APP_URL;
        }

        // Otherwise determine dynamically
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return "{$protocol}://{$host}";
    }

    /**
     * Determine the current language
     *
     * @return string Language code
     */
    protected function determineCurrentLanguage(): string
    {
        // Check URL parameter
        if (!empty($_GET['lang'])) {
            return $_GET['lang'];
        }

        // Check session if available
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }

        // Check configuration
        if (defined('DEF_LANG')) {
            return DEF_LANG;
        }

        // Default to English
        return 'en';
    }

    /**
     * Ensure the default layout exists, creating it if needed
     *
     * @return void
     */
    protected function ensureDefaultLayoutExists(): void
    {
        $layoutManager = LayoutManager::getInstance();
        $defaultLayout = $layoutManager->getDefaultLayout();

        if ($defaultLayout && !$layoutManager->layoutExists($defaultLayout)) {
            $this->logger->debug('Creating default layout', [
                'layout' => $defaultLayout
            ]);

            // Path to default layout template
            $templatePath = PD . DS . 'app' . DS . 'Assets' . DS . 'Framework' . DS . 'Views' . DS . 'layouts' . DS . 'template.php';

            // Check if template exists
            if (file_exists($templatePath)) {
                // Read template content
                $defaultContent = file_get_contents($templatePath);
                $layoutManager->createDefaultLayout($defaultContent);
            } else {
                $this->logger->warning('Default layout template not found', [
                    'templatePath' => $templatePath
                ]);

                // Fallback to creating a basic layout
                $defaultContent = $this->getDefaultLayoutTemplate();
                $layoutManager->createDefaultLayout($defaultContent);
            }
        }
    }


    /**
     * Get the default layout template content as a fallback
     *
     * This is only used if the template file cannot be found
     *
     * @return string Default layout HTML
     */
    protected function getDefaultLayoutTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? $appName ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <?= $viewContent ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

    /**
     * Check if the view subsystem has been bootstrapped
     *
     * @return bool Bootstrap status
     */
    public function isBootstrapped(): bool
    {
        return $this->bootstrapped;
    }
}