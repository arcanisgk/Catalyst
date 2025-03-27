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

    console.log(options)

    // Get the form element if a selector was provided
    if (typeof form === 'string') {
        form = document.querySelector(form);
    }

    let submitButton = form.querySelector(options.submitSelector);
    if (!submitButton) {
        submitButton = document.querySelector(options.submitSelector);
        if (!submitButton) {
            console.error('Submit button not found:', options.submitSelector);
            return;
        }
    }

    // Store original button state
    const originalText = submitButton.innerHTML;

    try {
        // Update button to loading state
        submitButton.disabled = true;
        submitButton.innerHTML = options.loadingText;

        // Collect data from multiple forms if specified
        let data = {};

        if (options.collectFromForms) {
            const forms = document.querySelectorAll(options.collectFromForms);
            forms.forEach(formElement => {
                new FormData(formElement).forEach((value, key) => {
                    data[key] = value;
                });
            });
        } else {
            // Otherwise just use the main form
            const formData = new FormData(form);
            formData.forEach((value, key) => {
                data[key] = value;
            });
        }

        // Apply pre-processing function if provided
        if (typeof options.preProcess === 'function') {
            data = options.preProcess(data);
        }

        // Make API request
        const result = await apiPost(endpoint, data, {
            redirectDelay: options.redirectDelay
        });

        console.log('Response data:', result);
        return result;
    } catch (error) {
        console.error('Error submitting form:', error);
        window.toasts.error('An error occurred while saving the configuration');
        throw error;
    } finally {
        // Restore button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
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
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
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
