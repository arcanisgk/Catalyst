<div class="row">
    <div class="col-lg-12">
        <form id="session-config-form">
            <!-- Session Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Session Settings</h3>
                </div>
                <div class="card-body">
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
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="session_secure" name="session_secure"
                                    <?= isset($configData['session']['session_secure']) && $configData['session']['session_secure'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="session_secure">Secure Cookie</label>
                                <small class="d-block text-muted">Only transmit cookies over HTTPS</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="session_http_only" name="session_http_only"
                                    <?= isset($configData['session']['session_http_only']) && $configData['session']['session_http_only'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="session_http_only">HTTP Only Cookie</label>
                                <small class="d-block text-muted">Prevent JavaScript access to cookies</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="session_same_site">SameSite Policy</label>
                                <select class="form-control" id="session_same_site" name="session_same_site">
                                    <option value="Strict" <?= ($configData['session']['session_same_site'] ?? 'Strict') === 'Strict' ? 'selected' : '' ?>>Strict</option>
                                    <option value="Lax" <?= ($configData['session']['session_same_site'] ?? '') === 'Lax' ? 'selected' : '' ?>>Lax</option>
                                    <option value="None" <?= ($configData['session']['session_same_site'] ?? '') === 'None' ? 'selected' : '' ?>>None</option>
                                </select>
                                <small class="form-text text-muted">Controls when cookies are sent with cross-site requests</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Registration Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="register_all" name="register_all"
                                    <?= isset($configData['register']['all']) && $configData['register']['all'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="register_all">Allow Public Registration</label>
                                <small class="d-block text-muted">Anyone can register an account</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="register_internal" name="register_internal"
                                    <?= isset($configData['register']['internal']) && $configData['register']['internal'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="register_internal">Allow Internal Registration</label>
                                <small class="d-block text-muted">Registration through internal channels</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="register_service" name="register_service"
                                    <?= isset($configData['register']['service']) && $configData['register']['service'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="register_service">Allow Service Registration</label>
                                <small class="d-block text-muted">Registration through third-party services</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Party Authentication -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Third-Party Authentication</h3>
                </div>
                <div class="card-body">
                    <div class="row">
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
                            ?>
                            <div class="col-md-3 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="<?= $serviceKey ?>" name="<?= $serviceKey ?>"
                                        <?= isset($configData['service'][$serviceKey]) && $configData['service'][$serviceKey] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?= $serviceKey ?>"><?= $name ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center  mb-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Save Configuration</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Form submission
        $('#session-config-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/configure/session/save',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Session configuration saved successfully');
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
