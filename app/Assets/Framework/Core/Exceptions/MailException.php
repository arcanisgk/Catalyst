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

namespace Catalyst\Framework\Core\Exceptions;

use Exception;

/**************************************************************************************
 * Mail exception
 *
 * Handles mail-related errors with specific error codes and messages.
 *
 * @package Catalyst\Framework\Core\Exceptions
 */
class MailException extends Exception
{
    // Error codes
    public const int ERROR_CONFIGURATION = 100;
    public const int ERROR_INVALID_ADDRESS = 101;
    public const int ERROR_SENDING = 102;
    public const int ERROR_ATTACHMENT = 103;
    public const int ERROR_TEMPLATE = 104;
    public const int ERROR_DKIM = 105;

    /**
     * Create a new configuration error instance
     *
     * @param string $message Error message
     * @return static
     */
    public static function configurationError(string $message): self
    {
        return new static($message, self::ERROR_CONFIGURATION);
    }

    /**
     * Create a new invalid address error instance
     *
     * @param string $address Invalid email address
     * @return static
     */
    public static function invalidAddress(string $address): self
    {
        return new static("Invalid email address: $address", self::ERROR_INVALID_ADDRESS);
    }

    /**
     * Create a new sending error instance
     *
     * @param string $message Error message
     * @return static
     */
    public static function sendingError(string $message): self
    {
        return new static("Failed to send email: $message", self::ERROR_SENDING);
    }

    /**
     * Create a new attachment error instance
     *
     * @param string $filePath File path
     * @param string $message Error message
     * @return static
     */
    public static function attachmentError(string $filePath, string $message): self
    {
        return new static("Failed to attach file '$filePath': $message", self::ERROR_ATTACHMENT);
    }

    /**
     * Create a new template error instance
     *
     * @param string $template MailTemplate name or path
     * @param string $message Error message
     * @return static
     */
    public static function templateError(string $template, string $message): self
    {
        return new static("Failed to process template '$template': $message", self::ERROR_TEMPLATE);
    }

    /**
     * Create a new DKIM error instance
     *
     * @param string $message Error message
     * @return static
     */
    public static function dkimError(string $message): self
    {
        return new static("DKIM signing error: $message", self::ERROR_DKIM);
    }
}