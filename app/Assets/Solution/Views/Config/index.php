<div class="row mb-2">
    <div class="col-12">
        <h1 class="text-center">Catalyst Framework Configuration</h1>
        <p class="text-center text-muted">Configure your application settings, connections, and environment</p>
    </div>
</div>

<div class="row env-selector">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Current Environment: <span class="badge bg-primary text-white"><?= $currentEnvironment ?></span></h4>
                <form id="environment-form">
                    <div class="mb-3">
                        <label for="environment" class="form-label">Switch Environment</label>
                        <select class="form-select" id="environment" name="environment">
                            <?php foreach ($environments as $env): ?>
                                <option value="<?= $env ?>" <?= $env === $currentEnvironment ? 'selected' : '' ?>><?= ucfirst($env) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Environment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($sections as $section): ?>
        <div class="col-md-4 mb-2">

            <a href="/configure/<?= $section ?>" class="card config-card text-decoration-none">
                <div class="card-body text-center">
                    <div class="card-icon">
                        <?php switch ($section):
                            case 'app': ?>
                                <i class="bi bi-gear-fill text-primary"></i>
                                <?php break; ?>
                            <?php case 'session': ?>
                                <i class="bi bi-shield-lock-fill text-success"></i>
                                <?php break; ?>
                            <?php case 'db': ?>
                                <i class="bi bi-database-fill text-info"></i>
                                <?php break; ?>
                            <?php case 'ftp': ?>
                                <i class="bi bi-hdd-network text-warning"></i>
                                <?php break; ?>
                            <?php case 'mail': ?>
                                <i class="bi bi-envelope-fill text-danger"></i>
                                <?php break; ?>
                            <?php case 'tools': ?>
                                <i class="bi bi-tools"></i>
                                <?php break; ?>
                            <?php default: ?>
                                <i class="bi bi-question-circle-fill"></i>
                            <?php endswitch; ?>
                    </div>
                    <h5 class="card-title"><?= ucfirst($section) ?> Configuration</h5>
                    <p class="card-text">
                        <?php switch ($section):
                            case 'app': ?>
                                Configure general application and company data
                                <?php break; ?>
                            <?php case 'session': ?>
                                Manage session parameters and login settings
                                <?php break; ?>
                            <?php case 'db': ?>
                                Set up database connection credentials
                                <?php break; ?>
                            <?php case 'ftp': ?>
                                Configure FTP connection settings
                                <?php break; ?>
                            <?php case 'mail': ?>
                                Manage email server configurations
                                <?php break; ?>
                            <?php case 'tools': ?>
                                Set up development tools and utilities
                                <?php break; ?>
                            <?php default: ?>
                                Configure system settings
                            <?php endswitch; ?>
                    </p>
                    <span class="btn btn-outline-primary">Configure</span>
                </div>
            </a>


        </div>
    <?php endforeach; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const environmentForm = document.getElementById('environment-form');

        environmentForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const formData = new FormData(environmentForm);
                const response = await fetch('/configure/change-environment', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing the environment.');
            }
        });
    });
</script>
