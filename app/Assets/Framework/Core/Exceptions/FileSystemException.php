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

namespace Catalyst\Assets\Framework\Core\Exceptions;

use RuntimeException;

/**************************************************************************************
 * Exception class for file system related errors
 *
 * Provides factory methods for common file system error scenarios
 */
class FileSystemException extends RuntimeException
{

    /**
     * @param string $path
     * @param string|null $reason
     * @return self
     */
    public static function unableToWriteFile(string $path, ?string $reason = null): self
    {
        $message = "Unable to write to file: '$path'";
        if ($reason) {
            $message .= " - Reason: $reason";
        }
        return new self($message);
    }

    /**
     * @param string $path
     * @param string|null $reason
     * @return self
     */
    public static function unableToReadFile(string $path, ?string $reason = null): self
    {
        $message = "Unable to read file: '$path'";
        if ($reason) {
            $message .= " - Reason: $reason";
        }
        return new self($message);
    }

    /**
     * @param string $path
     * @return self
     */
    public static function fileMissing(string $path): self
    {
        return new self("Required file not found: '$path'");
    }
}