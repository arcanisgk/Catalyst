<div class="row">
    <!-- Template for new database connections -->
    <template id="db-connection-template">
        <div class="db-connection" id="connection-__ID__">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>New Database Connection</h5>
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
                    <form class="db-config-form" data-connection-id="__ID__">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_name___ID__">Database Name</label>
                                    <input type="text" class="form-control" id="db_name___ID__"
                                           name="db_name___ID__" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_host___ID__">Database Host</label>
                                    <input type="text" class="form-control" id="db_host___ID__"
                                           name="db_host___ID__" value="localhost" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="db_port___ID__">Port</label>
                                    <input type="number" class="form-control" id="db_port___ID__"
                                           name="db_port___ID__" value="3306" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="db_user___ID__">Username</label>
                                    <input type="text" class="form-control" id="db_user___ID__"
                                           name="db_user___ID__" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" for="db_password___ID__">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="db_password___ID__" name="db_password___ID__" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="db_password_re___ID__">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="db_password_re___ID__" name="db_password_re___ID__" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
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
                <h5>Database Connections</h5>
                <div class="ibox-tools">
                    <button type="button" id="add-db-connection" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add Connection
                    </button>
                </div>
            </div>
            <div class="ibox-content">
                <div id="db-connections-container">
                    <?php
                    $connections = $configData ?? [];
                    if (empty($connections)) {
                        $connections = ['db1' => [
                            'db_name' => '',
                            'db_host' => 'localhost',
                            'db_port' => 3306,
                            'db_user' => '',
                            'db_password' => '',
                            'db_password_re' => ''
                        ]];
                    }

                    foreach ($connections as $id => $connection):
                        $connectionId = substr($id, 2); // Extract the numeric part (db1 -> 1)
                        ?>
                        <div class="db-connection" id="connection-<?= $connectionId ?>">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><?= $connection['db_name'] ? $connection['db_name'] . ' Database' : 'New Database Connection' ?></h5>
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
                                    <form class="db-config-form" data-connection-id="<?= $connectionId ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="db_name_<?= $connectionId ?>">Database Name</label>
                                                    <input type="text" class="form-control" id="db_name_<?= $connectionId ?>"
                                                           name="db_name_<?= $connectionId ?>" value="<?= $connection['db_name'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="db_host_<?= $connectionId ?>">Database Host</label>
                                                    <input type="text" class="form-control" id="db_host_<?= $connectionId ?>"
                                                           name="db_host_<?= $connectionId ?>" value="<?= $connection['db_host'] ?? 'localhost' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="db_port_<?= $connectionId ?>">Port</label>
                                                    <input type="number" class="form-control" id="db_port_<?= $connectionId ?>"
                                                           name="db_port_<?= $connectionId ?>" value="<?= $connection['db_port'] ?? 3306 ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group mb-3">
                                                    <label for="db_user_<?= $connectionId ?>">Username</label>
                                                    <input type="text" class="form-control" id="db_user_<?= $connectionId ?>"
                                                           name="db_user_<?= $connectionId ?>" value="<?= $connection['db_user'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label" for="db_password_<?= $connectionId ?>">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="db_password_<?= $connectionId ?>" name="db_password_<?= $connectionId ?>"
                                                           value="<?= $connection['db_password'] ?? '' ?>" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="db_password_re_<?= $connectionId ?>">Confirm Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="db_password_re_<?= $connectionId ?>" name="db_password_re_<?= $connectionId ?>"
                                                           value="<?= $connection['db_password_re'] ?? '' ?>" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
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

<script src="<?= isset($asset) ? $asset('js/bd-test.js') : 'assets/js/bd-test.js' ?>"></script>

<script>
    'use strict';

    /**
     * Database Configuration Manager
     * Modern ES6+ implementation for database connections management
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize state
        let connectionCounter = document.querySelectorAll('.db-connection').length;

        /**
         * Add a new database connection
         */
        const addConnection = () => {
            connectionCounter++;
            const connectionId = connectionCounter;

            // Get the template and clone it
            const template = document.getElementById('db-connection-template');
            const newConnection = template.content.cloneNode(true);

            // Replace all __ID__ placeholders with the actual connection ID
            replaceTemplateId(newConnection, '__ID__', connectionId);

            // Add the new connection to the container
            document.getElementById('db-connections-container').appendChild(newConnection);
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
            const connectionDiv = fragment.querySelector('.db-connection');
            if (connectionDiv && connectionDiv.id.includes(placeholder)) {
                connectionDiv.id = connectionDiv.id.replace(placeholder, value);
            }
        };

        /**
         * Test a database connection
         * @param {number} connectionId - The ID of the connection to test
         */
        const testConnection = async (connectionId) => {
            const form = document.querySelector(`form[data-connection-id="${connectionId}"]`);
            if (form && window.testDatabaseConnection) {
                await window.testDatabaseConnection(connectionId, form);
            }
        };

        /**
         * Remove a database connection
         * @param {number} connectionId - The ID of the connection to remove
         */
        const removeConnection = (connectionId) => {
            const connection = document.getElementById(`connection-${connectionId}`);
            if (connection) {
                connection.remove();
            }
        };

        /**
         * Save all database connections
         */
        const saveAllConnections = async () => {
            await handleConfigSubmit(
                document.querySelector('.db-config-form'), // Use any form as the base for the submit button
                '/configure/db/save',
                {
                    collectFromForms: '.db-config-form', // Collect from all DB forms
                    submitSelector: '#save-all-connections'
                }
            );
        };

        /**
         * Set up event listeners using event delegation
         */
        const setupEventListeners = () => {
            // Add connection button
            const addButton = document.getElementById('add-db-connection');
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

        // Initialize the application
        setupEventListeners();
    });
</script>