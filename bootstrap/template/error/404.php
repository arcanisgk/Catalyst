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
    <title>Page Not Found</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #d23d24;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .uri {
            font-family: monospace;
            background: #eee;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .back-button:hover {
            background-color: #0069d9;
        }

        .suggestions {
            margin-top: 30px;
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }

        .suggestions ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>404 - Page Not Found</h1>
    <div class="message">' . htmlspecialchars($e->getMessage()) . '</div>
    <p>The requested URL was: <span class="uri">' . htmlspecialchars($this->request->getUri()) . '</span></p>

    <button onclick="history.back()" class="back-button">Go Back</button>
    <a href="/" class="back-button">Go to Homepage</a>

    <div class="suggestions">
        <strong>Suggestions:</strong>
        <ul>
            <li>Check that the URL is correct</li>
            <li>The page might have been moved or deleted</li>
            <li>You might not have permission to view this page</li>
        </ul>
    </div>
</div>
</body>
</html>