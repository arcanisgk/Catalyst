/**************************************************************************************
 *
 * Catalyst PHP Framework - JavaScript Component
 * ES6+/ES7 Standard
 *
 * @package   Catalyst
 * @subpackage Js
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
 * Main component for the Catalyst Framework
 *
 */
 
 /**
 * Handle OAuth credential operations with specialized UI updates
 *
 * @param {string} action - The action to perform ('save' or 'clear')
 * @param {HTMLFormElement} form - The credentials form
 * @param {string} serviceKey - The service key identifier
 * @param {bootstrap.Modal} modal - The Bootstrap modal to hide on success
 * @returns {Promise} - Promise that resolves when operation is complete
 */
async function handleOAuthCredentials(action, form, serviceKey, modal) {
    try {
        let url, data;

        if (action === 'save') {
            url = '/configure/oauth/save';
            const formData = new FormData(form);
            data = new URLSearchParams(formData).toString();
        } else if (action === 'clear') {
            url = '/configure/oauth/clear';
            data = `service_key=${serviceKey}`;
        } else {
            throw new Error('Invalid action');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: data
        });

        const result = await response.json();

        if (result.success) {
            // Get service checkbox for UI updates
            const serviceCheckbox = document.querySelector(`.service-checkbox[data-service="${serviceKey}"]`);

            if (serviceCheckbox) {
                const serviceItem = serviceCheckbox.closest('.service-item');
                if (serviceItem) {
                    if (action === 'save') {
                        // Add success badge if saving
                        const badgeParent = serviceItem.querySelector('.form-check').parentElement;
                        if (!badgeParent.querySelector('.text-success')) {
                            const badge = document.createElement('span');
                            badge.className = 'ms-1 text-success';
                            badge.title = 'Credentials configured';
                            badge.innerHTML = '<i class="bi bi-check-circle"></i>';
                            badgeParent.appendChild(badge);
                        }
                    } else if (action === 'clear') {
                        // Remove badge if clearing
                        const badge = serviceItem.querySelector('.text-success');
                        if (badge) badge.remove();
                    }
                }
            }

            // Reset form and hide modal
            if (form) form.reset();
            if (modal) modal.hide();

            // Show success toast
            window.toasts.success(`Service credentials ${action === 'save' ? 'saved' : 'cleared'} successfully`);
        } else {
            window.toasts.error(`Error: ${result.message}`);
        }

        return result;
    } catch (error) {
        console.error(`Error ${action}ing credentials:`, error);
        window.toasts.error(`An error occurred while ${action}ing credentials`);
        throw error;
    }
}


/**
 * Handle configuration form submissions with standard UI feedback
 *
 * @param {HTMLFormElement|string} form - Form element or form selector
 * @param {string} endpoint - API endpoint to submit data to
 * @param {Object} options - Additional options
 * @returns {Promise} - Promise that resolves when submission is complete
 */
async function handleConfigSubmit(form, endpoint, options = {}) {
    // Default options
    const defaults = {
        redirectDelay: 1000,
        loadingText: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...',
        submitSelector: 'button[type="submit"]',
        collectFromForms: null, // Optional selector for multiple forms to collect data from
        preProcess: null, // Optional function to process data before submission
    };

    options = {...defaults, ...options};

    // Get the form element if a selector was provided
    if (typeof form === 'string') {
        form = document.querySelector(form);
    }

    if (!form) {
        console.error('Form not found');
        return false;
    }

    // Find submit button
    const submitButton = options.submitSelector instanceof HTMLElement
        ? options.submitSelector
        : form.querySelector(options.submitSelector);

    // Store original button content
    const originalButtonContent = submitButton ? submitButton.innerHTML : '';

    try {
        // Disable submit button and show loading indicator
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = options.loadingText;
        }

        // Collect form data
        let formData = new FormData(form);

        // If we need to collect data from multiple forms
        if (options.collectFromForms) {
            const additionalForms = document.querySelectorAll(options.collectFromForms);
            additionalForms.forEach(additionalForm => {
                const additionalFormData = new FormData(additionalForm);
                for (const [key, value] of additionalFormData.entries()) {
                    formData.append(key, value);
                }
            });
        }

        // Get CSRF token from the form or document
        const csrfToken = form.querySelector('input[name="csrf_token"]')?.value ||
            document.querySelector('input[name="csrf_token"]')?.value;

        // Ensure CSRF token is included
        if (csrfToken && !formData.has('csrf_token')) {
            formData.append('csrf_token', csrfToken);
        }

        // Allow pre-processing of form data if needed
        if (typeof options.preProcess === 'function') {
            formData = options.preProcess(formData);
        }

        // Send the request
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Parse the response
        const result = await response.json();

        // Handle the result
        if (result.success) {
            window.toasts.success(result.message || 'Configuration saved successfully');

            // Redirect if specified
            if (result.redirect) {
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, options.redirectDelay);
            }

            return true;
        } else {
            window.toasts.error(result.message || 'Failed to save configuration');
            console.error('Form submission error:', result);
            return false;
        }
    } catch (error) {
        console.error('Form submission error:', error);
        window.toasts.error('An unexpected error occurred');
        return false;
    } finally {
        // Restore submit button
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonContent;
        }
    }
}


/**
 * Función utilitaria para peticiones AJAX
 * @param {string} url - URL del endpoint
 * @param {Object} data - Datos a enviar
 * @param {Object} options - Opciones adicionales
 * @returns {Promise} - Promesa con la respuesta JSON
 */
async function apiPost(url, data, options = {redirectDelay: 1000, handleRedirect: true}) {
    try {

        // Add CSRF token to the data if it doesn't already exist
        if (typeof data === 'object' && data !== null) {
            // Get CSRF token from any form or meta tag on the page
            const csrfTokenElement = document.querySelector('input[name="csrf_token"]');
            const csrfToken = csrfTokenElement ? csrfTokenElement.value : null;

            if (csrfToken && !data.csrf_token) {
                data.csrf_token = csrfToken;
            }
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]')?.value || ''
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        // Manejo estandarizado de respuestas
        if (result.success) {
            window.toasts.success(result.message);

            // Handle redirect if present and not disabled in options
            if (result.redirect && options.handleRedirect !== false) {
                // Optional delay to allow toast to be seen
                if (options.redirectDelay) {
                    await new Promise(resolve => setTimeout(resolve, options.redirectDelay));
                }
                window.location.href = result.redirect;
            }
        } else {
            window.toasts.error(result.message || 'Error en la operación');
        }

        return result;
    } catch (error) {
        console.error('Error en petición API:', error);
        window.toasts.error('Error de conexión');
        throw error;
    }
}

// Generic password toggle functionality
/**
 * Password Toggle Utility
 *
 * A standalone utility that adds toggle functionality to password fields
 * Works with both static and dynamically added elements
 */
(function () {
    'use strict';

    /**
     * Initialize password toggle functionality
     */
    function initPasswordToggle() {
        // Use event delegation for all toggle password buttons
        document.addEventListener('click', function (event) {

            // Find if a toggle-password button was clicked or any of its children
            const toggleButton = event.target.closest('.toggle-password');
            if (!toggleButton) return;

            // Find the associated input field (should be a sibling in the same input-group)
            const inputGroup = toggleButton.closest('.input-group');
            if (!inputGroup) return;

            const passwordInput = inputGroup.querySelector('input[type="password"], input[type="text"]');
            if (!passwordInput) return;

            // Find the icon element
            const iconElement = toggleButton.querySelector('i, .bi');
            // Toggle the password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (iconElement) {
                    iconElement.classList.remove('bi-eye');
                    iconElement.classList.add('bi-eye-slash');
                }
            } else {
                passwordInput.type = 'password';
                if (iconElement) {
                    iconElement.classList.remove('bi-eye-slash');
                    iconElement.classList.add('bi-eye');
                }
            }

            // Prevent the default button action
            event.preventDefault();
        });
    }

    // Initialize when the DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPasswordToggle);
    } else {
        initPasswordToggle();
    }
})();


// Add global CSRF protection for all fetch requests
(function () {
    // Store the original fetch function
    const originalFetch = window.fetch;

    // Override fetch with our version that adds CSRF tokens
    window.fetch = function (url, options = {}) {
        // Don't modify GET requests or requests that already have a body
        if (options.method && options.method.toUpperCase() !== 'GET') {
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

            if (csrfToken) {
                // If it's a FormData object, append the token
                if (options.body instanceof FormData) {
                    if (!options.body.has('csrf_token')) {
                        options.body.append('csrf_token', csrfToken);
                    }
                } else if (typeof options.body === 'string' && options.headers?.['Content-Type'] === 'application/json') {
                    // If it's JSON, parse and add token
                    try {
                        const bodyData = JSON.parse(options.body);
                        if (!bodyData.csrf_token) {
                            bodyData.csrf_token = csrfToken;
                            options.body = JSON.stringify(bodyData);
                        }
                    } catch (e) {
                        // Not valid JSON, leave as is
                    }
                }

                // Add CSRF header for all non-GET requests
                options.headers = options.headers || {};
                if (!options.headers['X-CSRF-TOKEN']) {
                    options.headers['X-CSRF-TOKEN'] = csrfToken;
                }
            }
        }

        // Call the original fetch with our modified options
        return originalFetch(url, options);
    };
})();

