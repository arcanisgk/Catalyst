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
    <div class="col-lg-12">
        <form id="app-config-form">
            <!-- Company Information Section -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Company Information</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company_name">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       value="<?= $configData['company']['company_name'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company_owner">Company Owner</label>
                                <input type="text" class="form-control" id="company_owner" name="company_owner"
                                       value="<?= $configData['company']['company_owner'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company_department">Department</label>
                                <input type="text" class="form-control" id="company_department" name="company_department"
                                       value="<?= $configData['company']['company_department'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Information Section -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Project Information</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name"
                                       value="<?= $configData['project']['project_name'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_copyright">Copyright</label>
                                <input type="text" class="form-control" id="project_copyright" name="project_copyright"
                                       value="<?= $configData['project']['project_copyright'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="eula">End User License Agreement</label>
                                <input type="text" class="form-control" id="eula" name="eula"
                                       value="<?= $configData['project']['eula'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="project_config" name="project_config"
                                    <?= isset($configData['project']['project_config']) && $configData['project']['project_config'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="project_config">Enable Project Configuration</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Host Settings Section -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Host Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domain">Domain</label>
                                <input type="text" class="form-control" id="domain" name="domain"
                                       value="<?= $configData['host']['domain'] ?? 'localhost' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="protocol">Protocol</label>
                                <select class="form-control" id="protocol" name="protocol">
                                    <option value="http" <?= ($configData['host']['protocol'] ?? 'http') === 'http' ? 'selected' : '' ?>>HTTP</option>
                                    <option value="https" <?= ($configData['host']['protocol'] ?? '') === 'https' ? 'selected' : '' ?>>HTTPS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry">Entry Point</label>
                                <select class="form-control" id="entry" name="entry">
                                    <option value="">-- Select Entry Point --</option>
                                    <option value="/usser-access" <?= ($configData['host']['entry'] ?? '') === '/usser-access' ? 'selected' : '' ?>>Login de usuario (Usser-Access)</option>
                                    <option value="/landing" <?= ($configData['host']['entry'] ?? '') === '/landing' ? 'selected' : '' ?>>Landing Page (Landing page Home)</option>
                                    <option value="/dashboard" <?= ($configData['host']['entry'] ?? '') === '/dashboard' ? 'selected' : '' ?>>Initial Dashboard View</option>
                                    <option value="/store" <?= ($configData['host']['entry'] ?? '') === '/store' ? 'selected' : '' ?>>Initial View Store</option>
                                    <option value="/blog" <?= ($configData['host']['entry'] ?? '') === '/blog' ? 'selected' : '' ?>>Initial Blog View</option>
                                </select>
                                <small class="text-muted">This will be the default entry point when accessing your application</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="license">License Key</label>
                                <input type="text" class="form-control" id="license" name="license"
                                       value="<?= $configData['host']['license'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="free" name="free"
                                    <?= isset($configData['host']['free']) && $configData['host']['free'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="free">Free License</label>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="humanitarian" name="humanitarian"
                                    <?= isset($configData['host']['humanitarian']) && $configData['host']['humanitarian'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="humanitarian">Humanitarian License</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="m_lang" name="m_lang"
                                    <?= isset($configData['host']['m-lang']) && $configData['host']['m-lang'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="m_lang">Multi-language Support</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language Settings Section -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Language Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lang">Default Language</label>
                                <select class="form-control" id="lang" name="lang">
                                    <?php $languages = $configData['host']['s-lang'] ?? ['en' => 'English']; ?>
                                    <?php foreach ($languages as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= ($configData['host']['lang'] ?? 'en') === $code ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Supported Languages</label>
                            <div id="languages-container" class="mb-3">
                                <?php
                                $languages = $configData['host']['s-lang'] ?? ['en' => 'English'];
                                foreach ($languages as $code => $name):
                                    ?>
                                    <div class="language-tag">
                                        <input type="hidden" name="lang_<?= $code ?>" value="<?= $name ?>">
                                        <?= $code ?>: <?= $name ?>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 remove-language">
                                            <i class="fa fa-times-circle"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="new-lang-code" placeholder="Code (e.g., fr)">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="new-lang-name" placeholder="Name (e.g., French)">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100" id="add-language">
                                        <i class="fa fa-plus"></i> Add Language
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row m-t-lg">
                <div class="col-md-12 text-center mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Save Configuration
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    'use strict';

    /**
     * Application Configuration Manager
     * Modern ES6+ implementation for application settings
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize iCheck
        if ($.fn.iCheck) {
            $('.i-checks input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        }

        // Get references to DOM elements
        const addLanguageBtn = document.getElementById('add-language');
        const langCodeInput = document.getElementById('new-lang-code');
        const langNameInput = document.getElementById('new-lang-name');
        const languagesContainer = document.getElementById('languages-container');
        const appConfigForm = document.getElementById('app-config-form');

        // Add new language functionality
        if (addLanguageBtn && langCodeInput && langNameInput && languagesContainer) {
            addLanguageBtn.addEventListener('click', () => {
                const langCode = langCodeInput.value.trim();
                const langName = langNameInput.value.trim();

                if (langCode && langName) {
                    // Create new language tag
                    const langTag = document.createElement('div');
                    langTag.className = 'language-tag';
                    langTag.innerHTML = `
                        <input type="hidden" name="lang_${langCode}" value="${langName}">
                        ${langCode}: ${langName}
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 remove-language">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    `;

                    // Add to container
                    languagesContainer.appendChild(langTag);

                    // Clear input fields
                    langCodeInput.value = '';
                    langNameInput.value = '';
                }
            });
        }

        // Remove language functionality using event delegation
        if (languagesContainer) {
            languagesContainer.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-language');
                if (removeBtn) {
                    const langTag = removeBtn.closest('.language-tag');
                    if (langTag) {
                        langTag.remove();
                    }
                }
            });
        }

        // Form submission handling
        if (appConfigForm) {
            appConfigForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await handleConfigSubmit(appConfigForm, '/configure/app/save');
            });
        }
    });
</script>
