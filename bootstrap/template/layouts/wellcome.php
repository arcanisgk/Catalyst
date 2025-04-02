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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Catalyst Framework</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .welcome-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 40px;
            text-align: center;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }

        h1 {
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .feature {
            display: inline-block;
            background-color: #e9ecef;
            border-radius: 4px;
            padding: 8px 16px;
            margin: 8px;
            color: #495057;
        }

        .cta-button {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .cta-button:hover {
            background-color: #0b5ed7;
        }

        .footer {
            margin-top: 60px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="welcome-container">
    <!-- Replace with your actual logo path -->
    <img src="/assets/img/catalyst-logo.png" alt="Catalyst Framework Logo" class="logo">

    <h1>Welcome to Catalyst PHP Framework</h1>

    <p>Thank you for installing Catalyst! Your application is now set up and ready for development.</p>
    <!-- Insert flash messages here -->
    <?php include implode(DS, [PD, 'bootstrap', 'template', 'partials', 'flash-messages.php']); ?>
    <div>
        <span class="feature">PHP 8.3+</span>
        <span class="feature">MVC Architecture</span>
        <span class="feature">Dependency Injection</span>
        <span class="feature">Modern Routing</span>
        <span class="feature">Advanced Template System</span>
    </div>

    <p>To continue setting up your application, you'll need to configure your environment settings.</p>

    <a href="/configure" class="cta-button">Configure Application</a>

    <div class="footer">
        <p>Catalyst PHP Framework &copy; <?= date('Y') ?> - Version <?= CATALYST_VERSION ?? '1.0.0' ?></p>
    </div>

    <script src="<?= isset($asset) ? $asset('assets/js/toasts.js') : '/assets/js/toasts.js' ?>"></script>
</div>
</body>
</html>
