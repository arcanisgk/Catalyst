<div class="row">
    <div class="col-lg-12">
        <form id="app-config-form">
            <!-- Company Information Section -->
            <div class="app-section">
                <h3 class="app-section-title">Company Information</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="<?= $configData['company']['company_name'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="company_owner" class="form-label">Company Owner</label>
                            <input type="text" class="form-control" id="company_owner" name="company_owner"
                                   value="<?= $configData['company']['company_owner'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="company_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="company_department" name="company_department"
                                   value="<?= $configData['company']['company_department'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Information Section -->
            <div class="app-section">
                <h3 class="app-section-title">Project Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project_name" name="project_name"
                                   value="<?= $configData['project']['project_name'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_copyright" class="form-label">Copyright</label>
                            <input type="text" class="form-control" id="project_copyright" name="project_copyright"
                                   value="<?= $configData['project']['project_copyright'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="eula" class="form-label">End User License Agreement</label>
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

            <!-- Host Settings Section -->
            <div class="app-section">
                <h3 class="app-section-title">Host Settings</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="domain" class="form-label">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain"
                                   value="<?= $configData['host']['domain'] ?? 'localhost' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="protocol" class="form-label">Protocol</label>
                            <select class="form-control" id="protocol" name="protocol">
                                <option value="http" <?= ($configData['host']['protocol'] ?? 'http') === 'http' ? 'selected' : '' ?>>HTTP</option>
                                <option value="https" <?= ($configData['host']['protocol'] ?? '') === 'https' ? 'selected' : '' ?>>HTTPS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="entry" class="form-label">Entry Point</label>
                            <select class="form-control" id="entry" name="entry">
                                <option value="">-- Select Entry Point --</option>
                                <option value="/usser-access" <?= ($configData['host']['entry'] ?? '') === '/usser-access' ? 'selected' : '' ?>>Login de usuario (Usser-Access)</option>
                                <option value="/landing" <?= ($configData['host']['entry'] ?? '') === '/landing' ? 'selected' : '' ?>>Landing Page (Landing page Home)</option>
                                <option value="/dashboard" <?= ($configData['host']['entry'] ?? '') === '/dashboard' ? 'selected' : '' ?>>Initial Dashboard View</option>
                                <option value="/store" <?= ($configData['host']['entry'] ?? '') === '/store' ? 'selected' : '' ?>>Initial View Store</option>
                                <option value="/blog" <?= ($configData['host']['entry'] ?? '') === '/blog' ? 'selected' : '' ?>>Initial Blog View</option>
                            </select>
                            <small class="form-text text-muted">This will be the default entry point when accessing your application</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="license" class="form-label">License Key</label>
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

            <!-- Language Settings Section -->
            <div class="app-section">
                <h3 class="app-section-title">Language Settings</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="lang" class="form-label">Default Language</label>
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
                        <div id="languages-container">
                            <?php
                            $languages = $configData['host']['s-lang'] ?? ['en' => 'English'];
                            foreach ($languages as $code => $name):
                                ?>
                                <div class="language-tag">
                                    <input type="hidden" name="lang_<?= $code ?>" value="<?= $name ?>">
                                    <?= $code ?>: <?= $name ?>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 remove-language">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="new-lang-code" placeholder="Code (e.g., fr)">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="new-lang-name" placeholder="Name (e.g., French)">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" id="add-language">Add Language</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <button type="submit" class="btn btn-outline-primary btn-lg px-5">Save Configuration</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
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
                            <i class="bi bi-x-circle"></i>
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
            appConfigForm.addEventListener('submit', (e) => {
                // Add form validation if needed
                const requiredFields = appConfigForm.querySelectorAll('input[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor complete todos los campos requeridos');
                }
            });
        }
    });
</script>