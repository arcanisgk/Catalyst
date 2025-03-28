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

namespace Catalyst\Framework\Core\Mail;

use Catalyst\Framework\Core\Exceptions\MailException;
use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**************************************************************************************
 * Mail manager for sending emails
 *
 * Handles email configuration, creation, and sending with support for DKIM signing.
 *
 * @package Catalyst\Framework\Core\Mail
 */
class MailManager
{
    use SingletonTrait;

    /**
     * @var array Email configuration
     */
    protected array $config = [
        'host' => '',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'auth' => true,
        'debug' => 0,
        'from_address' => '',
        'from_name' => '',
        'reply_to' => '',
        'default_address' => '',
        'support_address' => '',
        'test_mode' => false,
        'test_prefix' => '[TEST]',
        'verify_peer' => true,
        'verify_peer_name' => true,
        'allow_self_signed' => false,
        'dkim_domain' => '',
        'dkim_selector' => '',
        'dkim_private_key' => '',
        'dkim_passphrase' => '',
        'dkim_identity' => '',
        'dkim_copy_header_fields' => false
    ];

    /**
     * @var string Current mail profile
     */
    protected string $currentProfile = 'mail1';

    /**
     * @var bool Whether the mail system has been initialized
     */
    protected bool $initialized = false;

    /**
     * Initialize the mail manager with provided configuration
     *
     * @param array $config Configuration options
     * @param string|null $profile Mail profile to use
     * @return self For method chaining
     * @throws MailException If configuration is invalid
     * @throws Exception
     */
    public function init(array $config = [], ?string $profile = null): self
    {
        if ($this->initialized) {
            return $this;
        }

        // If profile is provided, set it as current
        if ($profile !== null) {
            $this->currentProfile = $profile;
        }

        // If no explicit config provided, load from configuration system
        if (empty($config) && defined('APP_CONFIGURATION')) {
            $mailConfig = APP_CONFIGURATION->get("mail.$this->currentProfile", []);

            // Map mail.json keys to MailManager config keys
            if (!empty($mailConfig)) {
                $config = [
                    'host' => $mailConfig['mail_host'] ?? $this->config['host'],
                    'port' => $mailConfig['mail_port'] ?? $this->config['port'],
                    'username' => $mailConfig['mail_user'] ?? $this->config['username'],
                    'password' => $mailConfig['mail_password'] ?? $this->config['password'],
                    'encryption' => $mailConfig['mail_protocol'] ?? $this->config['encryption'],
                    'auth' => $mailConfig['mail_authentication'] ?? $this->config['auth'],
                    'debug' => $mailConfig['mail_debug'] ?? $this->config['debug'],
                    'from_address' => $mailConfig['mail_user'] ?? $this->config['from_address'],
                    'from_name' => $mailConfig['mail_name'] ?? $this->config['from_name'],
                    'reply_to' => $mailConfig['mail_support'] ?? $this->config['reply_to'],
                    'default_address' => $mailConfig['mail_default'] ?? $this->config['default_address'],
                    'support_address' => $mailConfig['mail_support'] ?? $this->config['support_address'],
                    'test_mode' => $mailConfig['mail_test'] ?? $this->config['test_mode'],
                    'test_prefix' => $mailConfig['mail_test_smg'] ?? $this->config['test_prefix'],
                    'verify_peer' => $mailConfig['mail_verify'] ?? $this->config['verify_peer'],
                    'verify_peer_name' => $mailConfig['mail_verify_peer_name'] ?? $this->config['verify_peer_name'],
                    'allow_self_signed' => $mailConfig['mail_self_signed'] ?? $this->config['allow_self_signed'],
                    //'dkim_domain' => $this->extractDomain($mailConfig['mail_user'] ?? ''),
                    'dkim_selector' => $mailConfig['mail_dkim_sign'] ?? $this->config['dkim_selector'],
                    'dkim_passphrase' => $mailConfig['mail_dkim_passphrase'] ?? $this->config['dkim_passphrase'],
                    'dkim_identity' => $mailConfig['mail_user'] ?? $this->config['dkim_identity'],
                    'dkim_copy_header_fields' => $mailConfig['mail_dkim_copy_header_fields'] ?? $this->config['dkim_copy_header_fields']
                ];

                // Extract domain from email or use custom domain if configured
                if (!empty($mailConfig['mail_dkim_domain_source']) && $mailConfig['mail_dkim_domain_source'] === 'custom' && !empty($mailConfig['mail_dkim_custom_domain'])) {
                    $config['dkim_domain'] = $mailConfig['mail_dkim_custom_domain'];
                } else {
                    $config['dkim_domain'] = $this->extractDomain($mailConfig['mail_user'] ?? '');
                }

                // Determine private key path for DKIM if selector is provided
                if (!empty($config['dkim_selector']) && !empty($config['dkim_domain'])) {
                    $connectionId = substr($this->currentProfile, 4); // Extract numeric ID from "mail1"
                    $config['dkim_private_key'] = implode(DS, [
                        PD,
                        'bootstrap',
                        'dkim',
                        $config['dkim_domain'],
                        $connectionId,
                        $config['dkim_selector'] . '_private.key'
                    ]);
                }
            }
        }

        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        // Validate minimum required configuration
        if (empty($this->config['host']) || empty($this->config['username'])) {
            throw MailException::configurationError('Mail host and username must be provided');
        }

        $this->initialized = true;

        // Log mail initialization if in development mode
        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && class_exists('\Catalyst\Helpers\Log\Logger')) {
            Logger::getInstance()->debug('Mail system initialized', [
                'host' => $this->config['host'],
                'from' => $this->config['from_address'],
                'test_mode' => $this->config['test_mode']
            ]);
        }

        return $this;
    }

    /**
     * Create a new message instance
     *
     * @return MailMessage New message instance
     * @throws MailException If mail manager is not initialized
     */
    public function createMessage(): MailMessage
    {
        $this->ensureInitialized();
        return new MailMessage($this);
    }

    /**
     * Send a prepared email message
     *
     * @param MailMessage $message MailMessage to send
     * @return bool True if the message was sent successfully
     * @throws MailException If sending fails
     * @throws Exception
     */
    public function send(MailMessage $message): bool
    {
        $this->ensureInitialized();

        $mailer = $this->createMailer();
        $this->configureMailer($mailer);

        try {
            // Set message properties
            $mailer->Subject = $this->prependTestPrefix($message->getSubject());

            // Set recipients
            foreach ($message->getTo() as $recipient) {
                $mailer->addAddress($recipient['email'], $recipient['name'] ?? '');
            }

            foreach ($message->getCc() as $recipient) {
                $mailer->addCC($recipient['email'], $recipient['name'] ?? '');
            }

            foreach ($message->getBcc() as $recipient) {
                $mailer->addBCC($recipient['email'], $recipient['name'] ?? '');
            }

            // Set reply-to
            $replyTo = $message->getReplyTo();
            if (!empty($replyTo)) {
                $mailer->addReplyTo($replyTo['email'], $replyTo['name'] ?? '');
            } elseif (!empty($this->config['reply_to'])) {
                // Use configured reply-to if message doesn't have one
                $mailer->addReplyTo($this->config['reply_to']);
            }

            // Set body
            if ($message->isHtml()) {
                $mailer->isHTML();
                $mailer->Body = $message->getHtmlBody();
                if ($message->getTextBody()) {
                    $mailer->AltBody = $message->getTextBody();
                }
            } else {
                $mailer->isHTML(false);
                $mailer->Body = $message->getTextBody();
            }

            // Add attachments
            foreach ($message->getAttachments() as $attachment) {
                if ($attachment['inline']) {
                    $mailer->addEmbeddedImage(
                        $attachment['path'],
                        $attachment['cid'],
                        $attachment['name'],
                        $attachment['encoding'],
                        $attachment['type']
                    );
                } else {
                    $mailer->addAttachment(
                        $attachment['path'],
                        $attachment['name'],
                        $attachment['encoding'],
                        $attachment['type']
                    );
                }
            }

            // Add custom headers
            foreach ($message->getHeaders() as $name => $value) {
                $mailer->addCustomHeader($name, $value);
            }

            // Configure DKIM if enabled
            $this->configureDkim($mailer);

            // Send the email
            if (!$mailer->send()) {
                throw MailException::sendingError($mailer->ErrorInfo);
            }

            // Log successful send in development mode
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                Logger::getInstance()->info('Email sent successfully', [
                    'subject' => $mailer->Subject,
                    'to' => array_column($message->getTo(), 'email')
                ]);
            }

            return true;
        } catch (PHPMailerException $e) {
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                Logger::getInstance()->error('Failed to send email', [
                    'error' => $e->getMessage(),
                    'subject' => $mailer->Subject ?? 'unknown'
                ]);
            }
            throw MailException::sendingError($e->getMessage());
        }
    }

    /**
     * Create and configure a new PHPMailer instance
     *
     * @return PHPMailer Configured mailer instance
     */
    protected function createMailer(): PHPMailer
    {
        return new PHPMailer(true);
    }

    /**
     * Configure the PHPMailer instance with current settings
     *
     * @param PHPMailer $mailer Mailer to configure
     * @return void
     * @throws PHPMailerException
     */
    protected function configureMailer(PHPMailer $mailer): void
    {
        // Set debug level
        $mailer->SMTPDebug = $this->config['debug'];

        // Configure SMTP
        $mailer->isSMTP();
        $mailer->Host = $this->config['host'];
        $mailer->Port = $this->config['port'];

        // Configure authentication
        if ($this->config['auth']) {
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->config['username'];
            $mailer->Password = $this->config['password'];
        } else {
            $mailer->SMTPAuth = false;
        }

        // Configure encryption
        $mailer->SMTPSecure = $this->config['encryption'];

        // Configure SSL options
        $mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => $this->config['verify_peer'],
                'verify_peer_name' => $this->config['verify_peer_name'],
                'allow_self_signed' => $this->config['allow_self_signed']
            ]
        ];

        // Set sender
        $mailer->setFrom(
            $this->config['from_address'],
            $this->config['from_name']
        );

        // Set default charset
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
    }

    /**
     * Configure DKIM signing for the mailer
     *
     * @param PHPMailer $mailer Mailer to configure
     * @return void
     * @throws Exception
     */
    protected function configureDkim(PHPMailer $mailer): void
    {
        // Skip if DKIM is not configured
        if (empty($this->config['dkim_selector']) || empty($this->config['dkim_private_key'])) {
            return;
        }

        // Check if private key exists
        if (!file_exists($this->config['dkim_private_key'])) {
            // Log warning but don't throw exception as email can still be sent without DKIM
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                Logger::getInstance()->warning('DKIM private key not found', [
                    'path' => $this->config['dkim_private_key']
                ]);
            }
            return;
        }

        try {
            $privateKey = file_get_contents($this->config['dkim_private_key']);

            // Set DKIM signing options
            $mailer->DKIM_domain = $this->config['dkim_domain'];
            $mailer->DKIM_private = $privateKey;
            $mailer->DKIM_selector = $this->config['dkim_selector'];
            $mailer->DKIM_passphrase = $this->config['dkim_passphrase'];
            $mailer->DKIM_identity = $this->config['dkim_identity'] ?: $this->config['from_address'];
            $mailer->DKIM_copyHeaderFields = $this->config['dkim_copy_header_fields'];

            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                Logger::getInstance()->debug('DKIM signing enabled', [
                    'domain' => $this->config['dkim_domain'],
                    'selector' => $this->config['dkim_selector']
                ]);
            }
        } catch (Exception $e) {
            // Log error but don't throw exception
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                Logger::getInstance()->error('DKIM configuration error', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Prepend test prefix to subject if in test mode
     *
     * @param string $subject Email subject
     * @return string Subject with test prefix if applicable
     */
    protected function prependTestPrefix(string $subject): string
    {
        if ($this->config['test_mode'] && !empty($this->config['test_prefix'])) {
            return $this->config['test_prefix'] . ' ' . $subject;
        }

        return $subject;
    }

    /**
     * Extract domain from email address
     *
     * @param string $email Email address
     * @return string Domain or empty string if not found
     */
    protected function extractDomain(string $email): string
    {
        if (preg_match('/@([^@]+)$/', $email, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Set the current mail profile
     *
     * @param string $profile Profile name
     * @return self For method chaining
     */
    public function profile(string $profile): self
    {
        $this->currentProfile = $profile;
        $this->initialized = false;
        return $this;
    }

    /**
     * Get the mail configuration
     *
     * @return array Mail configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Ensure the mail manager is initialized
     *
     * @return void
     * @throws MailException If not initialized
     */
    protected function ensureInitialized(): void
    {
        if (!$this->initialized) {
            $this->init();

            if (!$this->initialized) {
                throw MailException::configurationError('Mail manager not initialized. Call init() first.');
            }
        }
    }
}