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
        <form id="session-config-form">
            <!-- Session Settings -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Session Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="session_name">Session Name</label>
                                <input type="text" class="form-control" id="session_name" name="session_name"
                                       value="<?= $configData['session']['session_name'] ?? 'catalyst-session' ?>" required>
                                <small class="form-text text-muted">The name of the session that is used as the cookie name.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3 mt-4">
                                <input type="checkbox" class="form-check-input" id="session_inactivity" name="session_inactivity"
                                    <?= isset($configData['session']['session_inactivity']) && $configData['session']['session_inactivity'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="session_inactivity">Enable Session Inactivity Timeout</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="session_life_time">Session Lifetime (seconds)</label>
                                <input type="number" class="form-control" id="session_life_time" name="session_life_time"
                                       value="<?= $configData['session']['session_life_time'] ?? 2592000 ?>" required>
                                <small class="form-text text-muted">Maximum lifetime of a session in seconds (default: 30 days)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="session_activity_expire">Activity Expiration (seconds)</label>
                                <input type="number" class="form-control" id="session_activity_expire" name="session_activity_expire"
                                       value="<?= $configData['session']['session_activity_expire'] ?? 172800 ?>" required>
                                <small class="form-text text-muted">Inactivity period before expiration (default: 2 days)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="session_secure" name="session_secure"
                                        <?= isset($configData['session']['session_secure']) && $configData['session']['session_secure'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="session_secure">Secure Cookie</label>
                                </div>
                                <small class="form-text text-muted">Only transmit cookies over HTTPS</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="session_http_only" name="session_http_only"
                                        <?= isset($configData['session']['session_http_only']) && $configData['session']['session_http_only'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="session_http_only">HTTP Only Cookie</label>
                                </div>
                                <small class="form-text text-muted">Prevent JavaScript access to cookies</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row mb-3">
                                <label class="col-sm-4 col-form-label" for="session_same_site">SameSite Policy</label>
                                <div class="col-sm-8">
                                    <select class="form-select" id="session_same_site" name="session_same_site">
                                        <option value="Strict" <?= ($configData['session']['session_same_site'] ?? 'Strict') === 'Strict' ? 'selected' : '' ?>>Strict</option>
                                        <option value="Lax" <?= ($configData['session']['session_same_site'] ?? '') === 'Lax' ? 'selected' : '' ?>>Lax</option>
                                        <option value="None" <?= ($configData['session']['session_same_site'] ?? '') === 'None' ? 'selected' : '' ?>>None</option>
                                    </select>
                                </div>
                                <small class="form-text text-muted">Controls when cookies are sent with cross-site requests</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Settings -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Registration Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="register_all" name="register_all"
                                        <?= isset($configData['register']['all']) && $configData['register']['all'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="register_all">Allow Public Registration</label>
                                </div>
                                <div class="form-text text-muted">Anyone can register an account</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="register_internal" name="register_internal"
                                        <?= isset($configData['register']['internal']) && $configData['register']['internal'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="register_internal">Allow Internal Registration</label>
                                </div>
                                <div class="form-text text-muted">Registration through internal channels</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="register_service" name="register_service"
                                        <?= isset($configData['register']['service']) && $configData['register']['service'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="register_service">Allow Service Registration</label>
                                </div>
                                <div class="form-text text-muted">Registration through third-party services</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Party Authentication -->
            <div class="ibox" id="third-party-auth-container">
                <div class="ibox-title">
                    <h5>Third-Party Authentication</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row g-3">
                        <?php
                        $services = [
                            'google' => 'Google',
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'github' => 'GitHub',
                            'twitter' => 'Twitter',
                            'disqus' => 'Disqus',
                            'foursquare' => 'Foursquare',
                            'linkedin' => 'LinkedIn',
                            'apple' => 'Apple',
                            'microsoft' => 'Microsoft',
                            'steam' => 'Steam',
                            'dropbox' => 'Dropbox',
                            'spotify' => 'Spotify',
                            'twitch' => 'Twitch',
                            'slack' => 'Slack',
                            'auth0' => 'Auth0'
                        ];

                        foreach ($services as $key => $name):
                            $serviceKey = "{$key}_sign_service";
                            $isConfigured = isset($configData['oauth_credentials'][$key]) && !empty($configData['oauth_credentials'][$key]);
                            $isChecked = isset($configData['service'][$serviceKey]) && $configData['service'][$serviceKey];
                            ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="service-item p-2 border rounded d-flex flex-column h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check mb-0 d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input service-checkbox me-2"
                                                   id="<?= $serviceKey ?>"
                                                   name="<?= $serviceKey ?>"
                                                   data-service="<?= $key ?>"
                                                <?= $isChecked ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="<?= $serviceKey ?>">
                                                <?= $name ?>
                                            </label>
                                        </div>
                                        <span class="ms-1 text-warning" title="Info: Click and check the checkbox to enable or disable this service.">
                                            <i class="bi bi-info-circle"></i>
                                        </span>
                                        <?php if ($isConfigured): ?>
                                            <span class="ms-1 text-success" title="Credentials configured">
                                                <i class="bi bi-check-circle"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-auto">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary w-100 configure-service-btn"
                                                style="display: <?= $isChecked ? 'block' : 'none' ?>;"
                                                data-service="<?= $key ?>"
                                                data-service-name="<?= $name ?>">
                                            Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
<!-- OAuth Credentials Modal -->
<div class="modal fade" id="oauth-credentials-modal" tabindex="-1" aria-labelledby="oauth-credentials-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="oauth-credentials-modal-label">Configure OAuth Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="oauth-credentials-form">
                    <input type="hidden" id="oauth-service-key" name="service_key" value="">

                    <div class="mb-3">
                        <label for="oauth-client-id" class="form-label">Client ID</label>
                        <input type="text" class="form-control" id="oauth-client-id" name="client_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="oauth-client-secret" class="form-label">Client Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="oauth-client-secret" name="oauth-client-secret" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="oauth-redirect-uri" class="form-label">Redirect URI</label>
                        <input type="text" class="form-control" id="oauth-redirect-uri" name="redirect_uri" required>
                        <div class="form-text">
                            Suggested: <code id="default-redirect-uri"></code>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="oauth-scopes" class="form-label">Scopes</label>
                        <input type="text" class="form-control" id="oauth-scopes" name="scopes" placeholder="e.g., email,profile">
                        <div class="form-text">Comma-separated list of permission scopes</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" id="clear-credentials-btn">Clear Credentials</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-credentials-btn">Save Credentials</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Form submission handler and third-party services visibility
        const sessionConfigForm = document.getElementById('session-config-form');
        const registerServiceCheckbox = document.getElementById('register_service');
        const thirdPartyAuthContainer = document.getElementById('third-party-auth-container');
        const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
        const configureButtons = document.querySelectorAll('.configure-service-btn');

        // Modal elements - using proper Bootstrap 5.3 initialization
        let credentialsModal;
        const modalElement = document.getElementById('oauth-credentials-modal');

        if (modalElement) {
            // Add event listeners to manage focus and inert attribute
            modalElement.addEventListener('hidden.bs.modal', function () {
                // Add inert attribute when modal is hidden
                this.setAttribute('inert', '');
            });

            modalElement.addEventListener('show.bs.modal', function () {
                // Remove inert attribute when modal is shown
                this.removeAttribute('inert');
            });

            // Initialize modal
            credentialsModal = new bootstrap.Modal(modalElement);

            // Set inert by default when page loads (if modal is closed)
            if (!modalElement.classList.contains('show')) {
                modalElement.setAttribute('inert', '');
            }
        }

        const credentialsForm = document.getElementById('oauth-credentials-form');
        const serviceKeyInput = document.getElementById('oauth-service-key');
        const modalTitle = document.getElementById('oauth-credentials-modal-label');
        const saveCredentialsBtn = document.getElementById('save-credentials-btn');
        const clearCredentialsBtn = document.getElementById('clear-credentials-btn');
        const defaultRedirectUri = document.getElementById('default-redirect-uri');

        // Function to toggle visibility of third-party services
        function toggleThirdPartyServices() {
            if (registerServiceCheckbox && registerServiceCheckbox.checked) {
                thirdPartyAuthContainer.style.display = 'block';
            } else if (registerServiceCheckbox) {
                thirdPartyAuthContainer.style.display = 'none';
                // Uncheck all service checkboxes
                serviceCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    // Hide configure buttons
                    const serviceKey = checkbox.getAttribute('data-service');
                    const configBtn = document.querySelector(`.configure-service-btn[data-service="${serviceKey}"]`);
                    if (configBtn) configBtn.style.display = 'none';
                });
            }
        }

        // Toggle configure buttons when service is checked/unchecked
        serviceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const serviceKey = this.getAttribute('data-service');
                const configBtn = document.querySelector(`.configure-service-btn[data-service="${serviceKey}"]`);

                if (configBtn) {
                    configBtn.style.display = this.checked ? 'block' : 'none';
                }
            });
        });

        // Handle configure button click
        configureButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const serviceKey = this.getAttribute('data-service');
                const serviceName = this.getAttribute('data-service-name');

                if (!modalElement || !credentialsModal) {
                    console.error('Modal elements not found');
                    return;
                }

                // Set modal title and service key
                if (modalTitle) modalTitle.textContent = `Configure ${serviceName} Authentication`;
                if (serviceKeyInput) serviceKeyInput.value = serviceKey;

                // Set default redirect URI
                if (defaultRedirectUri) {
                    const domain = window.location.origin;
                    defaultRedirectUri.textContent = `${domain}/auth/${serviceKey}/callback`;
                }

                // Load existing credentials if available
                loadCredentials(serviceKey);

                // Show the modal
                credentialsModal.show();
            });
        });

        // Load credentials for a service
        function loadCredentials(serviceKey) {
            // Reset form
            if (credentialsForm) credentialsForm.reset();

            // In a real implementation, you would fetch this from the server
            fetch(`/configure/oauth/credentials/${serviceKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.credentials) {
                        const clientIdField = document.getElementById('oauth-client-id');
                        const clientSecretField = document.getElementById('oauth-client-secret');
                        const redirectUriField = document.getElementById('oauth-redirect-uri');
                        const scopesField = document.getElementById('oauth-scopes');

                        if (clientIdField) clientIdField.value = data.credentials.client_id || '';
                        if (clientSecretField) clientSecretField.value = data.credentials.client_secret || '';
                        if (redirectUriField) redirectUriField.value = data.credentials.redirect_uri || '';
                        if (scopesField) scopesField.value = data.credentials.scopes || '';

                        // Additional service-specific fields could be populated here
                    }
                })
                .catch(error => {
                    console.error('Error loading credentials:', error);
                });
        }

        // Save credentials
        if (saveCredentialsBtn) {
            saveCredentialsBtn.addEventListener('click', function () {
                if (!credentialsForm) return;

                const serviceKey = serviceKeyInput.value;
                handleOAuthCredentials('save', credentialsForm, serviceKey, credentialsModal);
            });
        }

        // Clear credentials
        if (clearCredentialsBtn) {
            clearCredentialsBtn.addEventListener('click', function () {
                if (!serviceKeyInput) return;

                const serviceKey = serviceKeyInput.value;

                if (confirm('Are you sure you want to clear all credentials for this service?')) {
                    handleOAuthCredentials('clear', credentialsForm, serviceKey, credentialsModal);
                }
            });
        }

        // Initial state setup
        if (registerServiceCheckbox) {
            toggleThirdPartyServices();

            // Listen for changes to the register_service checkbox
            registerServiceCheckbox.addEventListener('change', toggleThirdPartyServices);
        }

        if (sessionConfigForm) {
            sessionConfigForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await handleConfigSubmit(sessionConfigForm, '/configure/session/save');
            });
        }
    });

</script>
