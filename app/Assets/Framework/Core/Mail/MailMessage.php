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

/**************************************************************************************
 * Email message class
 *
 * Represents an email message with a fluent interface for configuration.
 *
 * @package Catalyst\Framework\Core\Mail
 */
class MailMessage
{
    /**
     * @var MailManager Mail manager instance
     */
    protected MailManager $mailManager;

    /**
     * @var array MailMessage recipients (to)
     */
    protected array $to = [];

    /**
     * @var array MailMessage CC recipients
     */
    protected array $cc = [];

    /**
     * @var array MailMessage BCC recipients
     */
    protected array $bcc = [];

    /**
     * @var array|null Reply-to address
     */
    protected ?array $replyTo = null;

    /**
     * @var string MailMessage subject
     */
    protected string $subject = '';

    /**
     * @var string|null HTML content
     */
    protected ?string $htmlBody = null;

    /**
     * @var string|null Plain text content
     */
    protected ?string $textBody = null;

    /**
     * @var array Attachments
     */
    protected array $attachments = [];

    /**
     * @var array Custom headers
     */
    protected array $headers = [];

    /**
     * Constructor
     *
     * @param MailManager $mailManager Mail manager instance
     */
    public function __construct(MailManager $mailManager)
    {
        $this->mailManager = $mailManager;
    }

    /**
     * Set recipient(s)
     *
     * @param array|string $address Email address or array of [email => name]
     * @param string|null $name Recipient name (optional)
     * @return self For method chaining
     * @throws MailException If email address is invalid
     */
    public function to(array|string $address, ?string $name = null): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                if (is_numeric($email)) {
                    $this->addRecipient($this->to, $recipientName);
                } else {
                    $this->addRecipient($this->to, $email, $recipientName);
                }
            }
        } else {
            $this->addRecipient($this->to, $address, $name);
        }

        return $this;
    }

    /**
     * Set CC recipient(s)
     *
     * @param array|string $address Email address or array of [email => name]
     * @param string|null $name Recipient name (optional)
     * @return self For method chaining
     * @throws MailException If email address is invalid
     */
    public function cc(array|string $address, ?string $name = null): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                if (is_numeric($email)) {
                    $this->addRecipient($this->cc, $recipientName);
                } else {
                    $this->addRecipient($this->cc, $email, $recipientName);
                }
            }
        } else {
            $this->addRecipient($this->cc, $address, $name);
        }

        return $this;
    }

    /**
     * Set BCC recipient(s)
     *
     * @param array|string $address Email address or array of [email => name]
     * @param string|null $name Recipient name (optional)
     * @return self For method chaining
     * @throws MailException If email address is invalid
     */
    public function bcc(array|string $address, ?string $name = null): self
    {
        if (is_array($address)) {
            foreach ($address as $email => $recipientName) {
                if (is_numeric($email)) {
                    $this->addRecipient($this->bcc, $recipientName);
                } else {
                    $this->addRecipient($this->bcc, $email, $recipientName);
                }
            }
        } else {
            $this->addRecipient($this->bcc, $address, $name);
        }

        return $this;
    }

    /**
     * Set reply-to address
     *
     * @param string $address Email address
     * @param string|null $name Name (optional)
     * @return self For method chaining
     * @throws MailException If email address is invalid
     */
    public function replyTo(string $address, ?string $name = null): self
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw MailException::invalidAddress($address);
        }

        $this->replyTo = [
            'email' => $address,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Set message subject
     *
     * @param string $subject Subject text
     * @return self For method chaining
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set HTML body
     *
     * @param string $html HTML content
     * @return self For method chaining
     */
    public function html(string $html): self
    {
        $this->htmlBody = $html;
        return $this;
    }

    /**
     * Set plain text body
     *
     * @param string $text Plain text content
     * @return self For method chaining
     */
    public function text(string $text): self
    {
        $this->textBody = $text;
        return $this;
    }

    /**
     * Set body content (automatically determines type)
     *
     * @param string $content MailMessage content
     * @return self For method chaining
     */
    public function body(string $content): self
    {
        // Check if content appears to be HTML
        if (preg_match('/<[^>]+>/', $content)) {
            $this->htmlBody = $content;
            // Generate a simple text version if no text body is set
            if ($this->textBody === null) {
                $this->textBody = strip_tags($content);
            }
        } else {
            $this->textBody = $content;
        }

        return $this;
    }

    /**
     * Use a template for the message body
     *
     * @param string $template MailTemplate name or path
     * @param array $variables Variables to replace in the template
     * @param bool $isPath Whether the template is a file path
     * @return self For method chaining
     * @throws MailException If template cannot be loaded
     */
    public function template(string $template, array $variables = [], bool $isPath = false): self
    {
        $templateProcessor = new MailTemplate();

        if ($isPath) {
            $result = $templateProcessor->renderFromPath($template, $variables);
        } else {
            $result = $templateProcessor->render($template, $variables);
        }

        $this->htmlBody = $result['html'] ?? null;
        $this->textBody = $result['text'] ?? null;

        return $this;
    }

    /**
     * Add a file attachment
     *
     * @param string $path File path
     * @param string|null $name Display filename (optional)
     * @param string $mimeType MIME type (optional)
     * @return self For method chaining
     * @throws MailException If file cannot be attached
     */
    public function attach(string $path, ?string $name = null, string $mimeType = ''): self
    {
        if (!file_exists($path)) {
            throw MailException::attachmentError($path, 'File not found');
        }

        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?: basename($path),
            'type' => $mimeType,
            'encoding' => 'base64',
            'inline' => false,
            'cid' => ''
        ];

        return $this;
    }

    /**
     * Add an inline attachment (for embedding images in HTML)
     *
     * @param string $path File path
     * @param string $cid Content ID for referencing in HTML
     * @param string|null $name Display filename (optional)
     * @param string $mimeType MIME type (optional)
     * @return self For method chaining
     * @throws MailException If file cannot be attached
     */
    public function attachInline(string $path, string $cid, ?string $name = null, string $mimeType = ''): self
    {
        if (!file_exists($path)) {
            throw MailException::attachmentError($path, 'File not found');
        }

        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?: basename($path),
            'type' => $mimeType,
            'encoding' => 'base64',
            'inline' => true,
            'cid' => $cid
        ];

        return $this;
    }

    /**
     * Add a custom header
     *
     * @param string $name Header name
     * @param string $value Header value
     * @return self For method chaining
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Send the message
     *
     * @return bool True if message was sent successfully
     * @throws MailException If message cannot be sent
     */
    public function send(): bool
    {
        $this->validate();
        return $this->mailManager->send($this);
    }

    /**
     * Validate the message before sending
     *
     * @return void
     * @throws MailException If message is invalid
     */
    protected function validate(): void
    {
        // Must have at least one recipient
        if (empty($this->to)) {
            throw MailException::configurationError('No recipients specified');
        }

        // Must have a subject
        if (empty($this->subject)) {
            throw MailException::configurationError('No subject specified');
        }

        // Must have a body
        if (empty($this->htmlBody) && empty($this->textBody)) {
            throw MailException::configurationError('No message body specified');
        }
    }

    /**
     * Add a recipient to the specified list
     *
     * @param array &$list Recipient list to update
     * @param string $email Email address
     * @param string|null $name Recipient name
     * @return void
     * @throws MailException If email address is invalid
     */
    protected function addRecipient(array &$list, string $email, ?string $name = null): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw MailException::invalidAddress($email);
        }

        $list[] = [
            'email' => $email,
            'name' => $name
        ];
    }

    // Getters for MailManager

    /**
     * Get all recipients
     *
     * @return array Recipients
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * Get CC recipients
     *
     * @return array CC recipients
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * Get BCC recipients
     *
     * @return array BCC recipients
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * Get reply-to address
     *
     * @return array|null Reply-to address
     */
    public function getReplyTo(): ?array
    {
        return $this->replyTo;
    }

    /**
     * Get message subject
     *
     * @return string Subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Get HTML body
     *
     * @return string|null HTML body
     */
    public function getHtmlBody(): ?string
    {
        return $this->htmlBody;
    }

    /**
     * Get plain text body
     *
     * @return string|null Plain text body
     */
    public function getTextBody(): ?string
    {
        return $this->textBody;
    }

    /**
     * Get attachments
     *
     * @return array Attachments
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Get custom headers
     *
     * @return array Headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Check if the message has HTML content
     *
     * @return bool True if HTML content is set
     */
    public function isHtml(): bool
    {
        return $this->htmlBody !== null;
    }
}