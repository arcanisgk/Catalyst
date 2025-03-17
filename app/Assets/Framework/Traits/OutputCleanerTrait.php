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

namespace App\Assets\Framework\Traits;

/**
 * Trait that provides output buffer cleaning functionality
 */
trait OutputCleanerTrait
{
    /**
     * Clean any output that might have been sent before an error occurred
     */
    protected function cleanOutput(): void
    {
        // Clear the output buffer if it's started
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Start a fresh output buffer
        if (ob_get_level() === 0) {
            ob_start();
        }
    }
}