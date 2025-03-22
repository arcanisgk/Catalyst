/**
 * Catalyst Framework - Landing Page
 * Modern ES6+ implementation
 */

// Use strict mode for better error catching
'use strict';

/**
 * Landing page controller class
 */
class LandingPage {
    /**
     * Initialize the landing page
     */
    constructor() {
        // DOM elements
        this.navbar = document.querySelector('.navbar-default');
        this.navLinks = document.querySelectorAll('.page-scroll');

        // Initialize components
        this.initNavigation();
        this.initAnimations();
        this.initContactForm();
        this.initFlaskEffect(); // Add this new method call
    }

    /**
     * Initialize navigation behavior
     */
    initNavigation() {
        // Handle scroll events to change navbar appearance
        window.addEventListener('scroll', () => {
            if (window.scrollY > 200) {
                this.navbar.classList.add('navbar-scroll');
            } else {
                this.navbar.classList.remove('navbar-scroll');
            }
        });

        // Smooth scrolling for navigation links
        this.navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                const targetId = link.getAttribute('href');
                if (targetId.startsWith('#')) {
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const headerOffset = 70;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                } else {
                    // For non-anchor links, navigate normally
                    window.location.href = targetId;
                }
            });
        });
    }

    /**
     * Initialize WOW.js animations
     */
    initAnimations() {
        // Initialize WOW animations (already included via Inspinia)
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    }

    /**
     * Initialize flask icon animation effect
     */
    initFlaskEffect() {
        // Add a subtle bubbling/pulsing effect to the flask
        const flask = document.querySelector('.flask-icon');
        if (flask) {
            // Initial animation
            setTimeout(() => {
                this.animateFlask(flask);
            }, 1000);

            // Periodic animation
            setInterval(() => {
                this.animateFlask(flask);
            }, 5000);
        }
    }

    /**
     * Animate the flask icon
     * @param {HTMLElement} flask - The flask icon element
     */
    animateFlask(flask) {
        // Add animation class
        flask.classList.add('fa-beat');

        // Remove after animation completes
        setTimeout(() => {
            flask.classList.remove('fa-beat');
        }, 1000);
    }

    /**
     * Initialize contact form with validation and AJAX submission
     */
    initContactForm() {
        const form = document.querySelector('.contact-form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate form
            if (!this.validateForm(form)) return;

            try {
                // Show loading indicator
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
                submitBtn.disabled = true;

                // Prepare form data
                const formData = new FormData(form);

                // Send form data via fetch API
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;

                // Handle response
                if (result.success) {
                    // Show success message
                    form.reset();
                    this.showMessage('Your message has been sent successfully!', 'success');
                } else {
                    this.showMessage(result.message || 'Failed to send message. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Contact form submission error:', error);
                this.showMessage('An error occurred. Please try again later.', 'error');
            }
        });
    }

    /**
     * Validate form inputs
     * @param {HTMLFormElement} form - The form to validate
     * @returns {boolean} - Whether the form is valid
     */
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, textarea');

        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }

            if (input.type === 'email' && input.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value)) {
                    isValid = false;
                    input.classList.add('is-invalid');
                }
            }
        });

        return isValid;
    }

    /**
     * Show a message to the user
     * @param {string} message - The message text
     * @param {string} type - The message type (success/error)
     */
    showMessage(message, type) {
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        messageEl.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Find the form
        const form = document.querySelector('.contact-form');

        // Insert message before the form
        form.parentNode.insertBefore(messageEl, form);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            messageEl.classList.remove('show');
            setTimeout(() => messageEl.remove(), 150);
        }, 5000);
    }
}

// Initialize the landing page when DOM content is loaded
document.addEventListener('DOMContentLoaded', () => {
    const landingPage = new LandingPage();
});
