/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
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
 * Index component for the Catalyst Framework
 *
 */


/**
 * Toast notification system for the Catalyst framework
 */
class ToastNotifications {
    constructor() {
        this.container = null;
        this.initialize();
    }

    /**
     * Initialize the toast container
     */
    initialize() {
        // Create container if it doesn't exist
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container position-fixed top-0 end-0 p-3';
            this.container.style.zIndex = '1090';
            document.body.appendChild(this.container);
        }
    }

    /**
     * Create a toast element
     *
     * @param {string} type - The toast type (success, error, warning, info)
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     * @returns {HTMLElement} - The toast element
     */
    createToast(type, message, duration = 5000) {
        const bgClass = this.getBackgroundClass(type);
        const iconClass = this.getIconClass(type);

        const toast = document.createElement('div');
        toast.className = `toast ${bgClass} text-white border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-bs-delay', duration);
        toast.innerHTML = `
            <div class="toast-header ${bgClass} text-white">
                <i class="bi ${iconClass} me-2"></i>
                <strong class="me-auto">${this.getTitle(type)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;

        return toast;
    }

    /**
     * Show a toast notification
     *
     * @param {string} type - The toast type (success, error, warning, info)
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     */
    show(type, message, duration = 5000) {
        const toast = this.createToast(type, message, duration);
        this.container.appendChild(toast);

        // Initialize the Bootstrap toast and show it
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: duration
        });

        bsToast.show();

        // Remove the toast from DOM after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            this.container.removeChild(toast);
        });
    }

    /**
     * Show a success toast
     *
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     */
    success(message, duration = 5000) {
        this.show('success', message, duration);
    }

    /**
     * Show an error toast
     *
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     */
    error(message, duration = 5000) {
        this.show('error', message, duration);
    }

    /**
     * Show a warning toast
     *
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     */
    warning(message, duration = 5000) {
        this.show('warning', message, duration);
    }

    /**
     * Show an info toast
     *
     * @param {string} message - The message to display
     * @param {number} duration - Duration in ms before auto-hide
     */
    info(message, duration = 5000) {
        this.show('info', message, duration);
    }

    /**
     * Get the background class for a toast type
     *
     * @param {string} type - The toast type
     * @returns {string} - The CSS class
     */
    getBackgroundClass(type) {
        switch (type) {
            case 'success':
                return 'bg-success';
            case 'error':
                return 'bg-danger';
            case 'warning':
                return 'bg-warning';
            case 'info':
                return 'bg-info';
            default:
                return 'bg-primary';
        }
    }

    /**
     * Get the icon class for a toast type
     *
     * @param {string} type - The toast type
     * @returns {string} - The icon class
     */
    getIconClass(type) {
        switch (type) {
            case 'success':
                return 'bi-check-circle-fill';
            case 'error':
                return 'bi-exclamation-circle-fill';
            case 'warning':
                return 'bi-exclamation-triangle-fill';
            case 'info':
                return 'bi-info-circle-fill';
            default:
                return 'bi-bell-fill';
        }
    }

    /**
     * Get the title for a toast type
     *
     * @param {string} type - The toast type
     * @returns {string} - The title
     */
    getTitle(type) {
        switch (type) {
            case 'success':
                return 'Success';
            case 'error':
                return 'Error';
            case 'warning':
                return 'Warning';
            case 'info':
                return 'Information';
            default:
                return 'Notification';
        }
    }
}

// Create global instance
window.toasts = new ToastNotifications();