<?php
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
