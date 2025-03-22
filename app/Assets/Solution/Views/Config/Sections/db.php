<div class="row">
    <div class="col-lg-12">
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
                    <div class="db-connection-header">
                        <h4><?= $connection['db_name'] ? $connection['db_name'] . ' Database' : 'New Database Connection' ?></h4>
                    </div>
                    <div class="db-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="<?= $connectionId ?>">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <?php if (count($connections) > 1): ?>
                            <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="<?= $connectionId ?>">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        <?php endif; ?>
                    </div>

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
                                <div class="form-group mb-3">
                                    <label for="db_password_<?= $connectionId ?>">Password</label>
                                    <input type="password" class="form-control" id="db_password_<?= $connectionId ?>"
                                           name="db_password_<?= $connectionId ?>" value="<?= $connection['db_password'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_password_re_<?= $connectionId ?>">Confirm Password</label>
                                    <input type="password" class="form-control" id="db_password_re_<?= $connectionId ?>"
                                           name="db_password_re_<?= $connectionId ?>" value="<?= $connection['db_password_re'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="add-connection-container text-center">
            <button type="button" id="add-db-connection" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Database Connection
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
        $('#add-db-connection').on('click', function () {
            connectionCounter++;
            const connectionId = connectionCounter;

            const newConnection = `
                <div class="db-connection" id="connection-${connectionId}">
                    <div class="db-connection-header">
                        <h4>New Database Connection</h4>
                    </div>
                    <div class="db-connection-actions">
                        <button type="button" class="btn btn-sm btn-success test-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-check-circle"></i> Test Connection
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove-connection" data-connection-id="${connectionId}">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>

                    <form class="db-config-form" data-connection-id="${connectionId}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_name_${connectionId}">Database Name</label>
                                    <input type="text" class="form-control" id="db_name_${connectionId}"
                                           name="db_name_${connectionId}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_host_${connectionId}">Database Host</label>
                                    <input type="text" class="form-control" id="db_host_${connectionId}"
                                                                                      name="db_host_${connectionId}" value="localhost" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="db_port_${connectionId}">Port</label>
                                    <input type="number" class="form-control" id="db_port_${connectionId}"
                                           name="db_port_${connectionId}" value="3306" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="db_user_${connectionId}">Username</label>
                                    <input type="text" class="form-control" id="db_user_${connectionId}"
                                           name="db_user_${connectionId}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_password_${connectionId}">Password</label>
                                    <input type="password" class="form-control" id="db_password_${connectionId}"
                                           name="db_password_${connectionId}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="db_password_re_${connectionId}">Confirm Password</label>
                                    <input type="password" class="form-control" id="db_password_re_${connectionId}"
                                           name="db_password_re_${connectionId}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            `;

            $('#db-connections-container').append(newConnection);
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
                data: formData + '&type=db&connection_id=' + connectionId,
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
            const allForms = $('.db-config-form');
            let formData = '';

            allForms.each(function () {
                formData += $(this).serialize() + '&';
            });

            $.ajax({
                url: '/configure/db/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Database configurations saved successfully');
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
