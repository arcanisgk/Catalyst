<?php

declare(strict_types=1);

namespace App\Assets\Framework\Exceptions;

use RuntimeException;

class FileSystemException extends RuntimeException
{
    public static function unableToWriteFile(string $path, ?string $reason = null): self
    {
        $message = "Unable to write to file: '$path'";
        if ($reason) {
            $message .= " - Reason: $reason";
        }
        return new self($message);
    }

    public static function unableToReadFile(string $path, ?string $reason = null): self
    {
        $message = "Unable to read file: '$path'";
        if ($reason) {
            $message .= " - Reason: $reason";
        }
        return new self($message);
    }

    public static function fileMissing(string $path): self
    {
        return new self("Required file not found: '$path'");
    }
}