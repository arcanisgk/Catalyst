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

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response;
use Exception;

/**************************************************************************************
 * Authentication Controller
 *
 * Handles user authentication, registration and session management
 *
 * @package Catalyst\Solution\Controllers
 */
class AuthenticationController extends Controller
{
    /**
     * Display the login form
     *
     * @param Request $request Current request
     * @return Response\ViewResponse|Response\RedirectResponse
     */
    public function loginForm(Request $request): Response\ViewResponse|Response\RedirectResponse
    {
        // If user is already logged in, redirect to dashboard
        if ($this->isUserLoggedIn()) {
            return $this->redirect('/dashboard');
        }

        // Check for redirect parameter (where to go after login)
        $redirect = $request->get('redirect', '/dashboard');

        // Get any error message from a failed login attempt
        $error = $request->get('error');

        return $this->viewWithLayout('auth.login', [
            'title' => 'Login',
            'redirect' => $redirect,
            'error' => $error,
            'old' => [
                'email' => $request->get('email', '')
            ]
        ], 'auth');
    }

    /**
     * Process login form submission
     *
     * @param Request $request Request containing login credentials
     * @return Response\RedirectResponse|Response\JsonResponse
     * @throws Exception
     */
    public function login(Request $request): Response\RedirectResponse|Response\JsonResponse
    {
        // Get credentials from request
        $email = $request->post('email');
        $password = $request->post('password');
        $remember = (bool)$request->post('remember', false);
        $redirect = $request->post('redirect', '/dashboard');

        // Validate input
        $errors = $this->validateLoginInput($email, $password);
        if (!empty($errors)) {
            if ($this->expectsJson()) {
                return $this->jsonError('Login failed', $errors, 422);
            }

            // Redirect back with error
            return $this->redirect('/auth/login?error=invalid_credentials&email=' . urlencode($email));
        }

        // Attempt to authenticate
        $authenticated = $this->attemptLogin($email, $password);

        if (!$authenticated) {
            $this->logInfo('Failed login attempt', ['email' => $email]);

            if ($this->expectsJson()) {
                return $this->jsonError('Invalid credentials', null, 401);
            }

            return $this->redirect('/auth/login?error=invalid_credentials&email=' . urlencode($email));
        }

        // Set up the user session
        $this->createUserSession($email, $remember);

        // Log successful login
        $this->logInfo('User logged in', ['email' => $email]);

        if ($this->expectsJson()) {
            return $this->jsonSuccess([
                'message' => 'Login successful',
                'redirect' => $redirect
            ]);
        }

        // Redirect to intended destination
        return $this->redirect($redirect);
    }

    /**
     * Display registration form
     *
     * @param Request $request Current request
     * @return Response\ViewResponse|Response\RedirectResponse
     */
    public function registerForm(Request $request): Response\ViewResponse|Response\RedirectResponse
    {
        // If user is already logged in, redirect to dashboard
        if ($this->isUserLoggedIn()) {
            return $this->redirect('/dashboard');
        }

        // Check for error messages
        $error = $request->get('error');

        return $this->viewWithLayout('auth.register', [
            'title' => 'Create an Account',
            'error' => $error,
            'old' => [
                'name' => $request->get('name', ''),
                'email' => $request->get('email', '')
            ]
        ], 'auth');
    }

    /**
     * Process registration form submission
     *
     * @param Request $request Request containing registration data
     * @return Response\RedirectResponse|Response\JsonResponse
     * @throws Exception
     */
    public function register(Request $request): Response\RedirectResponse|Response\JsonResponse
    {
        // Get registration data
        $name = $request->post('name');
        $email = $request->post('email');
        $password = $request->post('password');
        $passwordConfirm = $request->post('password_confirm');

        // Validate registration data
        $errors = $this->validateRegistrationInput($name, $email, $password, $passwordConfirm);

        if (!empty($errors)) {
            if ($this->expectsJson()) {
                return $this->jsonError('Registration failed', $errors, 422);
            }

            // In a real app, you'd use flash messages to persist errors
            // For this example, we'll use query parameters
            return $this->redirect('/auth/register?error=validation_failed&name=' .
                urlencode($name) . '&email=' . urlencode($email));
        }

        // Check if user already exists
        if ($this->userExists($email)) {
            if ($this->expectsJson()) {
                return $this->jsonError('Email already in use', ['email' => 'This email is already registered'], 422);
            }

            return $this->redirect('/auth/register?error=email_exists&name=' .
                urlencode($name) . '&email=' . urlencode($email));
        }

        // Create the user
        $userId = $this->createUser($name, $email, $password);

        if (!$userId) {
            $this->logError('Failed to create user', ['email' => $email]);

            if ($this->expectsJson()) {
                return $this->jsonError('Registration failed', null, 500);
            }

            return $this->redirect('/auth/register?error=registration_failed&name=' .
                urlencode($name) . '&email=' . urlencode($email));
        }

        // Log user creation
        $this->logInfo('New user registered', ['email' => $email, 'user_id' => $userId]);

        // Automatically log the user in
        $this->createUserSession($email, false);

        if ($this->expectsJson()) {
            return $this->jsonSuccess([
                'message' => 'Registration successful',
                'redirect' => '/dashboard'
            ]);
        }

        // Redirect to dashboard
        return $this->redirect('/dashboard');
    }

    /**
     * Process logout request
     *
     * @param Request $request Current request
     * @return Response\RedirectResponse|Response\JsonResponse
     * @throws Exception
     */
    public function logout(Request $request): Response\RedirectResponse|Response\JsonResponse
    {
        // Get current user info for logging
        $userEmail = $_SESSION['user_email'] ?? 'unknown';

        // Clear the session
        $this->destroyUserSession();

        // Log logout
        $this->logInfo('User logged out', ['email' => $userEmail]);

        if ($this->expectsJson()) {
            return $this->jsonSuccess(['message' => 'Logout successful']);
        }

        // Redirect to home page
        return $this->redirect('/');
    }

    /**
     * Validate login input
     *
     * @param string|null $email User email
     * @param string|null $password User password
     * @return array Validation errors
     */
    private function validateLoginInput(?string $email, ?string $password): array
    {
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }

    /**
     * Validate registration input
     *
     * @param string|null $name User name
     * @param string|null $email User email
     * @param string|null $password User password
     * @param string|null $passwordConfirm Password confirmation
     * @return array Validation errors
     */
    private function validateRegistrationInput(
        ?string $name,
        ?string $email,
        ?string $password,
        ?string $passwordConfirm
    ): array
    {
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        }

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }

        return $errors;
    }

    /**
     * Attempt to authenticate user with credentials
     *
     * @param string $email User email
     * @param string $password User password
     * @return bool Authentication success
     */
    private function attemptLogin(string $email, string $password): bool
    {
        // In a real application, you would:
        // 1. Look up the user in your database
        // 2. Verify password hash
        // 3. Check account status

        // For this example, we'll simulate authentication
        // Assume admin@example.com/password is a valid login
        if ($email === 'admin@example.com' && $password === 'password') {
            return true;
        }

        return false;
    }

    /**
     * Create user session after successful authentication
     *
     * @param string $email User email
     * @param bool $remember Whether to create a persistent session
     * @return void
     */
    private function createUserSession(string $email, bool $remember): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // In a real app, you would get this from your database
        $userData = [
            'id' => 1, // Example user ID
            'name' => 'Admin User',
            'email' => $email,
            'role' => 'admin'
        ];

        // Store user info in session
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_role'] = $userData['role'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['last_activity'] = time();

        // Set a longer session lifetime if "remember me" is checked
        if ($remember) {
            // Set the session cookie to expire in 30 days
            session_set_cookie_params(30 * 24 * 60 * 60);
            $_SESSION['remember_me'] = true;
        }
    }

    /**
     * Destroy user session (logout)
     *
     * @return void
     */
    private function destroyUserSession(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session variables
        $_SESSION = [];

        // If a session cookie is used, clear it
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Check if a user is currently logged in
     *
     * @return bool True if user is logged in
     */
    private function isUserLoggedIn(): bool
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    /**
     * Check if a user with the given email already exists
     *
     * @param string $email Email to check
     * @return bool True if user exists
     */
    private function userExists(string $email): bool
    {
        // In a real application, you would query your database
        // For this example, we'll just check a hardcoded value
        return $email === 'admin@example.com';
    }

    /**
     * Create a new user account
     *
     * @param string $name User name
     * @param string $email User email
     * @param string $password User password (plaintext, will be hashed)
     * @return int|false User ID if successful, false on failure
     * @throws Exception
     */
    private function createUser(string $name, string $email, string $password): false|int
    {
        // In a real application, you would:
        // 1. Hash the password securely
        // 2. Insert the user into your database
        // 3. Return the new user ID

        // For this example, we'll simulate user creation
        // Only allow creation of non-existing users
        if ($this->userExists($email)) {
            return false;
        }

        // Simulate password hashing
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Log the user creation
        $this->logInfo('User created', [
            'name' => $name,
            'email' => $email
        ]);

        // Return a fake user ID
        return rand(100, 999);
    }
}
