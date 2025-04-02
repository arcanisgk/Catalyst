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

use Catalyst\Helpers\Security\CsrfProtection;

$csrfProtection = CsrfProtection::getInstance();

?>
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
                    <?= $csrfProtection->getTokenField('mail_config'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="app_setting" name="app_setting"
                                        <?= isset($configData['app_setting']) && $configData['app_setting'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="app_setting">Enable Application Settings Panel</label>
                                </div>
                                <small class="form-text text-muted">Allows access to this configuration interface.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-1">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="dev_tool" name="dev_tool"
                                        <?= isset($configData['dev_tool']) && $configData['dev_tool'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="dev_tool">Enable Developer Tools</label>
                                </div>
                                <small class="form-text text-muted">Enables debugging and development tools</small>
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

                    <!-- Security Config Change Password -->

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Configuración de Seguridad</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="config_username">Nombre de Usuario para Configuración</label>
                                <input type="text" class="form-control" id="config_username" name="config_username"
                                       value="<?= $configData['config']['username'] ?? 'admin' ?>">
                                <small class="form-text text-muted">Usuario para acceder al panel de configuración</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="config_password">Contraseña para Configuración</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="config_password" name="config_password"
                                           value="<?= $configData['config']['password'] ?? 'admin' ?>">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Contraseña para acceder al panel de configuración</small>
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

            // Verifica que el token CSRF esté presente
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            console.log('CSRF Token:', csrfToken?.value); // Para depuración

            await handleConfigSubmit(configForm, '/configure/tools/save');
        });
    });
</script>