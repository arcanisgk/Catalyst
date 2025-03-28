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

namespace Catalyst\Framework\Core\Session;

/**
 * FlashMessage class for managing temporary messages across requests
 *
 * Handles flash messages for success notifications, errors, warnings, etc.
 *
 * @package Catalyst\Framework\Core\Session
 */
class FlashMessage
{
    /**
     * Session key for flash messages
     */
    private const string FLASH_KEY = '_flash_messages';

    /**
     * SessionManager instance
     *
     * @var SessionManager
     */
    protected SessionManager $session;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->session = SessionManager::getInstance();

        // Initialize flash message storage if needed
        if (!$this->session->has(self::FLASH_KEY)) {
            $this->session->set(self::FLASH_KEY, [
                'new' => [],
                'current' => []
            ]);
        }

        // Move new messages from previous request to current
        $this->moveNewToCurrent();
    }

    /**
     * Move new messages to current for display
     *
     * @return void
     */
    protected function moveNewToCurrent(): void
    {
        $flash = $this->session->get(self::FLASH_KEY);

        // Move new messages to current
        $flash['current'] = $flash['new'];
        $flash['new'] = [];

        $this->session->set(self::FLASH_KEY, $flash);
    }

    /**
     * Add a flash message
     *
     * @param string $type MailMessage type (success, error, warning, info)
     * @param string $message The message content
     * @return self For method chaining
     */
    public function add(string $type, string $message): self
    {
        $flash = $this->session->get(self::FLASH_KEY);

        // Add to new messages for next request
        $flash['new'][$type][] = $message;

        $this->session->set(self::FLASH_KEY, $flash);

        return $this;
    }

    /**
     * Add a success message
     *
     * @param string $message The message content
     * @return self For method chaining
     */
    public function success(string $message): self
    {
        return $this->add('success', $message);
    }

    /**
     * Add an error message
     *
     * @param string $message The message content
     * @return self For method chaining
     */
    public function error(string $message): self
    {
        return $this->add('error', $message);
    }

    /**
     * Add a warning message
     *
     * @param string $message The message content
     * @return self For method chaining
     */
    public function warning(string $message): self
    {
        return $this->add('warning', $message);
    }

    /**
     * Add an info message
     *
     * @param string $message The message content
     * @return self For method chaining
     */
    public function info(string $message): self
    {
        return $this->add('info', $message);
    }

    /**
     * Get all current flash messages
     *
     * @return array All flash messages by type
     */
    public function all(): array
    {
        $flash = $this->session->get(self::FLASH_KEY);
        return $flash['current'];
    }

    /**
     * Get flash messages of a specific type
     *
     * @param string $type MailMessage type (success, error, warning, info)
     * @return array Messages of the specified type
     */
    public function get(string $type): array
    {
        $flash = $this->session->get(self::FLASH_KEY);
        return $flash['current'][$type] ?? [];
    }

    /**
     * Check if there are any flash messages
     *
     * @param string|null $type Optional message type filter
     * @return bool True if there are flash messages
     */
    public function has(?string $type = null): bool
    {
        $flash = $this->session->get(self::FLASH_KEY);

        if ($type === null) {
            return !empty($flash['current']);
        }

        return !empty($flash['current'][$type]);
    }

    /**
     * Clear all flash messages
     *
     * @return self For method chaining
     */
    public function clear(): self
    {
        $this->session->set(self::FLASH_KEY, [
            'new' => [],
            'current' => []
        ]);

        return $this;
    }
}
