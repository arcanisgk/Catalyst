<div class="row">
    <div class="col-lg-12">
        <form id="tools-config-form">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Developer Tools</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="app_setting" name="app_setting"
                                    <?= isset($configData['app_setting']) && $configData['app_setting'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="app_setting">Enable Application Settings Panel</label>
                                <small class="d-block text-muted">Allows access to this configuration interface</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="dev_tool" name="dev_tool"
                                    <?= isset($configData['dev_tool']) && $configData['dev_tool'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="dev_tool">Enable Developer Tools</label>
                                <small class="d-block text-muted">Enables debugging and development tools</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="translate_tool">Translation Tool</label>
                                <select class="form-control" id="translate_tool" name="translate_tool">
                                    <option value="" <?= empty($configData['translate_tool']) ? 'selected' : '' ?>>None</option>
                                    <option value="basic" <?= ($configData['translate_tool'] ?? '') === 'basic' ? 'selected' : '' ?>>Basic Translation Editor</option>
                                    <option value="advanced" <?= ($configData['translate_tool'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced Translation Suite</option>
                                </select>
                                <small class="form-text text-muted">Select which translation tool to enable for content management</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Save Configuration</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Form submission
        $('#tools-config-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/configure/tools/save',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Tools configuration saved successfully');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while saving the configuration');
                }
            });
        });
    });
</script>
