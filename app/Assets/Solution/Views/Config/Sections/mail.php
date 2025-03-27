<div class="row">
    <!-- Template for new mail connections -->
    <template id="mail-connection-template">
        <div class="mail-connection" id="connection-__ID__">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>New Mail Connection</h5>
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
                    <form class="mail-config-form" data-connection-id="__ID__">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_name___ID__">Connection Name</label>
                                    <input type="text" class="form-control" id="mail_name___ID__"
                                           name="mail_name___ID__" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_host___ID__">Mail Server Host</label>
                                    <input type="text" class="form-control" id="mail_host___ID__"
                                           name="mail_host___ID__" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_port___ID__">Port</label>
                                    <input type="number" class="form-control" id="mail_port___ID__"
                                           name="mail_port___ID__" value="587" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_protocol___ID__">Security Protocol</label>
                                    <select class="form-control" id="mail_protocol___ID__" name="mail_protocol___ID__">
                                        <option value="tls" selected>TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_debug___ID__">Debug Level</label>
                                    <select class="form-control" id="mail_debug___ID__" name="mail_debug___ID__">
                                        <option value="0" selected>Off</option>
                                        <option value="1">Client</option>
                                        <option value="2">Client & Server</option>
                                        <option value="3">Client, Server & Connection</option>
                                        <option value="4">Low Level</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_user___ID__">Username</label>
                                    <input type="text" class="form-control" id="mail_user___ID__"
                                           name="mail_user___ID__" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_password___ID__">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="mail_password___ID__" name="mail_password___ID__" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_support___ID__">Support Email</label>
                                    <input type="email" class="form-control" id="mail_support___ID__"
                                           name="mail_support___ID__">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_postmaster___ID__">Postmaster Email</label>
                                    <input type="email" class="form-control" id="mail_postmaster___ID__"
                                           name="mail_postmaster___ID__">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_default___ID__">Default From</label>
                                    <input type="text" class="form-control" id="mail_default___ID__"
                                           name="mail_default___ID__" placeholder="email@example.com::Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_test_smg___ID__">Test Message Prefix</label>
                                    <input type="text" class="form-control" id="mail_test_smg___ID__"
                                           name="mail_test_smg___ID__" value="[TEST]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_test___ID__" name="mail_test___ID__">
                                        <label class="form-check-label" for="mail_test___ID__">Enable Test Mode</label>
                                    </div>
                                    <small class="form-text text-muted">For dev only, be careful.</small>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <h4>Authentication Options</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_authentication___ID__" name="mail_authentication___ID__" checked>
                                        <label class="form-check-label" for="mail_authentication___ID__">Authentication</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_verify___ID__" name="mail_verify___ID__" checked>
                                        <label class="form-check-label" for="mail_verify___ID__">Verify SSL Certificate</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_verify_peer_name___ID__"
                                               name="mail_verify_peer_name___ID__" checked>
                                        <label class="form-check-label" for="mail_verify_peer_name___ID__">Verify Peer Name</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_self_signed___ID__"
                                               name="mail_self_signed___ID__">
                                        <label class="form-check-label" for="mail_self_signed___ID__">Allow Self-Signed</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <h4>DKIM Options</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_dkim_sign___ID__">DKIM Selector</label>
                                    <input type="text" class="form-control" id="mail_dkim_sign___ID__"
                                           name="mail_dkim_sign___ID__">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="mail_dkim_passphrase___ID__">DKIM Passphrase</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="mail_dkim_passphrase___ID__" name="mail_dkim_passphrase___ID__">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="mail_dkim_copy_header_fields___ID__"
                                               name="mail_dkim_copy_header_fields___ID__">
                                        <label class="form-check-label" for="mail_dkim_copy_header_fields___ID__">Copy Header Fields</label>
                                    </div>
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
                <h5>Mail Connections</h5>
                <div class="ibox-tools">
                    <button type="button" id="add-mail-connection" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add Mail Connection
                    </button>
                </div>
            </div>
            <div class="ibox-content">
                <div id="mail-connections-container">
                    <?php
                    $connections = $configData ?? [];
                    if (empty($connections)) {
                        $connections = ['mail1' => [
                            'mail_name' => '',
                            'mail_host' => '',
                            'mail_port' => 587,
                            'mail_user' => '',
                            'mail_support' => '',
                            'mail_postmaster' => '',
                            'mail_password' => '',
                            'mail_default' => '',
                            'mail_test_smg' => '[TEST]',
                            'mail_protocol' => 'tls',
                            'mail_authentication' => true,
                            'mail_verify' => true,
                            'mail_verify_peer_name' => true,
                            'mail_self_signed' => false,
                            'mail_dkim_sign' => '',
                            'mail_dkim_passphrase' => '',
                            'mail_dkim_copy_header_fields' => false,
                            'mail_debug' => 0,
                            'mail_test' => false
                        ]];
                    }

                    foreach ($connections

                             as $id => $connection):
                        $connectionId = substr($id, 4); // Extract the numeric part (mail1 -> 1)
                        ?>
                        <div class="mail-connection" id="connection-<?= $connectionId ?>">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><?= $connection['mail_name'] ? $connection['mail_name'] : 'New Mail Connection' ?></h5>
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
                                    <form class="mail-config-form" data-connection-id="<?= $connectionId ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_name_<?= $connectionId ?>">Connection Name</label>
                                                    <input type="text" class="form-control" id="mail_name_<?= $connectionId ?>"
                                                           name="mail_name_<?= $connectionId ?>" value="<?= $connection['mail_name'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_host_<?= $connectionId ?>">Mail Server Host</label>
                                                    <input type="text" class="form-control" id="mail_host_<?= $connectionId ?>"
                                                           name="mail_host_<?= $connectionId ?>" value="<?= $connection['mail_host'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_port_<?= $connectionId ?>">Port</label>
                                                    <input type="number" class="form-control" id="mail_port_<?= $connectionId ?>"
                                                           name="mail_port_<?= $connectionId ?>" value="<?= $connection['mail_port'] ?? 587 ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_protocol_<?= $connectionId ?>">Security Protocol</label>
                                                    <select class="form-control" id="mail_protocol_<?= $connectionId ?>" name="mail_protocol_<?= $connectionId ?>">
                                                        <option value="tls" <?= ($connection['mail_protocol'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                                        <option value="ssl" <?= ($connection['mail_protocol'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                                        <option value="none" <?= ($connection['mail_protocol'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_debug_<?= $connectionId ?>">Debug Level</label>
                                                    <select class="form-control" id="mail_debug_<?= $connectionId ?>" name="mail_debug_<?= $connectionId ?>">
                                                        <option value="0" <?= ($connection['mail_debug'] ?? 0) == 0 ? 'selected' : '' ?>>Off</option>
                                                        <option value="1" <?= ($connection['mail_debug'] ?? 0) == 1 ? 'selected' : '' ?>>Client</option>
                                                        <option value="2" <?= ($connection['mail_debug'] ?? 0) == 2 ? 'selected' : '' ?>>Client & Server</option>
                                                        <option value="3" <?= ($connection['mail_debug'] ?? 0) == 3 ? 'selected' : '' ?>>Client, Server & Connection</option>
                                                        <option value="4" <?= ($connection['mail_debug'] ?? 0) == 4 ? 'selected' : '' ?>>Low Level</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_user_<?= $connectionId ?>">Username</label>
                                                    <input type="text" class="form-control" id="mail_user_<?= $connectionId ?>"
                                                           name="mail_user_<?= $connectionId ?>" value="<?= $connection['mail_user'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="mail_password_<?= $connectionId ?>">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="mail_password_<?= $connectionId ?>" name="mail_password_<?= $connectionId ?>"
                                                           value="<?= $connection['mail_password'] ?? '' ?>" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_support_<?= $connectionId ?>">Support Email</label>
                                                    <input type="email" class="form-control" id="mail_support_<?= $connectionId ?>"
                                                           name="mail_support_<?= $connectionId ?>" value="<?= $connection['mail_support'] ?? '' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_postmaster_<?= $connectionId ?>">Postmaster Email</label>
                                                    <input type="email" class="form-control" id="mail_postmaster_<?= $connectionId ?>"
                                                           name="mail_postmaster_<?= $connectionId ?>" value="<?= $connection['mail_postmaster'] ?? '' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="mail_default_<?= $connectionId ?>">Default From</label>
                                                    <input type="text" class="form-control" id="mail_default_<?= $connectionId ?>"
                                                           name="mail_default_<?= $connectionId ?>" value="<?= $connection['mail_default'] ?? '' ?>"
                                                           placeholder="email@example.com::Name">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_test_smg_<?= $connectionId ?>">Test Message Prefix</label>
                                                    <input type="text" class="form-control" id="mail_test_smg_<?= $connectionId ?>"
                                                           name="mail_test_smg_<?= $connectionId ?>" value="<?= $connection['mail_test_smg'] ?? '[TEST]' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3 mt-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_test_<?= $connectionId ?>" name="mail_test_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_test']) && $connection['mail_test'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_test_<?= $connectionId ?>">Enable Test Mode</label>
                                                    </div>
                                                    <small class="form-text text-muted">For dev only, be careful.</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <h4>Authentication Options</h4>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_authentication_<?= $connectionId ?>" name="mail_authentication_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_authentication']) && $connection['mail_authentication'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_authentication_<?= $connectionId ?>">Authentication</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_verify_<?= $connectionId ?>" name="mail_verify_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_verify']) && $connection['mail_verify'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_verify_<?= $connectionId ?>">Verify SSL Certificate</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_verify_peer_name_<?= $connectionId ?>"
                                                               name="mail_verify_peer_name_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_verify_peer_name']) && $connection['mail_verify_peer_name'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_verify_peer_name_<?= $connectionId ?>">Verify Peer Name</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_self_signed_<?= $connectionId ?>"
                                                               name="mail_self_signed_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_self_signed']) && $connection['mail_self_signed'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_self_signed_<?= $connectionId ?>">Allow Self-Signed</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <h4>DKIM Options</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="mail_dkim_sign_<?= $connectionId ?>">DKIM Selector</label>
                                                    <input type="text" class="form-control" id="mail_dkim_sign_<?= $connectionId ?>"
                                                           name="mail_dkim_sign_<?= $connectionId ?>" value="<?= $connection['mail_dkim_sign'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label" for="mail_dkim_passphrase_<?= $connectionId ?>">DKIM Passphrase</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="mail_dkim_passphrase_<?= $connectionId ?>" name="mail_dkim_passphrase_<?= $connectionId ?>"
                                                           value="<?= $connection['mail_dkim_passphrase'] ?? '' ?>">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="mail_dkim_copy_header_fields_<?= $connectionId ?>"
                                                               name="mail_dkim_copy_header_fields_<?= $connectionId ?>"
                                                            <?= isset($connection['mail_dkim_copy_header_fields']) && $connection['mail_dkim_copy_header_fields'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="mail_dkim_copy_header_fields_<?= $connectionId ?>">Copy Header Fields</label>
                                                    </div>
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
     * Mail Configuration Manager
     * Modern ES6+ implementation for mail connections management
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize state
        let connectionCounter = document.querySelectorAll('.mail-connection').length;

        /**
         * Add a new mail connection
         */
        const addConnection = () => {
            connectionCounter++;
            const connectionId = connectionCounter;

            // Get the template and clone it
            const template = document.getElementById('mail-connection-template');
            const newConnection = template.content.cloneNode(true);

            // Replace all __ID__ placeholders with the actual connection ID
            replaceTemplateId(newConnection, '__ID__', connectionId);

            // Add the new connection to the container
            document.getElementById('mail-connections-container').appendChild(newConnection);
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
            const connectionDiv = fragment.querySelector('.mail-connection');
            if (connectionDiv && connectionDiv.id.includes(placeholder)) {
                connectionDiv.id = connectionDiv.id.replace(placeholder, value);
            }
        };

        /**
         * Test a mail connection
         * @param {number} connectionId - The ID of the connection to test
         */
        const testConnection = async (connectionId) => {
            try {
                const form = document.querySelector(`form[data-connection-id="${connectionId}"]`);
                const formData = new FormData(form);
                formData.append('type', 'mail');
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
                console.error('Test connection error:', error);
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
         * Remove a mail connection
         * @param {number} connectionId - The ID of the connection to remove
         */
        const removeConnection = (connectionId) => {
            const connectionElement = document.getElementById(`connection-${connectionId}`);
            if (connectionElement) {
                connectionElement.remove();
            }
        };

        /**
         * Save all mail connections
         */
        const saveAllConnections = async () => {
            await handleConfigSubmit(
                document.querySelector('.mail-config-form'),
                '/configure/mail/save',
                {
                    collectFromForms: '.mail-config-form',
                    submitSelector: '#save-all-connections'
                }
            );
        };

        // Event Listeners
        document.getElementById('add-mail-connection').addEventListener('click', addConnection);
        document.getElementById('save-all-connections').addEventListener('click', saveAllConnections);

        // Event delegation for dynamically created elements
        document.addEventListener('click', (event) => {
            // Test connection button click
            if (event.target.closest('.test-connection')) {
                const button = event.target.closest('.test-connection');
                const connectionId = button.getAttribute('data-connection-id');
                testConnection(connectionId);
            }

            // Remove connection button click
            if (event.target.closest('.remove-connection')) {
                const button = event.target.closest('.remove-connection');
                const connectionId = button.getAttribute('data-connection-id');
                removeConnection(connectionId);
            }
        });

        // Initialize iCheck if available
        if (typeof $.fn !== 'undefined' && $.fn.iCheck) {
            $('.i-checks input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        }
    });
</script>
