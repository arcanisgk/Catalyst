<div class="row">
    <div class="col-lg-12">
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
                    <div class="ftp-connection-header">
                        <h4><?= $connection['ftp_name'] ? $connection['ftp_name'] : 'New FTP Connection' ?></h4>
                    </div>
                    <div class="ftp-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="<?= $connectionId ?>">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <?php if (count($connections) > 1): ?>
                            <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="<?= $connectionId ?>">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        <?php endif; ?>
                    </div>

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
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_password_<?= $connectionId ?>">Password</label>
                                    <input type="password" class="form-control" id="ftp_password_<?= $connectionId ?>"
                                           name="ftp_password_<?= $connectionId ?>" value="<?= $connection['ftp_password'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_password_re_<?= $connectionId ?>">Confirm Password</label>
                                    <input type="password" class="form-control" id="ftp_password_re_<?= $connectionId ?>"
                                           name="ftp_password_re_<?= $connectionId ?>" value="<?= $connection['ftp_password_re'] ?? '' ?>">
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
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="ftp_passive_mode_<?= $connectionId ?>"
                                           name="ftp_passive_mode_<?= $connectionId ?>" <?= isset($connection['ftp_passive_mode']) && $connection['ftp_passive_mode'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ftp_passive_mode_<?= $connectionId ?>">Passive Mode</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="add-ftp-container text-center">
            <button type="button" id="add-ftp-connection" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add FTP Connection
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
        $('#add-ftp-connection').on('click', function () {
            connectionCounter++;
            const connectionId = connectionCounter;

            const newConnection = `
                <div class="ftp-connection" id="connection-${connectionId}">
                    <div class="ftp-connection-header">
                        <h4>New FTP Connection</h4>
                    </div>
                    <div class="ftp-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>

                    <form class="ftp-config-form" data-connection-id="${connectionId}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_name_${connectionId}">Connection Name</label>
                                    <input type="text" class="form-control" id="ftp_name_${connectionId}"
                                           name="ftp_name_${connectionId}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_host_${connectionId}">FTP Host</label>
                                    <input type="text" class="form-control" id="ftp_host_${connectionId}"
                                           name="ftp_host_${connectionId}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ftp_port_${connectionId}">Port</label>
                                    <input type="number" class="form-control" id="ftp_port_${connectionId}"
                                           name="ftp_port_${connectionId}" value="21" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ftp_user_${connectionId}">Username</label>
                                    <input type="text" class="form-control" id="ftp_user_${connectionId}"
                                           name="ftp_user_${connectionId}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_password_${connectionId}">Password</label>
                                    <input type="password" class="form-control" id="ftp_password_${connectionId}"
                                           name="ftp_password_${connectionId}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ftp_password_re_${connectionId}">Confirm Password</label>
                                    <input type="password" class="form-control" id="ftp_password_re_${connectionId}"
                                           name="ftp_password_re_${connectionId}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                                                        <label for="ftp_path_${connectionId}">Default Path</label>
                                    <input type="text" class="form-control" id="ftp_path_${connectionId}"
                                           name="ftp_path_${connectionId}" value="/">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="ftp_passive_mode_${connectionId}"
                                           name="ftp_passive_mode_${connectionId}" checked>
                                    <label class="form-check-label" for="ftp_passive_mode_${connectionId}">Passive Mode</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            `;

            $('#ftp-connections-container').append(newConnection);
        });

        // Remove connection
        $(document).on('click', '.remove-connection', function () {
            const connectionId = $(this).data('connection-id');
            $('#connection-' + connectionId).remove();
        });

        // Test connection
        $(document).on('click', '.test-connection', function () {
            const connectionId = $(this).data('connection-id');
            const formData = $(`form[data-connection-id="${connectionId}"]`).serialize();

            $.ajax({
                url: '/configure/test-connection',
                type: 'POST',
                data: formData + '&type=ftp&connection_id=' + connectionId,
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

        // Save all connections
        $('#save-all-connections').on('click', function () {
            const allForms = $('.ftp-config-form');
            let formData = '';

            allForms.each(function () {
                formData += $(this).serialize() + '&';
            });

            $.ajax({
                url: '/configure/ftp/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('FTP configurations saved successfully');
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
