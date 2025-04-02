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

use Catalyst\Helpers\Log\Logger;

if (!defined('INITIALIZED_LOGGER_FUNCTION')) {
    /**
     * Helper function for logging messages
     *
     * @param string $level Log level (emergency, alert, critical, error, warning, notice, info, debug)
     * @param string $message Message to log
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_message(string $level, string $message, array $context = []): void
    {
        Logger::getInstance()->log($level, $message, $context);
    }

    /**
     * Helper function for logging errors
     *
     * @param string $message Error message
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_error(string $message, array $context = []): void
    {
        Logger::getInstance()->error($message, $context);
    }

    /**
     * Helper function for logging debug information
     *
     * @param string $message Debug message
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_debug(string $message, array $context = []): void
    {
        Logger::getInstance()->debug($message, $context);
    }

    /**
     * Helper function for logging events with a specific type
     *
     * @param string $type Event type (system, user, mail)
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_event(string $type, string $event, string $message, array $context = []): void
    {
        $logger = Logger::getInstance();

        switch (strtolower($type)) {
            case 'system':
                $logger->system($event, $message, $context);
                break;
            case 'user':
                $logger->user($event, $message, $context);
                break;
            case 'mail':
                $logger->mail($event, $message, $context);
                break;
            default:
                // Si el tipo no es reconocido, registrar como mensaje informativo
                $context['event_type'] = $type;
                $context['event_name'] = $event;
                $logger->info($message, $context);
        }
    }

    /**
     * Helper function for logging system events
     *
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_system(string $event, string $message, array $context = []): void
    {
        log_event('system', $event, $message, $context);
    }

    /**
     * Helper function for logging user events
     *
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_user(string $event, string $message, array $context = []): void
    {
        log_event('user', $event, $message, $context);
    }

    /**
     * Helper function for logging mail events
     *
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context information
     * @return void
     * @throws Exception
     */
    function log_mail(string $event, string $message, array $context = []): void
    {
        log_event('mail', $event, $message, $context);
    }

    define('INITIALIZED_LOGGER_FUNCTION', true);
}