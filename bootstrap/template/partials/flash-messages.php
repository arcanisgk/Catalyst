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

use Catalyst\Framework\Core\Session\FlashMessage;

// Initialize flash messages
$flash = new FlashMessage();

// Get messages first
$messages = $flash->all();

// Clear immediately after getting them (before rendering)
$flash->clear();
?>

<?php if (!empty($messages)): ?>
    <div class="flash-messages">
        <?php foreach ($messages as $type => $typeMessages): ?>
            <?php foreach ($typeMessages as $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?php if ($type === 'success'): ?>
                        <i class="bi bi-check-circle-fill me-2"></i>
                    <?php elseif ($type === 'error'): ?>
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?php elseif ($type === 'warning'): ?>
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php elseif ($type === 'info'): ?>
                        <i class="bi bi-info-circle-fill me-2"></i>
                    <?php endif; ?>
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
<?php endif; ?>
