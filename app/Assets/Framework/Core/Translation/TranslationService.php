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

namespace Catalyst\Framework\Core\Translation;

use Catalyst\Framework\Exceptions\FileSystemException;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Translation service for handling localization of text
 *
 * Provides functionality for loading, retrieving and manipulating translations
 * from JSON files with support for placeholders, pluralization and fallbacks.
 *
 * @package Catalyst\Framework\Core\Translation;
 */
class TranslationService
{
    /**
     * Default language code
     *
     * @var string
     */
    protected string $defaultLanguage = 'en';

    /**
     * Current language code
     *
     * @var string
     */
    protected string $currentLanguage;

    /**
     * Framework translation paths
     *
     * @var string
     */
    protected string $frameworkPath;

    /**
     * Application translation paths
     *
     * @var string
     */
    protected string $applicationPath;

    /**
     * Loaded translations
     *
     * @var array<string, array<string, array<string, mixed>>>
     */
    protected array $translations = [];

    /**
     * Loaded translation groups
     *
     * @var array<string, array<string>>
     */
    protected array $loadedGroups = [];

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Translation cache handler
     *
     * @var TranslationCache|null
     */
    protected ?TranslationCache $cache = null;

    /**
     * Whether to use caching
     *
     * @var bool
     */
    protected bool $useCache = false;

    /**
     * TranslationService constructor
     *
     * @param string $defaultLanguage Default language code
     * @param string|null $currentLanguage Current language code
     * @param string|null $frameworkPath Path to framework translations
     * @param string|null $applicationPath Path to application translations
     * @param TranslationCache|null $cache Translation cache handler
     */
    public function __construct(
        string            $defaultLanguage = 'en',
        ?string           $currentLanguage = null,
        ?string           $frameworkPath = null,
        ?string           $applicationPath = null,
        ?TranslationCache $cache = null
    )
    {
        $this->logger = Logger::getInstance();
        $this->defaultLanguage = $defaultLanguage;
        $this->currentLanguage = $currentLanguage ?? $defaultLanguage;

        // Set framework translations path
        $this->frameworkPath = $frameworkPath ??
            (PD . DS . 'app' . DS . 'Assets' . DS . 'Framework' . DS . 'Languages');

        // Set application translations path
        $this->applicationPath = $applicationPath ??
            (PD . DS . 'app' . DS . 'Repository' . DS . 'Languages');

        // Set cache if provided
        if ($cache !== null) {
            $this->cache = $cache;
            $this->useCache = true;
        }

        // Initialize internal storage
        $this->translations = [];
        $this->loadedGroups = [];
    }

    /**
     * Set the current language
     *
     * @param string $language Language code
     * @return self For method chaining
     * @throws Exception
     */
    public function setLanguage(string $language): self
    {
        if ($this->currentLanguage !== $language) {
            $this->currentLanguage = $language;
            $this->logger->debug('Language changed', ['language' => $language]);
        }

        return $this;
    }

    /**
     * Get the current language
     *
     * @return string Current language code
     */
    public function getLanguage(): string
    {
        return $this->currentLanguage;
    }

    /**
     * Set the default language
     *
     * @param string $language Default language code
     * @return self For method chaining
     */
    public function setDefaultLanguage(string $language): self
    {
        $this->defaultLanguage = $language;
        return $this;
    }

    /**
     * Get the default language
     *
     * @return string Default language code
     */
    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    /**
     * Get a translation by key
     *
     * @param string $key Translation key (format: group.item or group.subgroup.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language to use (defaults to current language)
     * @return string Translated text
     * @throws Exception
     */
    public function get(string $key, array $replacements = [], ?string $language = null): string
    {
        $language = $language ?? $this->currentLanguage;

        try {
            // Split the key into group and item parts
            $segments = explode('.', $key);

            if (count($segments) < 2) {
                // Key must have at least a group and an item
                $this->logger->debug('Invalid translation key', ['key' => $key]);
                return $key;
            }

            $group = $segments[0];
            $itemKey = implode('.', array_slice($segments, 1));

            // Load the group if not already loaded
            $this->loadTranslationGroup($group, $language);

            // Try to get the translation
            $translation = $this->findTranslation($group, $itemKey, $language);

            // Apply replacements if we have a translation
            if ($translation !== null) {
                return $this->applyReplacements($translation, $replacements);
            }

            // If we didn't find the translation in the current language, try the default language
            if ($language !== $this->defaultLanguage) {
                $this->loadTranslationGroup($group, $this->defaultLanguage);
                $translation = $this->findTranslation($group, $itemKey, $this->defaultLanguage);

                if ($translation !== null) {
                    return $this->applyReplacements($translation, $replacements);
                }
            }

            // If all else fails, return the key itself
            return $key;
        } catch (Exception $e) {
            $this->logger->error('Translation error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            // Return the key as fallback
            return $key;
        }
    }

    /**
     * Get a translated string with pluralization
     *
     * @param string $key Translation key base
     * @param int $count Item count for pluralization
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language to use (defaults to current language)
     * @return string Translated text
     * @throws Exception
     */
    public function choice(string $key, int $count, array $replacements = [], ?string $language = null): string
    {
        $language = $language ?? $this->currentLanguage;

        // Add count to replacements
        $replacements['count'] = $count;

        // Determine which plural form to use (simple one/many for now)
        $suffix = $count === 1 ? '.one' : '.many';

        // Try to get the specific plural form
        $pluralKey = $key . $suffix;
        $translation = $this->get($pluralKey, $replacements, $language);

        // If we got back the key, it means the translation wasn't found, so try the base key
        if ($translation === $pluralKey) {
            return $this->get($key, $replacements, $language);
        }

        return $translation;
    }

    /**
     * Check if a translation key exists
     *
     * @param string $key Translation key
     * @param string|null $language Language to check (defaults to current language)
     * @return bool True if the key exists
     * @throws Exception
     */
    public function has(string $key, ?string $language = null): bool
    {
        $language = $language ?? $this->currentLanguage;

        // Split the key into group and item parts
        $segments = explode('.', $key);

        if (count($segments) < 2) {
            return false;
        }

        $group = $segments[0];
        $itemKey = implode('.', array_slice($segments, 1));

        // Load the group if not already loaded
        $this->loadTranslationGroup($group, $language);

        // Check if the translation exists
        return $this->findTranslation($group, $itemKey, $language) !== null;
    }

    /**
     * Load a translation group
     *
     * @param string $group Group name
     * @param string $language Language code
     * @return bool True if loaded successfully
     * @throws Exception
     */
    protected function loadTranslationGroup(string $group, string $language): bool
    {
        // Skip if already loaded
        if (isset($this->loadedGroups[$language]) && in_array($group, $this->loadedGroups[$language], true)) {
            return true;
        }

        // Initialize language in loadedGroups if not already set
        if (!isset($this->loadedGroups[$language])) {
            $this->loadedGroups[$language] = [];
            $this->translations[$language] = [];
        }

        // Check cache first if enabled
        if ($this->useCache && $this->cache !== null) {
            $cachedTranslations = $this->cache->get($language, $group);

            if ($cachedTranslations !== null) {
                $this->translations[$language][$group] = $cachedTranslations;
                $this->loadedGroups[$language][] = $group;
                return true;
            }
        }

        // Try to load from files if not found in cache
        $loaded = false;

        // First check application translations (they have priority)
        $appPath = $this->applicationPath . DS . $language . DS . $group . '.json';
        $translations = $this->loadTranslationFile($appPath);

        if ($translations !== null) {
            if (!isset($this->translations[$language][$group])) {
                $this->translations[$language][$group] = [];
            }
            $this->translations[$language][$group] = array_merge(
                $this->translations[$language][$group],
                $translations
            );
            $loaded = true;
        }

        // Then check framework translations
        $fwPath = $this->frameworkPath . DS . $language . DS . $group . '.json';
        $translations = $this->loadTranslationFile($fwPath);

        if ($translations !== null) {
            if (!isset($this->translations[$language][$group])) {
                $this->translations[$language][$group] = [];
            }

            // Application translations take precedence, so we only add keys that don't exist
            foreach ($translations as $key => $value) {
                if (!isset($this->translations[$language][$group][$key])) {
                    $this->translations[$language][$group][$key] = $value;
                }
            }

            $loaded = true;
        }

        if ($loaded) {
            $this->loadedGroups[$language][] = $group;

            // Cache the translations if caching is enabled
            if ($this->useCache && $this->cache !== null) {
                $this->cache->put($language, $group, $this->translations[$language][$group]);
            }
        } else {
            $this->logger->debug('Translation group not found', [
                'group' => $group,
                'language' => $language
            ]);
        }

        return $loaded;
    }

    /**
     * Load a translation file
     *
     * @param string $path Path to the translation file
     * @return array<string, mixed>|null Translation data or null if file not found
     * @throws Exception
     */
    protected function loadTranslationFile(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        try {
            $content = file_get_contents($path);

            if ($content === false) {
                throw FileSystemException::unableToReadFile($path);
            }

            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                throw new Exception("Invalid translation file format: {$path}");
            }

            // Flatten nested arrays into dot notation
            return $this->flattenArray($data);
        } catch (Exception $e) {
            $this->logger->error('Failed to load translation file', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Find a translation in the loaded translations
     *
     * @param string $group Translation group
     * @param string $key Item key
     * @param string $language Language code
     * @return string|null Translation or null if not found
     */
    protected function findTranslation(string $group, string $key, string $language): ?string
    {
        // Check if the language and group are loaded
        if (!isset($this->translations[$language][$group])) {
            return null;
        }

        // Check for exact match
        if (isset($this->translations[$language][$group][$key])) {
            $translation = $this->translations[$language][$group][$key];

            // Only return string translations
            if (is_string($translation)) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * Apply replacements to a translation string
     *
     * @param string $translation Translation string
     * @param array $replacements Values to replace placeholders
     * @return string Processed translation
     */
    protected function applyReplacements(string $translation, array $replacements): string
    {
        if (empty($replacements)) {
            return $translation;
        }

        // Replace each placeholder with its corresponding value
        foreach ($replacements as $key => $value) {
            $translation = str_replace(':' . $key, (string)$value, $translation);
        }

        return $translation;
    }

    /**
     * Flatten a nested array to dot notation
     *
     * @param array $array The array to flatten
     * @param string $prefix Current prefix for keys
     * @return array Flattened array
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value) && !$this->isAssociativeArray($value)) {
                // Handle non-associative arrays specially (for pluralization)
                $result[$newKey] = $value;
            } elseif (is_array($value)) {
                // Recursively flatten nested arrays
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                // Add leaf values directly
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Check if an array is associative
     *
     * @param array $array Array to check
     * @return bool True if array is associative
     */
    protected function isAssociativeArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Clear loaded translations
     *
     * @param string|null $language Specific language to clear or null for all
     * @param string|null $group Specific group to clear or null for all
     * @return self For method chaining
     */
    public function clearTranslations(?string $language = null, ?string $group = null): self
    {
        if ($language === null) {
            // Clear all translations
            $this->translations = [];
            $this->loadedGroups = [];
        } elseif (isset($this->translations[$language])) {
            if ($group === null) {
                // Clear all translations for the specified language
                $this->translations[$language] = [];
                $this->loadedGroups[$language] = [];
            } elseif (isset($this->translations[$language][$group])) {
                // Clear only the specified group
                unset($this->translations[$language][$group]);

                // Remove group from loaded groups
                if (isset($this->loadedGroups[$language])) {
                    $key = array_search($group, $this->loadedGroups[$language], true);
                    if ($key !== false) {
                        unset($this->loadedGroups[$language][$key]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Enable translation caching
     *
     * @param TranslationCache $cache Translation cache handler
     * @return self For method chaining
     */
    public function enableCaching(TranslationCache $cache): self
    {
        $this->cache = $cache;
        $this->useCache = true;
        return $this;
    }

    /**
     * Disable translation caching
     *
     * @return self For method chaining
     */
    public function disableCaching(): self
    {
        $this->useCache = false;
        return $this;
    }

    /**
     * Export translations for JavaScript use
     *
     * @param array $groups Translation groups to export
     * @param string|null $language Language to export (defaults to current language)
     * @return array Exported translations
     * @throws Exception
     */
    public function exportForJavaScript(array $groups, ?string $language = null): array
    {
        $language = $language ?? $this->currentLanguage;
        $exported = [];

        foreach ($groups as $group) {
            // Load the group if not already loaded
            $this->loadTranslationGroup($group, $language);

            // Add translations to export if they exist
            if (isset($this->translations[$language][$group])) {
                if (!isset($exported[$group])) {
                    $exported[$group] = [];
                }

                $exported[$group] = $this->translations[$language][$group];
            }
        }

        return $exported;
    }

    /**
     * Get all available languages
     *
     * @return array Array of available language codes
     */
    public function getAvailableLanguages(): array
    {
        $languages = [];

        // Check framework languages
        if (is_dir($this->frameworkPath)) {
            $dirs = scandir($this->frameworkPath);
            if ($dirs !== false) {
                foreach ($dirs as $dir) {
                    if ($dir !== '.' && $dir !== '..' && is_dir($this->frameworkPath . DS . $dir)) {
                        $languages[] = $dir;
                    }
                }
            }
        }

        // Check application languages
        if (is_dir($this->applicationPath)) {
            $dirs = scandir($this->applicationPath);
            if ($dirs !== false) {
                foreach ($dirs as $dir) {
                    if ($dir !== '.' && $dir !== '..' && is_dir($this->applicationPath . DS . $dir) && !in_array($dir, $languages, true)) {
                        $languages[] = $dir;
                    }
                }
            }
        }

        return $languages;
    }
}