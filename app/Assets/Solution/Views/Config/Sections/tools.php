<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Developer Tools Configuration</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/configure">Configuration</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Developer Tools</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Developer Tools & Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form id="tools-config-form" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <label>
                                            <input type="checkbox" id="app_setting" name="app_setting"
                                                <?= isset($configData['app_setting']) && $configData['app_setting'] ? 'checked' : '' ?>>
                                            <i></i> Enable Application Settings Panel
                                        </label>
                                    </div>
                                    <small class="text-muted">Allows access to this configuration interface</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="i-checks">
                                        <label>
                                            <input type="checkbox" id="dev_tool" name="dev_tool"
                                                <?= isset($configData['dev_tool']) && $configData['dev_tool'] ? 'checked' : '' ?>>
                                            <i></i> Enable Developer Tools
                                        </label>
                                    </div>
                                    <small class="text-muted">Enables debugging and development tools</small>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="translate_tool">Translation Tool</label>
                                    <select class="form-control" id="translate_tool" name="translate_tool">
                                        <option value="" <?= empty($configData['translate_tool']) ? 'selected' : '' ?>>None</option>
                                        <option value="basic" <?= ($configData['translate_tool'] ?? '') === 'basic' ? 'selected' : '' ?>>Basic Translation Editor</option>
                                        <option value="advanced" <?= ($configData['translate_tool'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced Translation Suite</option>
                                    </select>
                                    <small class="text-muted">Select which translation tool to enable for content management</small>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-save"></i> Save Configuration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    'use strict';

    /**
     * Tools Configuration Manager
     * Modern ES6+ implementation for developer tools configuration
     */
    document.addEventListener('DOMContentLoaded', () => {
        // DOM Elements
        const configForm = document.getElementById('tools-config-form');

        // Initialize iCheck
        if ($.fn.iCheck) {
            $('.i-checks input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        }

        // Form submission handler
        configForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            await handleConfigSubmit(configForm, '/configure/tools/save');
        });
    });
</script>