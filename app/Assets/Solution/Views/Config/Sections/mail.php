<div class="row">
    <div class="col-lg-12">
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
                    <div class="mail-connection-header">
                        <h4><?= $connection['mail_name'] ? $connection['mail_name'] : 'New Mail Connection' ?></h4>
                    </div>
                    <div class="mail-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="<?= $connectionId ?>">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <?php if (count($connections) > 1): ?>
                            <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="<?= $connectionId ?>">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        <?php endif; ?>
                    </div>

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
                                <div class="form-group mb-3">
                                    <label for="mail_password_<?= $connectionId ?>">Password</label>
                                    <input type="password" class="form-control" id="mail_password_<?= $connectionId ?>"
                                           name="mail_password_<?= $connectionId ?>" value="<?= $connection['mail_password'] ?? '' ?>">
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
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="mail_test_<?= $connectionId ?>"
                                           name="mail_test_<?= $connectionId ?>" <?= isset($connection['mail_test']) && $connection['mail_test'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mail_test_<?= $connectionId ?>">Enable Test Mode</label>
                                </div>
                            </div>
                        </div>

                        <div class="mail-options-section">
                            <h5>Authentication & Security Options</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_authentication_<?= $connectionId ?>"
                                               name="mail_authentication_<?= $connectionId ?>" <?= isset($connection['mail_authentication']) && $connection['mail_authentication'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mail_authentication_<?= $connectionId ?>">Authentication</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_verify_<?= $connectionId ?>"
                                               name="mail_verify_<?= $connectionId ?>" <?= isset($connection['mail_verify']) && $connection['mail_verify'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mail_verify_<?= $connectionId ?>">Verify SSL Certificate</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_verify_peer_name_<?= $connectionId ?>"
                                               name="mail_verify_peer_name_<?= $connectionId ?>" <?= isset($connection['mail_verify_peer_name']) && $connection['mail_verify_peer_name'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mail_verify_peer_name_<?= $connectionId ?>">Verify Peer Name</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_self_signed_<?= $connectionId ?>"
                                               name="mail_self_signed_<?= $connectionId ?>" <?= isset($connection['mail_self_signed']) && $connection['mail_self_signed'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mail_self_signed_<?= $connectionId ?>">Allow Self-Signed</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mail-options-section">
                            <h5>DKIM Options</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="mail_dkim_sign_<?= $connectionId ?>">DKIM Selector</label>
                                        <input type="text" class="form-control" id="mail_dkim_sign_<?= $connectionId ?>"
                                               name="mail_dkim_sign_<?= $connectionId ?>" value="<?= $connection['mail_dkim_sign'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="mail_dkim_passphrase_<?= $connectionId ?>">DKIM Passphrase</label>
                                        <input type="password" class="form-control" id="mail_dkim_passphrase_<?= $connectionId ?>"
                                               name="mail_dkim_passphrase_<?= $connectionId ?>" value="<?= $connection['mail_dkim_passphrase'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_dkim_copy_header_fields_<?= $connectionId ?>"
                                               name="mail_dkim_copy_header_fields_<?= $connectionId ?>" <?= isset($connection['mail_dkim_copy_header_fields']) && $connection['mail_dkim_copy_header_fields'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mail_dkim_copy_header_fields_<?= $connectionId ?>">Copy Header Fields</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="add-mail-container text-center">
            <button type="button" id="add-mail-connection" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Mail Connection
            </button>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <button type="button" id="save-all-connections" class="btn btn-primary btn-lg px-5">Save All Connections</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let connectionCounter = <?= count($connections) ?>;

        // Add new connection
        $('#add-mail-connection').on('click', function () {
            connectionCounter++;
            const connectionId = connectionCounter;

            const newConnection = `
                <div class="mail-connection" id="connection-${connectionId}">
                    <div class="mail-connection-header">
                        <h4>New Mail Connection</h4>
                    </div>
                    <div class="mail-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>

                    <form class="mail-config-form" data-connection-id="${connectionId}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_name_${connectionId}">Connection Name</label>
                                    <input type="text" class="form-control" id="mail_name_${connectionId}"
                                           name="mail_name_${connectionId}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_host_${connectionId}">Mail Server Host</label>
                                    <input type="text" class="form-control" id="mail_host_${connectionId}"
                                           name="mail_host_${connectionId}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_port_${connectionId}">Port</label>
                                    <input type="number" class="form-control" id="mail_port_${connectionId}"
                                           name="mail_port_${connectionId}" value="587" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_protocol_${connectionId}">Security Protocol</label>
                                    <select class="form-control" id="mail_protocol_${connectionId}" name="mail_protocol_${connectionId}">
                                        <option value="tls" selected>TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_debug_${connectionId}">Debug Level</label>
                                    <select class="form-control" id="mail_debug_${connectionId}" name="mail_debug_${connectionId}">
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
                                    <label for="mail_user_${connectionId}">Username</label>
                                    <input type="text" class="form-control" id="mail_user_${connectionId}"
                                           name="mail_user_${connectionId}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_password_${connectionId}">Password</label>
                                    <input type="password" class="form-control" id="mail_password_${connectionId}"
                                           name="mail_password_${connectionId}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_support_${connectionId}">Support Email</label>
                                    <input type="email" class="form-control" id="mail_support_${connectionId}"
                                           name="mail_support_${connectionId}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_postmaster_${connectionId}">Postmaster Email</label>
                                    <input type="email" class="form-control" id="mail_postmaster_${connectionId}"
                                           name="mail_postmaster_${connectionId}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mail_default_${connectionId}">Default From</label>
                                    <input type="text" class="form-control" id="mail_default_${connectionId}"
                                           name="mail_default_${connectionId}" placeholder="email@example.com::Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mail_test_smg_${connectionId}">Test Message Prefix</label>
                                    <input type="text" class="form-control" id="mail_test_smg_${connectionId}"
                                           name="mail_test_smg_${connectionId}" value="[TEST]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="mail_test_${connectionId}"
                                           name="mail_test_${connectionId}">
                                    <label class="form-check-label" for="mail_test_${connectionId}">Enable Test Mode</label>
                                </div>
                            </div>
                        </div>

                        <div class="mail-options-section">
                            <h5>Authentication & Security Options</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_authentication_${connectionId}"
                                               name="mail_authentication_${connectionId}" checked>
                                        <label class="form-check-label" for="mail_authentication_${connectionId}">Authentication</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_verify_${connectionId}"
                                               name="mail_verify_${connectionId}" checked>
                                        <label class="form-check-label" for="mail_verify_${connectionId}">Verify SSL Certificate</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_verify_peer_name_${connectionId}"
                                               name="mail_verify_peer_name_${connectionId}" checked>
                                        <label class="form-check-label" for="mail_verify_peer_name_${connectionId}">Verify Peer Name</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_self_signed_${connectionId}"
                                               name="mail_self_signed_${connectionId}">
                                        <label class="form-check-label" for="mail_self_signed_${connectionId}">Allow Self-Signed</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mail-options-section">
                            <h5>DKIM Options</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="mail_dkim_sign_${connectionId}">DKIM Selector</label>
                                        <input type="text" class="form-control" id="mail_dkim_sign_${connectionId}"
                                               name="mail_dkim_sign_${connectionId}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="mail_dkim_passphrase_${connectionId}">DKIM Passphrase</label>
                                        <input type="password" class="form-control" id="mail_dkim_passphrase_${connectionId}"
                                               name="mail_dkim_passphrase_${connectionId}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="mail_dkim_copy_header_fields_${connectionId}"
                                               name="mail_dkim_copy_header_fields_${connectionId}">
                                        <label class="form-check-label" for="mail_dkim_copy_header_fields_${connectionId}">Copy Header Fields</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            `;

            $('#mail-connections-container').append(newConnection);
        });

        // Test connection
        $(document).on('click', '.test-connection', function () {
            const connectionId = $(this).data('connection-id');
            const formData = $(`form[data-connection-id="${connectionId}"]`).serialize();

            $.ajax({
                url: '/configure/test-connection',
                type: 'POST',
                data: formData + '&type=mail&connection_id=' + connectionId,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Connection successful: ' + response.message);
                    } else {
                        alert('Connection failed: ' + response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while testing the connection');
                }
            });
        });

        // Remove connection
        $(document).on('click', '.remove-connection', function () {
            const connectionId = $(this).data('connection-id');
            $('#connection-' + connectionId).remove();
        });

        // Save all connections
        $('#save-all-connections').on('click', function () {
            const allForms = $('.mail-config-form');
            let formData = '';

            allForms.each(function () {
                formData += $(this).serialize() + '&';
            });

            $.ajax({
                url: '/configure/mail/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Mail configurations saved successfully');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while saving the configurations');
                }
            });
        });
    });
</script>
