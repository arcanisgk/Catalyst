<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
 * @see       https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2025
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Framework
 * @filesource
 *
 * @link      https://catalyst.dock Local development URL
 *
 */

/**
 * Alert partial for displaying messages
 *
 * Usage:
 * <?= $include('alert', ['type' => 'success', 'message' => 'Operation successful']) ?>
 *
 * Parameters:
 * - type: success, info, warning, danger
 * - message: The alert message
 * - dismissible: Whether the alert can be dismissed (default: true)
 */

$type = $type ?? 'info';
$message = $message ?? '';
$dismissible = $dismissible ?? true;
$classes = 'alert alert-' . $type;

if ($dismissible) {
    $classes .= ' alert-dismissible fade show';
}
?>

<div class="<?= $classes ?>" role="alert">
    <?= $message ?>

    <?php if ($dismissible): ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <?php endif; ?>
</div>
