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

namespace App\Assets\Framework\Core\Providers;

use App\Assets\Framework\Core\Translation\TranslationCache;
use App\Assets\Framework\Core\Translation\TranslationManager;
use App\Assets\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\Log\Logger;
use Exception;
use Locale;

/**************************************************************************************
 * TranslationServiceProvider class for bootstrapping the translation subsystem
 *
 * Centralizes the initialization and configuration of translation components,
 * ensuring they're properly set up and integrated with the rest of the framework.
 *
 * @package App\Assets\Framework\Core\Providers;
 */
class TranslationServiceProvider
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
     * Bootstrap the translation subsystem components
     *
     * @return bool Success status
     * @throws Exception
     */
    public function bootstrap(): bool
    {
        if ($this->bootstrapped) {
            return true;
        }

        try {
            $this->logger->debug('Bootstrapping translation subsystem');

            // Initialize translation cache if in production
            $cache = null;
            if (IS_PRODUCTION) {
                $cache = $this->initializeTranslationCache();
            }

            // Initialize and configure the translation manager
            $this->configureTranslationManager($cache);

            $this->bootstrapped = true;
            $this->logger->debug('Translation subsystem bootstrapped successfully');

            return true;
        } catch (Exception $e) {
            $this->logger->error('Failed to bootstrap translation subsystem', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Initialize the translation cache
     *
     * @return TranslationCache The initialized cache
     * @throws Exception
     */
    protected function initializeTranslationCache(): TranslationCache
    {
        // Create cache directory if it doesn't exist
        $cacheDir = PD . DS . 'cache' . DS . 'translations';
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0755, true) && !is_dir($cacheDir)) {
                $this->logger->warning('Could not create translation cache directory', [
                    'directory' => $cacheDir
                ]);
            }
        }

        // Create and configure the cache
        $cache = new TranslationCache(
            $cacheDir,
            true,
            86400 // 24 hours cache expiration
        );

        $this->logger->debug('Translation cache initialized', [
            'cache_dir' => $cacheDir,
            'expiration' => '24 hours'
        ]);

        return $cache;
    }

    /**
     * Configure the translation manager
     *
     * @param TranslationCache|null $cache Optional translation cache
     * @return void
     * @throws Exception
     */
    protected function configureTranslationManager(?TranslationCache $cache = null): void
    {
        // Get the translation manager instance
        $manager = TranslationManager::getInstance();

        // Configure the manager
        $manager->initialize([
            'defaultLanguage' => $this->getDefaultLanguage(),
            'currentLanguage' => $this->determineCurrentLanguage(),
            'useCache' => $cache !== null,
            'frameworkPath' => PD . DS . 'app' . DS . 'Assets' . DS . 'Framework' . DS . 'Languages',
            'applicationPath' => PD . DS . 'app' . DS . 'Repository' . DS . 'Languages'
        ]);

        $this->logger->debug('TranslationManager configured', [
            'default_language' => $manager->getDefaultLanguage(),
            'current_language' => $manager->getLanguage(),
            'available_languages' => $manager->getAvailableLanguages(),
            'use_cache' => $cache !== null
        ]);
    }

    /**
     * Get the default language from configuration
     *
     * @return string Default language code
     */
    protected function getDefaultLanguage(): string
    {
        return defined('DEF_LANG') ? DEF_LANG : 'en';
    }

    /**
     * Determine the current language from various sources
     *
     * @return string Current language code
     */
    protected function determineCurrentLanguage(): string
    {
        // Check URL parameter first
        if (!empty($_GET['lang'])) {
            return $_GET['lang'];
        }

        // Then check session if available
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }

        // Then check cookie if available
        if (isset($_COOKIE['language'])) {
            return $_COOKIE['language'];
        }

        // Then check Accept-Language header
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if ($locale) {
                $parts = explode('_', $locale);
                return $parts[0]; // Get language part only
            }
        }

        // Finally fall back to default language
        return $this->getDefaultLanguage();
    }

    /**
     * Reset the translation system (useful for testing)
     *
     * @return void
     */
    public function reset(): void
    {
        $this->bootstrapped = false;
        TranslationManager::resetInstance();
    }

    /**
     * Check if the translation subsystem has been bootstrapped
     *
     * @return bool Bootstrap status
     */
    public function isBootstrapped(): bool
    {
        return $this->bootstrapped;
    }
}
