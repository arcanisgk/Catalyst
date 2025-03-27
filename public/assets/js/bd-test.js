/**
 * Database Connection Test Utility
 *
 * Provides functionality for testing database connections in the configuration panel.
 * This module follows the same patterns as other utility functions in main.js
 */

/**
 * Test a database connection using the provided form data
 *
 * @param {number|string} connectionId - The ID of the connection to test
 * @param {HTMLFormElement|string} form - The form element or selector containing connection details
 * @returns {Promise} - Promise that resolves with the test result
 */
async function testDatabaseConnection(connectionId, form) {
    // Get the form element if a selector was provided
    if (typeof form === 'string') {
        form = document.querySelector(form);
    }

    if (!form) {
        form = document.querySelector(`form[data-connection-id="${connectionId}"]`);
        if (!form) {
            throw new Error(`Form for connection ID ${connectionId} not found`);
        }
    }

    try {
        // Create FormData object and add test-specific fields
        const formData = new FormData(form);
        formData.append('type', 'db');
        formData.append('connection_id', connectionId);

        // Display loading indicator
        const testButton = document.querySelector(`.test-connection[data-connection-id="${connectionId}"]`);
        const originalContent = testButton ? testButton.innerHTML : null;

        if (testButton) {
            testButton.disabled = true;
            testButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Testing...';
        }

        // Send the request
        const response = await fetch('/configure/test-connection', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        // Show appropriate feedback
        if (result.success) {
            window.toasts.success('Connection successful: ' + result.message);
        } else {
            window.toasts.error('Connection failed: ' + result.message);
        }

        return result;
    } catch (error) {
        console.error('Error testing database connection:', error);
        window.toasts.error('An error occurred while testing the connection');
        throw error;
    } finally {
        // Restore the test button
        const testButton = document.querySelector(`.test-connection[data-connection-id="${connectionId}"]`);
        if (testButton) {
            testButton.disabled = false;
            testButton.innerHTML = '<i class="bi bi-check-circle"></i> Test Connection';
        }
    }
}

// Export the function to the global scope for use in HTML
window.testDatabaseConnection = testDatabaseConnection;