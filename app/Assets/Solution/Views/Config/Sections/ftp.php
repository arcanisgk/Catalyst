<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
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
<div class="row">
    <!-- Template for new FTP connections -->
    <template id="ftp-connection-template">
        <div class="ftp-connection" id="connection-__ID__">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>New FTP Connection</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-success btn-xs test-connection" data-connection-id="__ID__">
                            <i class="fa fa-check-circle"></i> Test
                        </button>
                        <button type="button" class="btn btn-danger btn-xs remove-connection" data-connection-id="__ID__">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <form class="ftp-config-form" data-connection-id="__ID__">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_name___ID__">Connection Name</label>
                                    <input type="text" class="form-control" id="ftp_name___ID__"
                                           name="ftp_name___ID__" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_host___ID__">FTP Host</label>
                                    <input type="text" class="form-control" id="ftp_host___ID__"
                                           name="ftp_host___ID__" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ftp_port___ID__">Port</label>
                                    <input type="number" class="form-control" id="ftp_port___ID__"
                                           name="ftp_port___ID__" value="21" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ftp_user___ID__">Username</label>
                                    <input type="text" class="form-control" id="ftp_user___ID__"
                                           name="ftp_user___ID__" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="ftp_password___ID__">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="ftp_password___ID__" name="ftp_password___ID__" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="ftp_password_re___ID__">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="ftp_password_re___ID__" name="ftp_password_re___ID__" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ftp_path___ID__">Default Path</label>
                                    <input type="text" class="form-control" id="ftp_path___ID__"
                                           name="ftp_path___ID__" value="/">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="ftp_passive_mode___ID__" name="ftp_passive_mode___ID__" checked>
                                        <label class="form-check-label" for="ftp_passive_mode___ID__">Enable Passive Mode</label>
                                    </div>
                                    <small class="form-text text-muted">To Enable/Disable Passive Mode.</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>FTP Connections</h5>
                <div class="ibox-tools">
                    <button type="button" id="add-ftp-connection" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add Connection
                    </button>
                </div>
            </div>
            <div class="ibox-content">
                <div id="ftp-connections-container">
                    <?php
                    $connections = $configData ?? [];
                    if (empty($connections)) {
                        $connections = ['ftp1' => [
                            'ftp_name' => '',
                            'ftp_host' => '',
                            'ftp_port' => 21,
                            'ftp_user' => '',
                            'ftp_password' => '',
                            'ftp_password_re' => '',
                            'ftp_path' => '/',
                            'ftp_passive_mode' => true
                        ]];
                    }

                    foreach ($connections as $id => $connection):
                        $connectionId = substr($id, 3); // Extract the numeric part (ftp1 -> 1)
                        ?>
                        <div class="ftp-connection" id="connection-<?= $connectionId ?>">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><?= $connection['ftp_name'] ? $connection['ftp_name'] : 'New FTP Connection' ?></h5>
                                    <div class="ibox-tools">
                                        <button type="button" class="btn btn-success btn-xs test-connection" data-connection-id="<?= $connectionId ?>">
                                            <i class="fa fa-check-circle"></i> Test
                                        </button>
                                        <?php if (count($connections) > 1): ?>
                                            <button type="button" class="btn btn-danger btn-xs remove-connection" data-connection-id="<?= $connectionId ?>">
                                                <i class="fa fa-trash"></i> Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <form class="ftp-config-form" data-connection-id="<?= $connectionId ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="ftp_name_<?= $connectionId ?>">Connection Name</label>
                                                    <input type="text" class="form-control" id="ftp_name_<?= $connectionId ?>"
                                                           name="ftp_name_<?= $connectionId ?>" value="<?= $connection['ftp_name'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="ftp_host_<?= $connectionId ?>">FTP Host</label>
                                                    <input type="text" class="form-control" id="ftp_host_<?= $connectionId ?>"
                                                           name="ftp_host_<?= $connectionId ?>" value="<?= $connection['ftp_host'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="ftp_port_<?= $connectionId ?>">Port</label>
                                                    <input type="number" class="form-control" id="ftp_port_<?= $connectionId ?>"
                                                           name="ftp_port_<?= $connectionId ?>" value="<?= $connection['ftp_port'] ?? 21 ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group mb-3">
                                                    <label for="ftp_user_<?= $connectionId ?>">Username</label>
                                                    <input type="text" class="form-control" id="ftp_user_<?= $connectionId ?>"
                                                           name="ftp_user_<?= $connectionId ?>" value="<?= $connection['ftp_user'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="ftp_password_<?= $connectionId ?>">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="ftp_password_<?= $connectionId ?>" name="ftp_password_<?= $connectionId ?>"
                                                           value="<?= $connection['ftp_password'] ?? '' ?>" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="ftp_password_re_<?= $connectionId ?>">Confirm Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="ftp_password_re_<?= $connectionId ?>" name="ftp_password_re_<?= $connectionId ?>"
                                                           value="<?= $connection['ftp_password_re'] ?? '' ?>" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group mb-3">
                                                    <label for="ftp_path_<?= $connectionId ?>">Default Path</label>
                                                    <input type="text" class="form-control" id="ftp_path_<?= $connectionId ?>"
                                                           name="ftp_path_<?= $connectionId ?>" value="<?= $connection['ftp_path'] ?? '/' ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="mb-3 mt-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="ftp_passive_mode_<?= $connectionId ?>" name="ftp_passive_mode_<?= $connectionId ?>"
                                                            <?= isset($connection['ftp_passive_mode']) && $connection['ftp_passive_mode'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="ftp_passive_mode_<?= $connectionId ?>">Enable Passive Mode</label>
                                                    </div>
                                                    <small class="form-text text-muted">To Enable/Disable Passive Mode.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="row m-t-md">
                    <div class="col-md-12 text-center">
                        <button type="button" id="save-all-connections" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> Save All Connections
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    'use strict';

    /**
     * FTP Configuration Manager
     * Modern ES6+ implementation for FTP connections management
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize state
        let connectionCounter = document.querySelectorAll('.ftp-connection').length;

        /**
         * Add a new FTP connection
         */
        const addConnection = () => {
            connectionCounter++;
            const connectionId = connectionCounter;

            // Get the template and clone it
            const template = document.getElementById('ftp-connection-template');
            const newConnection = template.content.cloneNode(true);

            // Replace all __ID__ placeholders with the actual connection ID
            replaceTemplateId(newConnection, '__ID__', connectionId);

            // Add the new connection to the container
            document.getElementById('ftp-connections-container').appendChild(newConnection);

            // Initialize iCheck if available
            if (typeof $.fn !== 'undefined' && $.fn.iCheck) {
                $(`#connection-${connectionId} .i-checks input`).iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            }
        };

        /**
         * Replace all occurrences of a placeholder in a template with actual value
         *
         * @param {DocumentFragment} fragment - The template fragment
         * @param {string} placeholder - The placeholder to replace
         * @param {string|number} value - The value to replace with
         */
        const replaceTemplateId = (fragment, placeholder, value) => {
            // Replace in element IDs
            fragment.querySelectorAll(`[id*="${placeholder}"]`).forEach(el => {
                el.id = el.id.replace(placeholder, value);
            });

            // Replace in element names
            fragment.querySelectorAll(`[name*="${placeholder}"]`).forEach(el => {
                el.name = el.name.replace(placeholder, value);
            });

            // Replace in data attributes
            fragment.querySelectorAll(`[data-connection-id="${placeholder}"]`).forEach(el => {
                el.setAttribute('data-connection-id', value);
            });

            // Replace in labels' for attributes
            fragment.querySelectorAll(`[for*="${placeholder}"]`).forEach(el => {
                el.setAttribute('for', el.getAttribute('for').replace(placeholder, value));
            });

            // Replace in the connection div ID
            const connectionDiv = fragment.querySelector('.ftp-connection');
            if (connectionDiv && connectionDiv.id.includes(placeholder)) {
                connectionDiv.id = connectionDiv.id.replace(placeholder, value);
            }
        };

        /**
         * Test an FTP connection
         * @param {number} connectionId - The ID of the connection to test
         */
        const testConnection = async (connectionId) => {
            try {
                const form = document.querySelector(`form[data-connection-id="${connectionId}"]`);
                const formData = new FormData(form);
                formData.append('type', 'ftp');
                formData.append('connection_id', connectionId);

                // Display loading indicator
                const testButton = document.querySelector(`.test-connection[data-connection-id="${connectionId}"]`);
                const originalContent = testButton ? testButton.innerHTML : null;

                if (testButton) {
                    testButton.disabled = true;
                    testButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Testing...';
                }

                const response = await fetch('/configure/test-connection', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Restore button state
                if (testButton && originalContent) {
                    testButton.disabled = false;
                    testButton.innerHTML = originalContent;
                }

                if (data.success) {
                    toastr.success('Connection successful: ' + data.message);
                } else {
                    toastr.error('Connection failed: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                toastr.error('An error occurred while testing the connection');

                // Restore button state on error
                const testButton = document.querySelector(`.test-connection[data-connection-id="${connectionId}"]`);
                if (testButton) {
                    testButton.disabled = false;
                    testButton.innerHTML = '<i class="fa fa-check-circle"></i> Test';
                }
            }
        };

        /**
         * Remove an FTP connection
         * @param {number} connectionId - The ID of the connection to remove
         */
        const removeConnection = (connectionId) => {
            const connection = document.getElementById(`connection-${connectionId}`);
            if (connection) {
                connection.remove();
            }
        };

        /**
         * Save all FTP connections
         */
        const saveAllConnections = async () => {
            await handleConfigSubmit(
                document.querySelector('.ftp-config-form'), // First form for submit button
                '/configure/ftp/save',
                {
                    collectFromForms: '.ftp-config-form',
                    submitSelector: '#save-all-connections'
                }
            );
        };

        /**
         * Set up event listeners
         */
        const setupEventListeners = () => {
            // Add connection button
            const addButton = document.getElementById('add-ftp-connection');
            if (addButton) {
                addButton.addEventListener('click', addConnection);
            }

            // Save all connections button
            const saveButton = document.getElementById('save-all-connections');
            if (saveButton) {
                saveButton.addEventListener('click', saveAllConnections);
            }

            // Use event delegation for dynamically added elements
            document.addEventListener('click', (event) => {
                // Test connection button
                if (event.target.closest('.test-connection')) {
                    const button = event.target.closest('.test-connection');
                    const connectionId = button.dataset.connectionId;
                    testConnection(connectionId);
                }

                // Remove connection button
                if (event.target.closest('.remove-connection')) {
                    const button = event.target.closest('.remove-connection');
                    const connectionId = button.dataset.connectionId;
                    removeConnection(connectionId);
                }
            });
        };

        // Initialize i-checks
        if (typeof $.fn !== 'undefined' && $.fn.iCheck) {
            $('.i-checks input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        }

        // Initialize the application
        setupEventListeners();
    });
</script>