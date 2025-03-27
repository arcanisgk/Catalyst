<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

namespace Catalyst\Solution\Controllers;

use Catalyst\Framework\Core\Database\ConnectionTester;
use Catalyst\Framework\Core\Response\JsonResponse;
use Catalyst\Framework\Core\Response\ViewResponse;
use Catalyst\Assets\Framework\Core\Http\Request;
use Exception;

/**
 * Configuration Controller
 *
 * Handles system configuration management including database, mail, FTP and tools settings
 *
 * @package Catalyst\Solution\Controllers
 */
class ConfigController extends Controller
{
    /**
     * @var string Path to configuration files
     */
    private string $configPath;

    /**
     * @var array Available environment names
     */
    private array $environments = ['development', 'production', 'quality', 'testing'];

    /**
     * @var string Current active environment
     */
    private string $currentEnvironment = 'development';

    /**
     * @var array Available configuration sections
     */
    private array $sections = ['app', 'session', 'db', 'ftp', 'mail', 'tools'];


    /**
     * Construct of Configuration Controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->configPath = implode(DS, [PD, 'bootstrap', 'config']);
    }

    /**
     * Display the main configuration page
     *
     * @param Request $request The current request
     * @return ViewResponse
     * @throws Exception
     */
    public function index(Request $request): ViewResponse
    {
        $this->detectCurrentEnvironment();

        // Check which sections have custom configurations
        $customConfigs = [];
        foreach ($this->sections as $section) {
            $customConfigs[$section] = $this->hasCustomConfig($section);
        }

        // Log access to configuration panel
        $this->logInfo('Configuration panel accessed', [
            'ip' => $request->getClientIp ?? 'unknown',
            'environment' => $this->currentEnvironment
        ]);

        return $this->viewWithLayout('Config.index', [
            'environments' => $this->environments,
            'currentEnvironment' => $this->currentEnvironment,
            'sections' => $this->sections,
            'customConfigs' => $customConfigs
        ], 'config');
    }

    /**
     * Check if a configuration section has custom settings
     *
     * @param string $section Configuration section to check
     * @return bool True if custom settings exist, false if using defaults
     */
    private function hasCustomConfig(string $section): bool
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, $section . '.json']);
        return file_exists($filePath);
    }

    /**
     * Show a specific configuration section
     *
     * @param Request $request The current request
     * @param string $section Configuration section to display
     * @return ViewResponse
     * @throws Exception
     */
    public function showSection(Request $request, string $section): ViewResponse
    {
        if (!in_array($section, $this->sections)) {
            // Log invalid section access attempt
            $this->logWarning('Invalid configuration section access attempt', [
                'section' => $section,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            return $this->viewWithLayout('errors.404', [], 'default', 404);
        }

        $this->detectCurrentEnvironment();
        $configData = $this->loadConfigFile($section);

        // If this is the session section, also load OAuth credentials
        if ($section === 'session') {
            $configData['oauth_credentials'] = $this->loadAllOAuthCredentials();
        }

        // Log configuration section access
        $this->logInfo('Configuration section accessed', [
            'section' => $section,
            'environment' => $this->currentEnvironment,
            'ip' => $request->getClientIp ?? 'unknown'
        ]);

        return $this->viewWithLayout("Config.Sections.$section", [
            'section' => $section,
            'configData' => $configData,
            'environments' => $this->environments,
            'currentEnvironment' => $this->currentEnvironment
        ], 'config');
    }

    /**
     * Save configuration for a specific section
     *
     * @param Request $request The current request
     * @param string $section Configuration section to save
     * @return JsonResponse
     * @throws Exception
     */
    public function saveConfig(Request $request, string $section): JsonResponse
    {
        // Determinar si es petición API
        $isApiRequest = $this->expectsJson();

        // Obtener datos según el formato de la petición
        $contentType = $request->getHeaders('Content-Type');
        if ($isApiRequest && $contentType && str_contains($contentType, 'application/json')) {
            // Obtener datos del cuerpo JSON
            $postData = json_decode($request->getContent(), true) ?? [];
        } else {
            // Obtener datos de POST tradicional
            $postData = $request->getAllPost();
        }

        if (!in_array($section, $this->sections)) {
            // Log invalid section save attempt
            $this->logWarning('Invalid configuration section save attempt', [
                'section' => $section,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            // Solo establecer flash message si NO es AJAX
            if (!$this->expectsJson()) {
                $this->flashError('Invalid configuration section');
            }

            return new JsonResponse(['success' => false, 'message' => 'Invalid section'], 400);
        }

        $this->detectCurrentEnvironment();
        //$postData = $request->getAllPost();

        // Process and validate data based on section
        $processedData = $this->processConfigData($section, $postData);

        // Save the configuration
        $saved = $this->saveConfigFile($section, $processedData);

        if ($saved) {
            // Log successful configuration save
            $this->logInfo('Configuration saved successfully', [
                'section' => $section,
                'environment' => $this->currentEnvironment,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            // Flash message solo para navegación tradicional
            if (!$this->expectsJson()) {
                $this->flashSuccess("Configuration for $section saved successfully");
            }

            // Solo establecer flash message si NO es AJAX
            //if (!$this->expectsJson()) {
            //    $this->flashSuccess("Configuration for $section saved successfully");
            //}

            return new JsonResponse([
                'success' => true,
                'message' => "Configuration for $section saved successfully",
                'redirect' => "/configure/$section"
            ]);
        } else {
            // Log configuration save failure
            $this->logError('Failed to save configuration', [
                'section' => $section,
                'environment' => $this->currentEnvironment,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            // Solo establecer flash message si NO es AJAX
            if (!$this->expectsJson()) {
                $this->flashError("Failed to save configuration for $section");
            }

            return new JsonResponse([
                'success' => false,
                'message' => "Failed to save configuration for $section"
            ], 500);
        }
    }


    /**
     * Test connection for database, mail or FTP servers
     *
     * @param Request $request The current request
     * @return JsonResponse
     * @throws Exception
     */
    public function testConnection(Request $request): JsonResponse
    {
        $type = $request->post('type', '');
        $connectionId = $request->post('connection_id', '');

        if (empty($type) || empty($connectionId)) {
            $this->logWarning('Missing parameters in connection test', [
                'type' => $type,
                'connectionId' => $connectionId,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            return new JsonResponse(['success' => false, 'message' => 'Missing parameters'], 400);
        }

        $result = $this->testConnectionByType($type, $connectionId, $request->getAllPost());

        // Log test connection attempt
        $logLevel = $result['success'] ? 'info' : 'warning';
        // Primero determina el resultado
        $resultText = $result['success'] ? 'succeeded' : 'failed';

        // Luego usa la variable simple en la interpolación
        $this->{"log$logLevel"}("Connection test $resultText", [
            'type' => $type,
            'connectionId' => $connectionId,
            'result' => $result['message'],
            'ip' => $request->server('REMOTE_ADDR', 'unknown')
        ]);

        return new JsonResponse($result);
    }

    /**
     * Change the current environment
     *
     * @param Request $request The current request
     * @return JsonResponse
     * @throws Exception
     */
    public function changeEnvironment(Request $request): JsonResponse
    {
        $newEnvironment = $request->post('environment', '');

        if (!in_array($newEnvironment, $this->environments)) {
            $this->logWarning('Invalid environment change attempt', [
                'requestedEnvironment' => $newEnvironment,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            return new JsonResponse(['success' => false, 'message' => 'Invalid environment'], 400);
        }

        // Update the current environment
        $this->currentEnvironment = $newEnvironment;

        // Create environment directory if it doesn't exist
        $envDir = implode(DS, [$this->configPath, $newEnvironment]);

        if (!is_dir($envDir)) {
            mkdir($envDir, 0755, true);
        }

        $this->logInfo('Environment changed successfully', [
            'environment' => $newEnvironment,
            'ip' => $request->getClientIp ?? 'unknown'
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => "Environment changed to $newEnvironment",
            'environment' => $newEnvironment
        ]);
    }

    /**
     * Get OAuth credentials for a specific service
     *
     * @param Request $request The current request
     * @param string $service Service identifier
     * @return JsonResponse
     * @throws Exception
     */
    public function getOAuthCredentials(Request $request, string $service): JsonResponse
    {
        $this->detectCurrentEnvironment();

        // Load OAuth credentials
        $credentials = $this->loadOAuthCredentials($service);

        // For security, mask the client secret if it exists
        if (isset($credentials['client_secret']) && !empty($credentials['client_secret'])) {
            // If returning actual credentials in production, you should decrypt here
            // In development, we'll just return a placeholder
            if (!IS_DEVELOPMENT) {
                $credentials['client_secret'] = '********';
            }
        }

        return new JsonResponse([
            'success' => true,
            'service' => $service,
            'credentials' => $credentials
        ]);
    }

    /**
     * Save OAuth credentials for a service
     *
     * @param Request $request The current request
     * @return JsonResponse
     * @throws Exception
     */
    public function saveOAuthCredentials(Request $request): JsonResponse
    {
        $serviceKey = $request->post('service_key', '');
        if (empty($serviceKey)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Service key is required'
            ], 400);
        }

        // Get credentials from request
        $credentials = [
            'client_id' => $request->post('client_id', ''),
            'client_secret' => $request->post('client_secret', ''),
            'redirect_uri' => $request->post('redirect_uri', ''),
            'scopes' => $request->post('scopes', '')
        ];

        // Add any service-specific fields
        foreach ($request->getAllPost() as $key => $value) {
            if (str_starts_with($key, 'service_specific_')) {
                $credentials[substr($key, 16)] = $value;
            }
        }

        // Save the credentials
        $saved = $this->storeOAuthCredentials($serviceKey, $credentials);

        if ($saved) {
            $this->logInfo('OAuth credentials saved', [
                'service' => $serviceKey,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            // Solo establecer flash message si NO es AJAX
            if (!$this->expectsJson()) {
                $this->flashSuccess("Credentials for $serviceKey saved successfully");
            }

            return new JsonResponse([
                'success' => true,
                'message' => "Credentials for $serviceKey saved successfully"
            ]);
        } else {
            $this->logError('Failed to save OAuth credentials', [
                'service' => $serviceKey,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            // Solo establecer flash message si NO es AJAX
            if (!$this->expectsJson()) {
                $this->flashError("Failed to save credentials for $serviceKey");
            }

            return new JsonResponse([
                'success' => false,
                'message' => "Failed to save credentials for $serviceKey"
            ], 500);
        }
    }

    /**
     * Clear OAuth credentials for a service
     *
     * @param Request $request The current request
     * @return JsonResponse
     * @throws Exception
     */
    public function clearOAuthCredentials(Request $request): JsonResponse
    {
        $serviceKey = $request->post('service_key', '');
        if (empty($serviceKey)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Service key is required'
            ], 400);
        }

        // Clear the credentials
        $cleared = $this->removeOAuthCredentials($serviceKey);

        if ($cleared) {
            $this->logInfo('OAuth credentials cleared', [
                'service' => $serviceKey,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => "Credentials for $serviceKey cleared successfully"
            ]);
        } else {
            $this->logError('Failed to clear OAuth credentials', [
                'service' => $serviceKey,
                'ip' => $request->getClientIp ?? 'unknown'
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => "Failed to clear credentials for $serviceKey"
            ], 500);
        }
    }

    /**
     * Detect current environment from config or set default
     *
     * @return void
     */
    private function detectCurrentEnvironment(): void
    {
        // Get environment from APP_ENV variable or default to 'development'
        $this->currentEnvironment = getenv('APP_ENV') ?: 'development';

        // Ensure environment is valid (only 'development' or 'production' allowed)
        if (!in_array($this->currentEnvironment, ['development', 'production'])) {
            $this->currentEnvironment = 'development';
        }

        // Ensure environment directory exists
        $envDir = implode(DS, [$this->configPath, $this->currentEnvironment]);

        if (!is_dir($envDir)) {
            mkdir($envDir, 0755, true);
        }
    }

    /**
     * Load a configuration file
     *
     * @param string $section Configuration section to load
     * @return array Configuration data
     */
    private function loadConfigFile(string $section): array
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, $section . '.json']);

        // If the file doesn't exist in current environment, check backup
        if (!file_exists($filePath)) {
            $filePath = implode(DS, [$this->configPath, 'backup', $section . '.json']);
        }

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Save a configuration file
     *
     * @param string $section Configuration section to save
     * @param array $data Configuration data to save
     * @return bool Success status
     */
    private function saveConfigFile(string $section, array $data): bool
    {
        $dirPath = implode(DS, [$this->configPath, $this->currentEnvironment]);

        // Ensure the directory exists
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $filePath = implode(DS, [$dirPath, $section . '.json']);

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        return file_put_contents($filePath, $jsonData) !== false;
    }

    /**
     * Load all OAuth credentials
     *
     * @return array All OAuth credentials
     */
    private function loadAllOAuthCredentials(): array
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, 'oauth_credentials.json']);

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $decoded = json_decode($content, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Load OAuth credentials from file
     *
     * @param string $service Service identifier
     * @return array Credentials
     */
    private function loadOAuthCredentials(string $service): array
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, 'oauth_credentials.json']);

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $decoded = json_decode($content, true);

            if (is_array($decoded) && isset($decoded[$service])) {
                return $decoded[$service];
            }
        }

        return [];
    }

    /**
     * Save OAuth credentials to file
     *
     * @param string $service Service identifier
     * @param array $credentials Credentials to save
     * @return bool Success status
     */
    private function storeOAuthCredentials(string $service, array $credentials): bool
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, 'oauth_credentials.json']);

        // Create or load existing credentials
        $allCredentials = [];
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $allCredentials = $decoded;
            }
        }

        // Update credentials for this service
        $allCredentials[$service] = $credentials;

        // Save the file
        $jsonData = json_encode($allCredentials, JSON_PRETTY_PRINT);

        // Ensure the directory exists
        $dirPath = implode(DS, [$this->configPath, $this->currentEnvironment]);
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        return file_put_contents($filePath, $jsonData) !== false;
    }

    /**
     * Remove OAuth credentials for a service
     *
     * @param string $service Service identifier
     * @return bool Success status
     */
    private function removeOAuthCredentials(string $service): bool
    {
        $filePath = implode(DS, [$this->configPath, $this->currentEnvironment, 'oauth_credentials.json']);

        // If file doesn't exist, nothing to remove
        if (!file_exists($filePath)) {
            return true;
        }

        // Load existing credentials
        $content = file_get_contents($filePath);
        $allCredentials = json_decode($content, true);

        // If not an array or service doesn't exist, nothing to remove
        if (!is_array($allCredentials) || !isset($allCredentials[$service])) {
            return true;
        }

        // Remove the service credentials
        unset($allCredentials[$service]);

        // Save the file
        $jsonData = json_encode($allCredentials, JSON_PRETTY_PRINT);
        return file_put_contents($filePath, $jsonData) !== false;
    }

    /**
     * Process and validate configuration data based on section
     *
     * @param string $section Configuration section
     * @param array $data Form data
     * @return array Processed configuration data
     */
    private function processConfigData(string $section, array $data): array
    {
        return match ($section) {
            'app' => $this->processAppConfig($data),
            'session' => $this->processSessionConfig($data),
            'db' => $this->processDatabaseConfig($data),
            'ftp' => $this->processFtpConfig($data),
            'mail' => $this->processMailConfig($data),
            'tools' => $this->processToolsConfig($data),
            default => $data,
        };
    }

    /**
     * Test connection based on service type
     *
     * @param string $type Connection type (db, mail, ftp)
     * @param string $connectionId Connection identifier
     * @param array $data Connection parameters
     * @return array Result with success status and message
     */
    private function testConnectionByType(string $type, string $connectionId, array $data): array
    {
        return match ($type) {
            'db' => $this->testDatabaseConnection($connectionId, $data),
            'mail' => $this->testMailConnection($connectionId, $data),
            'ftp' => $this->testFtpConnection($connectionId, $data),
            default => ['success' => false, 'message' => 'Unsupported connection type'],
        };
    }

    /**
     * Process application configuration data
     *
     * @param array $data Form data
     * @return array Processed app configuration
     */
    private function processAppConfig(array $data): array
    {
        return [
            'company' => [
                'company_name' => $data['company_name'] ?? '',
                'company_owner' => $data['company_owner'] ?? '',
                'company_department' => $data['company_department'] ?? ''
            ],
            'project' => [
                'project_name' => $data['project_name'] ?? '',
                'project_config' => isset($data['project_config']) && $data['project_config'] === 'on',
                'project_copyright' => $data['project_copyright'] ?? '',
                'eula' => $data['eula'] ?? ''
            ],
            'host' => [
                'domain' => $data['domain'] ?? 'localhost',
                'lang' => $data['lang'] ?? 'en',
                's-lang' => $this->processLanguages($data),
                'm-lang' => isset($data['m_lang']) && $data['m_lang'] === 'on',
                'protocol' => $data['protocol'] ?? 'http',
                'entry' => $data['entry'] ?? '',
                'license' => $data['license'] ?? '',
                'free' => isset($data['free']) && $data['free'] === 'on',
                'humanitarian' => isset($data['humanitarian']) && $data['humanitarian'] === 'on'
            ]
        ];
    }

    /**
     * Process languages from form data
     *
     * @param array $data Form data
     * @return array Processed language settings
     */
    private function processLanguages(array $data): array
    {
        $languages = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'lang_')) {
                $langCode = substr($key, 5);
                $languages[$langCode] = $value;
            }
        }

        return $languages ?: ['en' => 'English'];
    }

    /**
     * Process session configuration data
     *
     * @param array $data Form data
     * @return array Processed session configuration
     */
    private function processSessionConfig(array $data): array
    {
        return [
            'session' => [
                'session_name' => $data['session_name'] ?? 'catalyst-session',
                'session_inactivity' => isset($data['session_inactivity']) && $data['session_inactivity'] === 'on',
                'session_life_time' => (int)($data['session_life_time'] ?? 2592000),
                'session_activity_expire' => (int)($data['session_activity_expire'] ?? 172800),
                'session_secure' => isset($data['session_secure']) && $data['session_secure'] === 'on',
                'session_http_only' => isset($data['session_http_only']) && $data['session_http_only'] === 'on',
                'session_same_site' => $data['session_same_site'] ?? 'Strict'
            ],
            'register' => [
                'all' => isset($data['register_all']) && $data['register_all'] === 'on',
                'internal' => isset($data['register_internal']) && $data['register_internal'] === 'on',
                'service' => isset($data['register_service']) && $data['register_service'] === 'on'
            ],
            'service' => $this->processServiceConfig($data),
            // Add reference to oauth credentials
            'oauth_credentials_file' => 'oauth_credentials.json'
        ];
    }

    /**
     * Process service configuration for session
     *
     * @param array $data Form data
     * @return array Processed service configuration
     */
    private function processServiceConfig(array $data): array
    {
        $services = [];
        $serviceKeys = [
            'google', 'facebook', 'instagram', 'github', 'twitter', 'disqus',
            'foursquare', 'linkedin', 'apple', 'microsoft', 'steam', 'dropbox',
            'spotify', 'twitch', 'slack', 'auth0'
        ];

        foreach ($serviceKeys as $service) {
            $key = "{$service}_sign_service";
            $services[$key] = isset($data[$key]) && $data[$key] === 'on';
        }

        return $services;
    }

    /**
     * Process database configuration data
     *
     * @param array $data Form data
     * @return array Processed database configuration
     */
    private function processDatabaseConfig(array $data): array
    {
        $dbConfig = [];

        // Process each database connection
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'db_name_')) {
                $dbId = substr($key, 8);
                $dbConfig["db$dbId"] = [
                    'db_name' => $value,
                    'db_host' => $data["db_host_$dbId"] ?? '',
                    'db_port' => (int)($data["db_port_$dbId"] ?? 3306),
                    'db_user' => $data["db_user_$dbId"] ?? '',
                    'db_password' => $data["db_password_$dbId"] ?? '',
                    'db_password_re' => $data["db_password_re_$dbId"] ?? ''
                ];
            }
        }

        return $dbConfig;
    }

    /**
     * Process FTP configuration data
     *
     * @param array $data Form data
     * @return array Processed FTP configuration
     */
    private function processFtpConfig(array $data): array
    {
        $ftpConfig = [];

        // Process each FTP connection
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'ftp_name_')) {
                $ftpId = substr($key, 9);
                $ftpConfig["ftp$ftpId"] = [
                    'ftp_name' => $value,
                    'ftp_host' => $data["ftp_host_$ftpId"] ?? '',
                    'ftp_port' => (int)($data["ftp_port_$ftpId"] ?? 21),
                    'ftp_user' => $data["ftp_user_$ftpId"] ?? '',
                    'ftp_password' => $data["ftp_password_$ftpId"] ?? '',
                    'ftp_password_re' => $data["ftp_password_re_$ftpId"] ?? '',
                    'ftp_path' => $data["ftp_path_$ftpId"] ?? '/',
                    'ftp_passive_mode' => isset($data["ftp_passive_mode_$ftpId"]) && $data["ftp_passive_mode_$ftpId"] === 'on'
                ];
            }
        }

        return $ftpConfig;
    }

    /**
     * Process mail configuration data
     *
     * @param array $data Form data
     * @return array Processed mail configuration
     */
    private function processMailConfig(array $data): array
    {
        $mailConfig = [];

        // Process each mail connection
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'mail_name_')) {
                $mailId = substr($key, 10);
                $mailConfig["mail$mailId"] = [
                    'mail_name' => $value,
                    'mail_host' => $data["mail_host_$mailId"] ?? '',
                    'mail_port' => (int)($data["mail_port_$mailId"] ?? 587),
                    'mail_user' => $data["mail_user_$mailId"] ?? '',
                    'mail_support' => $data["mail_support_$mailId"] ?? '',
                    'mail_postmaster' => $data["mail_postmaster_$mailId"] ?? '',
                    'mail_password' => $data["mail_password_$mailId"] ?? '',
                    'mail_default' => $data["mail_default_$mailId"] ?? '',
                    'mail_test_smg' => $data["mail_test_smg_$mailId"] ?? '[TEST]',
                    'mail_protocol' => $data["mail_protocol_$mailId"] ?? 'tls',
                    'mail_authentication' => isset($data["mail_authentication_$mailId"]) && $data["mail_authentication_$mailId"] === 'on',
                    'mail_verify' => isset($data["mail_verify_$mailId"]) && $data["mail_verify_$mailId"] === 'on',
                    'mail_verify_peer_name' => isset($data["mail_verify_peer_name_$mailId"]) && $data["mail_verify_peer_name_$mailId"] === 'on',
                    'mail_self_signed' => isset($data["mail_self_signed_$mailId"]) && $data["mail_self_signed_$mailId"] === 'on',
                    'mail_dkim_sign' => $data["mail_dkim_sign_$mailId"] ?? '',
                    'mail_dkim_passphrase' => $data["mail_dkim_passphrase_$mailId"] ?? '',
                    'mail_dkim_copy_header_fields' => isset($data["mail_dkim_copy_header_fields_$mailId"]) && $data["mail_dkim_copy_header_fields_$mailId"] === 'on',
                    'mail_debug' => (int)($data["mail_debug_$mailId"] ?? 0),
                    'mail_test' => isset($data["mail_test_$mailId"]) && $data["mail_test_$mailId"] === 'on'
                ];
            }
        }

        return $mailConfig;
    }

    /**
     * Process tools configuration data
     *
     * @param array $data Form data
     * @return array Processed tools configuration
     */
    private function processToolsConfig(array $data): array
    {
        return [
            'app_setting' => isset($data['app_setting']) && $data['app_setting'] === 'on',
            'dev_tool' => isset($data['dev_tool']) && $data['dev_tool'] === 'on',
            'translate_tool' => $data['translate_tool'] ?? '',
            'security_tool' => isset($data['security_tool']) && $data['security_tool'] === 'on'
        ];
    }

    /**
     * Test database connection
     *
     * @param string $connectionId Connection identifier
     * @param array $data Connection parameters
     * @return array Result with success status and message
     */
    private function testDatabaseConnection(string $connectionId, array $data): array
    {
        try {
            $host = $data["db_host_$connectionId"] ?? '';
            $port = (int)($data["db_port_$connectionId"] ?? 3306);
            $dbname = $data["db_name_$connectionId"] ?? '';
            $user = $data["db_user_$connectionId"] ?? '';
            $password = $data["db_password_$connectionId"] ?? '';

            // Use the new ConnectionTester
            return ConnectionTester::test(
                $host,
                $port,
                $dbname,
                $user,
                $password
            );
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Database connection test failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Test mail connection
     *
     * @param string $connectionId Connection identifier
     * @param array $data Connection parameters
     * @return array Result with success status and message
     */
    private function testMailConnection(string $connectionId, array $data): array
    {
        // Note: In a real implementation, you would want to use a proper mail library
        // like PHPMailer or Swift Mailer to test the connection
        try {
            $host = $data["mail_host_$connectionId"] ?? '';
            $port = (int)($data["mail_port_$connectionId"] ?? 587);
            $user = $data["mail_user_$connectionId"] ?? '';
            $password = $data["mail_password_$connectionId"] ?? '';

            // Simple connection test - this is not a full email test
            $socket = @fsockopen($host, $port, $errno, $errstr, 5);

            if (!$socket) {
                return [
                    'success' => false,
                    'message' => "Mail server connection failed: $errstr ($errno)"
                ];
            }

            fclose($socket);

            return [
                'success' => true,
                'message' => "Successfully connected to mail server at '$host:$port'. For a full test, send a test email."
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Mail server connection failed: " . $e->getMessage()
            ];
        }
    }

    /**
     * Test FTP connection
     *
     * @param string $connectionId Connection identifier
     * @param array $data Connection parameters
     * @return array Result with success status and message
     */
    private function testFtpConnection(string $connectionId, array $data): array
    {
        try {
            $host = $data["ftp_host_$connectionId"] ?? '';
            $port = (int)($data["ftp_port_$connectionId"] ?? 21);
            $user = $data["ftp_user_$connectionId"] ?? '';
            $password = $data["ftp_password_$connectionId"] ?? '';
            $passiveMode = isset($data["ftp_passive_mode_$connectionId"]) && $data["ftp_passive_mode_$connectionId"] === 'on';

            // Connect to FTP server
            $conn = @ftp_connect($host, $port, 10);

            if (!$conn) {
                return [
                    'success' => false,
                    'message' => "Could not connect to FTP server at '$host:$port'"
                ];
            }

            // Login
            if (!@ftp_login($conn, $user, $password)) {
                ftp_close($conn);
                return [
                    'success' => false,
                    'message' => "FTP login failed for user '$user'"
                ];
            }

            // Set passive mode if requested
            if ($passiveMode) {
                ftp_pasv($conn, true);
            }

            ftp_close($conn);

            return [
                'success' => true,
                'message' => "Successfully connected to FTP server at '$host' with user '$user'"
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "FTP connection failed: " . $e->getMessage()
            ];
        }
    }
}
