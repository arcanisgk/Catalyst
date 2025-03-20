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

use Catalyst\Framework\Core\Translation\TranslationManager;

if (!function_exists('t')) {
    /**
     * Get a translation by key
     *
     * Shorthand function for accessing translations.
     *
     * @param string $key Translation key (format: group.item or group.subgroup.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     */
    function t(string $key, array $replacements = [], ?string $language = null): string
    {
        try {
            return TranslationManager::getInstance()->get($key, $replacements, $language);
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Translation error: {$e->getMessage()}");
            }

            // Return the key as fallback
            return $key;
        }
    }
}

if (!function_exists('trans')) {
    /**
     * Get a translation by key
     *
     * Alias for t() function with a more descriptive name.
     *
     * @param string $key Translation key (format: group.item or group.subgroup.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     */
    function trans(string $key, array $replacements = [], ?string $language = null): string
    {
        return t($key, $replacements, $language);
    }
}

if (!function_exists('trans_choice')) {
    /**
     * Get a translation with pluralization based on count
     *
     * @param string $key Translation key base
     * @param int $count Count for determining pluralization
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     */
    function trans_choice(string $key, int $count, array $replacements = [], ?string $language = null): string
    {
        try {
            return TranslationManager::getInstance()->choice($key, $count, $replacements, $language);
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Translation choice error: {$e->getMessage()}");
            }

            // Return the key as fallback
            return $key;
        }
    }
}

if (!function_exists('has_translation')) {
    /**
     * Check if a translation exists
     *
     * @param string $key Translation key
     * @param string|null $language Language code (defaults to current language)
     * @return bool True if translation exists
     */
    function has_translation(string $key, ?string $language = null): bool
    {
        try {
            return TranslationManager::getInstance()->has($key, $language);
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Translation check error: {$e->getMessage()}");
            }

            return false;
        }
    }
}

if (!function_exists('current_language')) {
    /**
     * Get the current language
     *
     * @return string Current language code
     */
    function current_language(): string
    {
        try {
            return TranslationManager::getInstance()->getLanguage();
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Current language error: {$e->getMessage()}");
            }

            // Return default language as fallback
            return defined('DEF_LANG') ? DEF_LANG : 'en';
        }
    }
}

if (!function_exists('available_languages')) {
    /**
     * Get all available languages
     *
     * @return array Array of language codes
     */
    function available_languages(): array
    {
        try {
            return TranslationManager::getInstance()->getAvailableLanguages();
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Available languages error: {$e->getMessage()}");
            }

            return ['en']; // Return at least English as fallback
        }
    }
}

if (!function_exists('set_language')) {
    /**
     * Set the current language
     *
     * @param string $language Language code
     * @return bool Success status
     */
    function set_language(string $language): bool
    {
        try {
            TranslationManager::getInstance()->setLanguage($language);

            // Update session if available
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['language'] = $language;
            }

            return true;
        } catch (Exception $e) {
            // Log this error if we're in development mode
            if (IS_DEVELOPMENT) {
                error_log("Set language error: {$e->getMessage()}");
            }

            return false;
        }
    }
}

if (!function_exists('__')) {
    /**
     * Get a translation by key (alias for t())
     *
     * This is a common shorthand in many frameworks.
     *
     * @param string $key Translation key (format: group.item or group.subgroup.item)
     * @param array $replacements Values to replace placeholders
     * @param string|null $language Language code (defaults to current language)
     * @return string Translated text
     */
    function __(string $key, array $replacements = [], ?string $language = null): string
    {
        return t($key, $replacements, $language);
    }
}