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

use App\Assets\Framework\Exceptions\FileSystemException;
use App\Assets\Helpers\Log\Logger;

/**************************************************************************************
 * Translation cache handler for improved performance
 *
 * Provides caching capabilities for translations to reduce file system access
 * and improve application performance, especially in production environments.
 *
 * @package App\Assets\Framework\Core\Translation;
 */
class TranslationCache
{
    /**
     * In-memory cache storage
     *
     * @var array<string, array<string, array<string, mixed>>>
     */
    protected array $cache = [];

    /**
     * Path to the cache directory
     *
     * @var string
     */
    protected string $cacheDir;

    /**
     * Whether to use file-based caching
     *
     * @var bool
     */
    protected bool $useFileCache;

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Cache file expiration time in seconds
     *
     * @var int
     */
    protected int $cacheExpiration;

    /**
     * TranslationCache constructor
     *
     * @param string|null $cacheDir Cache directory path
     * @param bool $useFileCache Whether to use file-based caching
     * @param int $cacheExpiration Cache expiration time in seconds
     */
    public function __construct(
        ?string $cacheDir = null,
        bool    $useFileCache = true,
        int     $cacheExpiration = 86400
    )
    {
        $this->logger = Logger::getInstance();

        // Default cache directory is in the project's cache directory
        $this->cacheDir = $cacheDir ?? (PD . DS . 'cache' . DS . 'translations');
        $this->useFileCache = $useFileCache && IS_PRODUCTION;
        $this->cacheExpiration = $cacheExpiration;

        // Create cache directory if it doesn't exist and file caching is enabled
        if ($this->useFileCache && !is_dir($this->cacheDir)) {
            $this->createCacheDirectory();
        }
    }

    /**
     * Get cached translations for a language and group
     *
     * @param string $language Language code
     * @param string $group Translation group name
     * @return array<string, mixed>|null Cached translations or null if not found/expired
     */
    public function get(string $language, string $group): ?array
    {
        // Check in-memory cache first
        if (isset($this->cache[$language][$group])) {
            $this->logger->debug('Translation cache hit (memory)', [
                'language' => $language,
                'group' => $group
            ]);
            return $this->cache[$language][$group];
        }

        // If file caching is enabled, try to load from file
        if ($this->useFileCache) {
            $cacheFile = $this->getCacheFilePath($language, $group);

            if (file_exists($cacheFile) && is_readable($cacheFile)) {
                // Check if cache is expired
                if (filemtime($cacheFile) + $this->cacheExpiration < time()) {
                    $this->logger->debug('Translation cache expired', [
                        'language' => $language,
                        'group' => $group
                    ]);

                    return null;
                }

                try {
                    $data = include $cacheFile;

                    if (is_array($data)) {
                        // Store in memory cache for faster access
                        $this->cache[$language][$group] = $data;

                        $this->logger->debug('Translation cache hit (file)', [
                            'language' => $language,
                            'group' => $group
                        ]);

                        return $data;
                    }
                } catch (\Throwable $e) {
                    $this->logger->error('Failed to load translation cache file', [
                        'file' => $cacheFile,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->logger->debug('Translation cache miss', [
            'language' => $language,
            'group' => $group
        ]);

        return null;
    }

    /**
     * Store translations in cache
     *
     * @param string $language Language code
     * @param string $group Translation group name
     * @param array<string, mixed> $translations Translations to cache
     * @return bool Success status
     */
    public function put(string $language, string $group, array $translations): bool
    {
        // Always store in memory cache
        $this->cache[$language][$group] = $translations;

        // If file caching is enabled, store to file as well
        if ($this->useFileCache) {
            return $this->writeToFile($language, $group, $translations);
        }

        return true;
    }

    /**
     * Check if a cache entry exists
     *
     * @param string $language Language code
     * @param string $group Translation group name
     * @return bool True if cache exists and is not expired
     */
    public function has(string $language, string $group): bool
    {
        // Check in-memory cache first
        if (isset($this->cache[$language][$group])) {
            return true;
        }

        // If file caching is enabled, check file existence and expiration
        if ($this->useFileCache) {
            $cacheFile = $this->getCacheFilePath($language, $group);

            if (file_exists($cacheFile) && is_readable($cacheFile)) {
                // Check if cache is expired
                return filemtime($cacheFile) + $this->cacheExpiration >= time();
            }
        }

        return false;
    }

    /**
     * Clear specific or all translations from cache
     *
     * @param string|null $language Language code or null for all languages
     * @param string|null $group Translation group name or null for all groups
     * @return bool Success status
     */
    public function forget(?string $language = null, ?string $group = null): bool
    {
        // Clear from memory cache
        if ($language === null) {
            // Clear all cached translations
            $this->cache = [];
        } elseif ($group === null) {
            // Clear all translations for a specific language
            unset($this->cache[$language]);
        } else {
            // Clear a specific translation group
            unset($this->cache[$language][$group]);
        }

        // If file caching is enabled, clear from file system as well
        if ($this->useFileCache) {
            try {
                if ($language === null) {
                    // Clear all cache files
                    $this->clearCacheDirectory();
                } elseif ($group === null) {
                    // Clear all cache files for a specific language
                    $this->clearLanguageCache($language);
                } else {
                    // Clear a specific cache file
                    $cacheFile = $this->getCacheFilePath($language, $group);
                    if (file_exists($cacheFile)) {
                        unlink($cacheFile);
                    }
                }

                return true;
            } catch (\Throwable $e) {
                $this->logger->error('Failed to clear translation cache', [
                    'language' => $language,
                    'group' => $group,
                    'error' => $e->getMessage()
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Create the cache directory
     *
     * @return void
     * @throws FileSystemException If directory creation fails
     */
    protected function createCacheDirectory(): void
    {
        try {
            if (!mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
                throw FileSystemException::unableToWriteFile(
                    $this->cacheDir,
                    "Unable to create cache directory"
                );
            }

            $this->logger->debug('Translation cache directory created', [
                'directory' => $this->cacheDir
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create translation cache directory', [
                'directory' => $this->cacheDir,
                'error' => $e->getMessage()
            ]);

            throw FileSystemException::unableToWriteFile(
                $this->cacheDir,
                $e->getMessage()
            );
        }
    }

    /**
     * Get the path to a cache file for a specific language and group
     *
     * @param string $language Language code
     * @param string $group Translation group name
     * @return string Cache file path
     */
    protected function getCacheFilePath(string $language, string $group): string
    {
        return $this->cacheDir . DS . $language . '_' . $group . '.php';
    }

    /**
     * Write translations to a cache file
     *
     * @param string $language Language code
     * @param string $group Translation group name
     * @param array<string, mixed> $translations Translations to cache
     * @return bool Success status
     */
    protected function writeToFile(string $language, string $group, array $translations): bool
    {
        $cacheFile = $this->getCacheFilePath($language, $group);

        try {
            // Create directory if it doesn't exist
            $dir = dirname($cacheFile);
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    throw FileSystemException::unableToWriteFile(
                        $dir,
                        "Unable to create directory"
                    );
                }
            }

            // Write cache file with translations
            $content = "<?php\n\n// Generated: " . date('Y-m-d H:i:s') . "\n// Language: {$language}\n// Group: {$group}\n\nreturn " .
                var_export($translations, true) . ";\n";

            if (file_put_contents($cacheFile, $content) === false) {
                throw FileSystemException::unableToWriteFile($cacheFile);
            }

            $this->logger->debug('Translation cache written to file', [
                'language' => $language,
                'group' => $group,
                'file' => $cacheFile
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to write translation cache file', [
                'file' => $cacheFile,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Clear all translation cache files
     *
     * @return void
     */
    protected function clearCacheDirectory(): void
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }

        $files = glob($this->cacheDir . DS . '*.php');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        $this->logger->debug('All translation cache files cleared');
    }

    /**
     * Clear all translation cache files for a specific language
     *
     * @param string $language Language code
     * @return void
     */
    protected function clearLanguageCache(string $language): void
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }

        $files = glob($this->cacheDir . DS . $language . '_*.php');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        $this->logger->debug('Translation cache files cleared for language', [
            'language' => $language
        ]);
    }
}