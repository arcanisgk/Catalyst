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


?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? $appName ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <?php if (function_exists('asset')): ?>
        <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <?php endif; ?>

    <!-- Additional head content -->
    <?= $headContent ?? '' ?>
</head>
<body>
<!-- Main Content -->
<div class="container my-4">
    <!-- Insert flash messages here -->
    <?php include implode(DS, [PD, 'bootstrap', 'template', 'partials', 'flash-messages.php']); ?>
    <?= $viewContent ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<?php if (function_exists('asset')): ?>
    <script src="<?= asset('js/main.js') ?>"></script>
<?php endif; ?>

<script src="<?= isset($asset) ? $asset('assets/js/toasts.js') : '/assets/js/toasts.js' ?>"></script>

<!-- Additional scripts -->
<?= $scripts ?? '' ?>
</body>
</html>
