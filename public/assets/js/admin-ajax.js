/**
 * Admin AJAX Helper Functions
 * Reusable functions for consistent AJAX operations across admin panels
 */

class AdminAjax {
    
    /**
     * Initialize Toastr configuration
     */
    static initToastr() {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        }
    }
    
    /**
     * Show message to user using Toastr
     */
    static showMessage(type, message) {
        // Initialize Toastr if not already done
        AdminAjax.initToastr();
        
        // Use Toastr if available, otherwise fallback to alert
        if (typeof toastr !== 'undefined') {
            // Clear any existing duplicates
            toastr.clear();
            
            // Show the appropriate toast
            switch (type) {
                case 'success':
                    toastr.success(message);
                    break;
                case 'error':
                case 'danger':
                    toastr.error(message);
                    break;
                case 'warning':
                    toastr.warning(message);
                    break;
                case 'info':
                    toastr.info(message);
                    break;
                default:
                    toastr.info(message);
            }
            
            console.log('AdminAjax: Toastr message shown:', type, message);
        } else {
            // Fallback to browser alert if Toastr is not available
            console.warn('Toastr not available, using browser alert');
            alert(message);
        }
    }

    /**
     * Clear all existing messages
     */
    static clearMessages() {
        // Clear Toastr messages if available
        if (typeof toastr !== 'undefined') {
            toastr.clear();
        }
        
        // Also clear any remaining Bootstrap alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            try {
                // Try Bootstrap's close method first
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } else {
                    // Fallback to manual removal
                    alert.remove();
                }
            } catch (e) {
                // If anything fails, just remove the element
                alert.remove();
            }
        });
    }

    /**
     * Set button loading state
     */
    static setButtonLoading(button, loading = true) {
        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');
        
        if (loading) {
            button.disabled = true;
            if (btnText) btnText.classList.add('d-none');
            if (btnLoading) btnLoading.classList.remove('d-none');
        } else {
            button.disabled = false;
            if (btnText) btnText.classList.remove('d-none');
            if (btnLoading) btnLoading.classList.add('d-none');
        }
    }

    /**
     * Handle form submission with AJAX
     */
    static submitForm(formElement, url, options = {}) {
        return new Promise((resolve, reject) => {
            const formData = new FormData(formElement);
            const button = options.button;
            
            // Check for Laravel method spoofing (_method field) first
            const methodField = formData.get('_method');
            let method;
            
            if (methodField) {
                // For Laravel method spoofing (PUT, PATCH, DELETE), always use POST
                method = 'POST';
                console.log('AdminAjax: Laravel method spoofing detected:', methodField, '- using POST');
            } else {
                // Use provided method or default to POST for form submissions
                method = options.method || 'POST';
                console.log('AdminAjax: No method spoofing, using method:', method);
            }
            console.log('AdminAjax: Submitting form to', url, 'with method', method);
            
            // Log form data for debugging (excluding files for security)
            const formDataEntries = {};
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    formDataEntries[key] = `File: ${value.name} (${value.size} bytes)`;
                } else {
                    formDataEntries[key] = value;
                }
            }
            console.log('AdminAjax: Form data:', formDataEntries);
            
            // Set loading state
            if (button) {
                AdminAjax.setButtonLoading(button, true);
            }
            
            // Clear previous messages
            AdminAjax.clearMessages();
            
            // Make AJAX request
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                }
            })
            .then(response => {
                console.log('AdminAjax: Response status:', response.status, response.statusText);
                if (!response.ok) {
                    return response.json().then(data => {
                        console.log('AdminAjax: Error response data:', data);
                        return Promise.reject(data);
                    }).catch(() => {
                        // If response is not JSON
                        return Promise.reject({
                            success: false,
                            message: `HTTP ${response.status}: ${response.statusText}`
                        });
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    AdminAjax.showMessage('success', data.message || 'Operation completed successfully!');
                    resolve(data);
                } else {
                    AdminAjax.showMessage('error', data.message || 'Operation failed');
                    
                    // Show validation errors if any
                    if (data.errors) {
                        let errorList = '<ul class="mb-0">';
                        Object.values(data.errors).forEach(errors => {
                            errors.forEach(error => {
                                errorList += `<li>${error}</li>`;
                            });
                        });
                        errorList += '</ul>';
                        AdminAjax.showMessage('error', errorList);
                    }
                    reject(data);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                const errorMessage = error.message || 'An unexpected error occurred. Please try again.';
                AdminAjax.showMessage('error', errorMessage);
                reject(error);
            })
            .finally(() => {
                // Reset loading state
                if (button) {
                    AdminAjax.setButtonLoading(button, false);
                }
            });
        });
    }

    /**
     * Handle simple AJAX request (for buttons, links, etc.)
     */
    static makeRequest(url, options = {}) {
        return new Promise((resolve, reject) => {
            const button = options.button;
            let originalButtonContent = '';
            
            // Set loading state for button
            if (button) {
                originalButtonContent = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    ${options.loadingText || 'Loading...'}
                `;
            }
            
            // Clear messages if requested
            if (options.clearMessages !== false) {
                AdminAjax.clearMessages();
            }
            
            fetch(url, {
                method: options.method || 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                },
                body: options.body ? JSON.stringify(options.body) : null
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (options.showMessage !== false) {
                        AdminAjax.showMessage('success', data.message || 'Operation completed successfully!');
                    }
                    resolve(data);
                } else {
                    AdminAjax.showMessage('error', data.message || 'Operation failed');
                    reject(data);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                const errorMessage = error.message || 'An unexpected error occurred. Please try again.';
                AdminAjax.showMessage('error', errorMessage);
                reject(error);
            })
            .finally(() => {
                // Reset button state
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalButtonContent;
                }
            });
        });
    }

    /**
     * Confirm action with SweetAlert or browser confirm
     */
    static confirmAction(message, title = 'Are you sure?') {
        // Use browser confirm for now, can be upgraded to SweetAlert later
        return Promise.resolve(confirm(`${title}\n\n${message}`));
    }

    /**
     * Handle delete operations with confirmation
     */
    static deleteItem(url, options = {}) {
        const message = options.message || 'This item will be permanently deleted.';
        const title = options.title || 'Delete Item';
        
        return AdminAjax.confirmAction(message, title)
            .then(confirmed => {
                if (confirmed) {
                    return AdminAjax.makeRequest(url, {
                        method: 'DELETE',
                        button: options.button,
                        loadingText: 'Deleting...',
                        ...options
                    });
                } else {
                    return Promise.reject({ cancelled: true });
                }
            });
    }

    /**
     * Handle toggle operations (publish/unpublish, enable/disable, etc.)
     */
    static toggleStatus(url, options = {}) {
        return AdminAjax.makeRequest(url, {
            method: 'POST',
            button: options.button,
            loadingText: options.loadingText || 'Updating...',
            showMessage: options.showMessage !== false,
            ...options
        });
    }

    /**
     * Test message display (for debugging)
     */
    static testMessage(type = 'success', message = 'Test message') {
        AdminAjax.showMessage(type, message);
    }
}

// Auto-setup common event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-handle forms with data-ajax attribute
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        const url = form.getAttribute('action') || form.dataset.ajax;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            AdminAjax.submitForm(this, url, {
                button: submitButton
                // Method will be determined automatically by submitForm
            }).then(data => {
                // Custom success callback if defined
                if (form.dataset.onSuccess) {
                    try {
                        const callback = new Function('data', form.dataset.onSuccess);
                        callback(data);
                    } catch (e) {
                        console.error('Success callback error:', e);
                    }
                }
            }).catch(error => {
                // Custom error callback if defined
                if (form.dataset.onError) {
                    try {
                        const callback = new Function('error', form.dataset.onError);
                        callback(error);
                    } catch (e) {
                        console.error('Error callback error:', e);
                    }
                }
            });
        });
    });

    // Auto-handle buttons with data-ajax attribute
    document.querySelectorAll('[data-ajax]').forEach(element => {
        if (element.tagName === 'FORM') return; // Skip forms, handled above
        
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.dataset.ajax;
            const method = this.dataset.method || 'POST';
            const confirmMessage = this.dataset.confirm;
            const loadingText = this.dataset.loading;
            
            const makeRequest = () => {
                AdminAjax.makeRequest(url, {
                    method: method,
                    button: this,
                    loadingText: loadingText
                }).then(data => {
                    // Custom success callback if defined
                    if (this.dataset.onSuccess) {
                        try {
                            const callback = new Function('data', this.dataset.onSuccess);
                            callback(data);
                        } catch (e) {
                            console.error('Success callback error:', e);
                        }
                    }
                });
            };

            if (confirmMessage) {
                AdminAjax.confirmAction(confirmMessage).then(confirmed => {
                    if (confirmed) makeRequest();
                });
            } else {
                makeRequest();
            }
        });
    });
});

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminAjax;
}