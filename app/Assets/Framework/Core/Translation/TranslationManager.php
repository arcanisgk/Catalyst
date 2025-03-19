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

namespace App\Assets\Framework\Core\Translation;

use App\Assets\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Translation manager singleton for accessing translation services
 *
 * Provides a centralized point of access to translation functionality with
 * proper configuration management and environment awareness.
 *
 * @package App\Assets\Framework\Core\Translation;
 */
class TranslationManager
{
    use SingletonTrait;

    /**
     * The underlying TranslationService instance
     *
     * @var TranslationService
     */
    protected TranslationService $service;

    /**
     * The TranslationCache instance (if caching is enabled)
     *
     * @var TranslationCache|null
     */
    protected ?TranslationCache $cache = null;

    /**
     * Whether the manager has been initialized
     *
     * @var bool
     */
    protected bool $initialized = false;

    /**
     * Configuration options
     *
     * @var array
     */
    protected array $config = [
        'useCache' => false,
        'defaultLanguage' => 'en',
        'currentLanguage' => null,
        'frameworkPath' => null,
        'applicationPath' => null
    ];

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->logger = Logger::getInstance();

        // Set default current language based on environment
        $this->config['currentLanguage'] = defined('DEF_LANG') ? DEF_LANG : 'en';

        // Set default paths
        $this->config['frameworkPath'] = PD . DS . 'app' . DS . 'Assets' . DS . 'Framework' . DS . 'Languages';
        $this->config['applicationPath'] = PD . DS . 'app' . DS . 'Repository' . DS . 'Languages';

        // Determine whether to use cache based on environment
        $this->config['useCache'] = IS_PRODUCTION;
    }

    /**
     * Initialize the translation manager with configuration
     *
     * @param array $config Configuration options
     * @return self For method chaining
     * @throws Exception If initialization fails
     */
    public function initialize(array $config = []): self
    {
        if ($this->initialized) {
            $this->logger->debug('TranslationManager already initialized, skipping');
            return $this;
        }

        try {
            // Merge provided config with defaults
            $this->config = array_merge($this->config, $config);

            // Create cache if enabled
            if ($this->config['useCache']) {
                $this->cache = new TranslationCache();
            }

            // Create and configure translation service
            $this->service = new TranslationService(
                $this->config['defaultLanguage'],
                $this->config['currentLanguage'],
                $this->config['frameworkPath'],
                $this->config['applicationPath'],
                $this->cache
            );

            $this->initialized = true;

            $this->logger->debug('TranslationManager initialized', [
                'defaultLanguage' => $this->config['defaultLanguage'],
                'currentLanguage' => $this->config['currentLanguage'],
                'useCache' => $this->config['useCache']
            ]);

            return $this;
        } catch (Exception $e) {
            $this->logger->error('Failed to initialize TranslationManager', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Get a translation by key
     *
     * @param string $key Translation key (format: group.item or group.subgroup.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     * @throws Exception If not initialized
     */
    public function get(string $key, array $replacements = [], ?string $language = null): string
    {
        $this->ensureInitialized();
        return $this->service->get($key, $replacements, $language);
    }

    /**
     * Get a translation with pluralization based on count
     *
     * @param string $key Translation key base
     * @param int $count Count for determining pluralization
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     * @throws Exception If not initialized
     */
    public function choice(string $key, int $count, array $replacements = [], ?string $language = null): string
    {
        $this->ensureInitialized();
        return $this->service->choice($key, $count, $replacements, $language);
    }

    /**
     * Check if a translation key exists
     *
     * @param string $key Translation key
     * @param string|null $language Language code (defaults to current language)
     * @return bool True if translation exists
     * @throws Exception If not initialized
     */
    public function has(string $key, ?string $language = null): bool
    {
        $this->ensureInitialized();
        return $this->service->has($key, $language);
    }

    /**
     * Set the current language
     *
     * @param string $language Language code
     * @return self For method chaining
     * @throws Exception If not initialized
     */
    public function setLanguage(string $language): self
    {
        $this->ensureInitialized();
        $this->service->setLanguage($language);
        return $this;
    }

    /**
     * Get the current language
     *
     * @return string Current language code
     * @throws Exception If not initialized
     */
    public function getLanguage(): string
    {
        $this->ensureInitialized();
        return $this->service->getLanguage();
    }

    /**
     * Get all available languages
     *
     * @return array Array of language codes
     * @throws Exception If not initialized
     */
    public function getAvailableLanguages(): array
    {
        $this->ensureInitialized();
        return $this->service->getAvailableLanguages();
    }

    /**
     * Clear loaded translations
     *
     * @param string|null $language Specific language to clear or null for all
     * @param string|null $group Specific group to clear or null for all
     * @return self For method chaining
     * @throws Exception If not initialized
     */
    public function clearTranslations(?string $language = null, ?string $group = null): self
    {
        $this->ensureInitialized();
        $this->service->clearTranslations($language, $group);
        return $this;
    }

    /**
     * Export translations for JavaScript use
     *
     * @param array $groups Translation groups to export
     * @param string|null $language Language to export (defaults to current language)
     * @return array Exported translations
     * @throws Exception If not initialized
     */
    public function exportForJavaScript(array $groups, ?string $language = null): array
    {
        $this->ensureInitialized();
        return $this->service->exportForJavaScript($groups, $language);
    }

    /**
     * Check and ensure the manager is initialized
     *
     * @return void
     * @throws Exception If not initialized
     */
    protected function ensureInitialized(): void
    {
        if (!$this->initialized) {
            // Auto-initialize with defaults if not already initialized
            $this->initialize();
        }
    }

    /**
     * Get the underlying TranslationService instance
     *
     * @return TranslationService The translation service
     * @throws Exception If not initialized
     */
    public function getService(): TranslationService
    {
        $this->ensureInitialized();
        return $this->service;
    }

    /**
     * Manually enable or disable translation caching
     *
     * @param bool $enabled Whether caching should be enabled
     * @return self For method chaining
     * @throws Exception If not initialized
     */
    public function setCaching(bool $enabled): self
    {
        $this->ensureInitialized();

        if ($enabled && $this->cache === null) {
            $this->cache = new TranslationCache();
            $this->service->enableCaching($this->cache);
        } elseif (!$enabled) {
            $this->service->disableCaching();
        }

        $this->config['useCache'] = $enabled;

        return $this;
    }
}