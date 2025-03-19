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

namespace App\Assets\Framework\Controllers;

use App\Assets\Framework\Core\Response\JsonResponse;
use App\Assets\Framework\Core\Response\RedirectResponse;
use App\Assets\Framework\Core\Response\ViewResponse;
use App\Assets\Helpers\Http\Request;
use Exception;

/**
 * Contact Controller
 *
 * Handles contact form display and submission processing
 *
 * @package App\Assets\Framework\Controllers
 */
class ContactController extends Controller
{
    /**
     * Display the contact form
     *
     * @param Request $request The current request
     * @return ViewResponse
     */
    public function index(Request $request): ViewResponse
    {
        // Check for success message from redirect
        $success = $request->get('success', false);

        // Get any validation errors (would come from a flash message system in a real app)
        $errors = [];

        // Log page access
        $this->logInfo('Contact page accessed');

        // Return view with data
        return $this->viewWithLayout('contact', [
            'title' => 'Contact Us',
            'activeMenu' => 'contact',    // For menu highlighting
            'pageTitle' => 'Contact',     // For page header
            'pageSubtitle' => 'Get in touch with us',  // For breadcrumbs
            'success' => $success,
            'errors' => $errors,
            'contactInfo' => [
                'email' => 'contact@example.com',
                'phone' => '+1 (123) 456-7890',
                'address' => '123 Framework St, Web City, Internet'
            ],
            // For re-populating the form after validation errors
            'old' => [
                'name' => $request->get('name', ''),
                'email' => $request->get('email', ''),
                'subject' => $request->get('subject', ''),
                'message' => $request->get('message', ''),
            ]
        ], 'default');
    }

    /**
     * Process contact form submission
     *
     * @param Request $request The current request with form data
     * @return RedirectResponse|JsonResponse
     * @throws Exception
     */
    public function submit(Request $request): RedirectResponse|JsonResponse
    {
        // Extract form data
        $name = $request->post('name');
        $email = $request->post('email');
        $subject = $request->post('subject', 'Contact Form Submission');
        $message = $request->post('message');

        // Validate form data
        $errors = $this->validateContactForm($name, $email, $message);

        // Check if validation failed
        if (!empty($errors)) {
            $this->logDebug('Contact form validation failed', ['errors' => $errors]);

            // Handle AJAX requests differently
            if ($this->isAjax() || $this->expectsJson()) {
                return $this->jsonError('Validation failed', $errors, 422);
            }

            // In a real app, you'd store these errors in the session
            // For now, just redirect back to the form
            return $this->redirect('/contact');
        }

        // Process the form submission (in a real app, you'd send an email or store in DB)
        $this->processContactSubmission($name, $email, $subject, $message);

        // Log the successful submission
        $this->logInfo('Contact form submitted successfully', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject
        ]);

        // Respond based on request type
        if ($this->isAjax() || $this->expectsJson()) {
            return $this->jsonSuccess([
                'message' => 'Your message has been sent. Thank you for contacting us!'
            ]);
        }

        // Redirect with success flag
        return $this->redirect('/contact?success=1');
    }

    /**
     * Validate the contact form input
     *
     * @param string|null $name Name input
     * @param string|null $email Email input
     * @param string|null $message Message input
     * @return array Array of validation errors
     */
    private function validateContactForm(?string $name, ?string $email, ?string $message): array
    {
        $errors = [];

        // Validate name
        if (empty($name)) {
            $errors['name'] = 'Please enter your name';
        } elseif (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        }

        // Validate email
        if (empty($email)) {
            $errors['email'] = 'Please enter your email address';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        // Validate message
        if (empty($message)) {
            $errors['message'] = 'Please enter your message';
        } elseif (strlen($message) < 10) {
            $errors['message'] = 'Message must be at least 10 characters';
        }

        return $errors;
    }

    /**
     * Process contact form submission
     *
     * @param string $name Sender's name
     * @param string $email Sender's email
     * @param string $subject Message subject
     * @param string $message Message content
     * @return void Success status
     * @throws Exception
     */
    private function processContactSubmission(string $name, string $email, string $subject, string $message): void
    {
        // In a real application, you would:
        // 1. Send an email notification
        // 2. Store the message in a database
        // 3. Maybe trigger events for other systems

        // For this example, we'll just simulate success

        // Log the contact submission
        $this->logger->info('Contact form processed', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message_length' => strlen($message)
        ]);

    }

    /**
     * API endpoint to validate contact form fields in real-time
     *
     * @param Request $request The current request
     * @return JsonResponse JSON response with validation result
     */
    public function validateField(Request $request): JsonResponse
    {
        $field = $request->post('field');
        $value = $request->post('value');

        if (!$field || !in_array($field, ['name', 'email', 'message'])) {
            return $this->jsonError('Invalid field', null, 400);
        }

        $error = null;

        // Validate based on field type
        switch ($field) {
            case 'name':
                if (empty($value)) {
                    $error = 'Please enter your name';
                } elseif (strlen($value) < 2) {
                    $error = 'Name must be at least 2 characters';
                }
                break;

            case 'email':
                if (empty($value)) {
                    $error = 'Please enter your email address';
                } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address';
                }
                break;

            case 'message':
                if (empty($value)) {
                    $error = 'Please enter your message';
                } elseif (strlen($value) < 10) {
                    $error = 'Message must be at least 10 characters';
                }
                break;
        }

        // Return validation result
        if ($error) {
            return $this->jsonError($error, [$field => $error], 422);
        }

        return $this->jsonSuccess(['field' => $field, 'valid' => true]);
    }
}
