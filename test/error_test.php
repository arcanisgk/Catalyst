<?php
/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Test
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
 * Error Test component for the Catalyst Framework
 *
 */

use Catalyst\Helpers\Error\BugCatcher;

/**
 * Test function to run various error scenarios
 *
 * @param string $testType Type of test to run (error, exception, fatal)
 * @return void
 * @throws Exception
 */
function runErrorTest(string $testType = 'all'): void
{
    echo "<h1>Catalyst Error Handling System Test</h1>";
    echo "<p>Testing error type: " . htmlspecialchars($testType) . "</p>";

    // Initialize the error handling system
    BugCatcher::getInstance()->initialize();

    switch ($testType) {
        case 'error':
            // Test a PHP warning
            echo "<p>Testing PHP Warning...</p>";
            trigger_error("This is a forced error", E_USER_ERROR);
            break;

        case 'notice':
            // Test a PHP notice
            echo "<p>Testing PHP Notice...</p>";
            $array = [];
            echo $array['non_existent_key'];
            break;

        case 'exception':
            // Test an exception
            echo "<p>Testing Exception...</p>";
            throw new Exception("This is a test exception");
            break;

        case 'fatal':
            // Test a fatal error
            echo "<p>Testing Fatal Error...</p>";
            non_existent_function();
            break;

        case 'all':
        default:
            // Let user choose which test to run
            echo <<<HTML
            <p>Choose a test to run:</p>
            <ul>
                <li><a href="?test=error">Test PHP Warning</a></li>
                <li><a href="?test=notice">Test PHP Notice</a></li>
                <li><a href="?test=exception">Test Exception</a></li>
                <li><a href="?test=fatal">Test Fatal Error</a></li>
            </ul>
            HTML;
            break;
    }
}