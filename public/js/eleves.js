/**
 * Student Management JavaScript
 * Handles form validations, AJAX requests, and UI interactions for student management
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    initDatePickers();
    
    // Initialize phone number formatting
    initPhoneNumberFormatting();
    
    // Initialize name capitalization
    initNameCapitalization();
    
    // Initialize form validations
    initFormValidations();
    
    // Initialize class-dependent fields
    initClassDependentFields();
    
    // Initialize delete confirmation handlers
    initDeleteConfirmations();
});

/**
 * Initialize date picker inputs
 */
function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set max date to today for birth date fields
        if (input.id.includes('date_naissance')) {
            input.max = new Date().toISOString().split('T')[0];
        }
        
        // Set min date to current academic year start for date_inscription
        if (input.id === 'date_inscription') {
            const currentYear = new Date().getFullYear();
            const academicYearStart = new Date(`09/01/${currentYear}`);
            
            // If current month is before September, set to previous academic year
            if (new Date().getMonth() < 8) { // January is 0, August is 7
                academicYearStart.setFullYear(currentYear - 1);
            }
            
            input.min = academicYearStart.toISOString().split('T')[0];
            input.max = new Date().toISOString().split('T')[0];
        }
    });
}

/**
 * Format phone number inputs
 */
function initPhoneNumberFormatting() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Remove all non-digit characters
            let value = e.target.value.replace(/\D/g, '');
            
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            // Format as XX-XXXX-XXXX
            if (value.length > 6) {
                value = `${value.substring(0, 2)}-${value.substring(2, 6)}-${value.substring(6)}`;
            } else if (value.length > 2) {
                value = `${value.substring(0, 2)}-${value.substring(2)}`;
            }
            
            e.target.value = value;
        });
    });
}

/**
 * Auto-capitalize name fields
 */
function initNameCapitalization() {
    const nameInputs = ['nom', 'prenom', 'nom_pere', 'nom_mere', 'lieu_naissance'];
    
    nameInputs.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                e.target.value = e.target.value.toUpperCase();
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            });
        }
    });
}

/**
 * Initialize form validations
 */
function initFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
                
                // Scroll to the first error
                const firstError = this.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        }, false);
        
        // Add input event listeners for real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                validateField(this);
            });
            
            // Validate on blur
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

/**
 * Validate a single form field
 */
function validateField(field) {
    const isValid = checkValidity(field);
    
    // Toggle error classes
    if (isValid) {
        field.classList.remove('border-red-500');
        field.classList.add('border-green-500');
        
        // Remove error message if exists
        const errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.remove();
        }
    } else {
        field.classList.add('border-red-500');
        field.classList.remove('border-green-500');
        
        // Add or update error message
        let errorElement = field.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback text-red-500 text-sm mt-1';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
        
        // Set appropriate error message
        if (field.validity.valueMissing) {
            errorElement.textContent = field.dataset.requiredMessage || 'Ce champ est requis.';
        } else if (field.validity.typeMismatch) {
            if (field.type === 'email') {
                errorElement.textContent = 'Veuillez entrer une adresse email valide.';
            } else if (field.type === 'url') {
                errorElement.textContent = 'Veuillez entrer une URL valide.';
            }
        } else if (field.validity.patternMismatch) {
            errorElement.textContent = field.dataset.patternMessage || 'Le format saisi est invalide.';
        } else if (field.validity.tooShort) {
            errorElement.textContent = `Le champ doit contenir au moins ${field.minLength} caractères.`;
        } else if (field.validity.tooLong) {
            errorElement.textContent = `Le champ ne doit pas dépasser ${field.maxLength} caractères.`;
        } else if (field.validity.rangeUnderflow) {
            errorElement.textContent = `La valeur doit être supérieure ou égale à ${field.min}.`;
        } else if (field.validity.rangeOverflow) {
            errorElement.textContent = `La valeur doit être inférieure ou égale à ${field.max}.`;
        } else if (field.validity.stepMismatch) {
            errorElement.textContent = 'La valeur doit être un multiple de ' + (field.step || 1) + '.';
        } else if (field.validity.badInput) {
            errorElement.textContent = 'La valeur saisie est incorrecte.';
        } else if (field.validity.customError) {
            errorElement.textContent = field.validationMessage;
        } else {
            errorElement.textContent = 'La valeur saisie est invalide.';
        }
    }
    
    return isValid;
}

/**
 * Check if a field is valid
 */
function checkValidity(field) {
    // Skip disabled and readonly fields
    if (field.disabled || field.readOnly) {
        return true;
    }
    
    // Check required fields
    if (field.required && !field.value.trim()) {
        return false;
    }
    
    // Check pattern if specified
    if (field.pattern) {
        const regex = new RegExp(field.pattern);
        if (!regex.test(field.value)) {
            return false;
        }
    }
    
    // Check min/max length for text inputs
    if (field.type === 'text' || field.type === 'password' || field.type === 'textarea') {
        if (field.minLength > 0 && field.value.length < field.minLength) {
            return false;
        }
        
        if (field.maxLength > 0 && field.value.length > field.maxLength) {
            return false;
        }
    }
    
    // Check min/max for number inputs
    if ((field.type === 'number' || field.type === 'range') && field.value !== '') {
        if (field.min && parseFloat(field.value) < parseFloat(field.min)) {
            return false;
        }
        
        if (field.max && parseFloat(field.value) > parseFloat(field.max)) {
            return false;
        }
    }
    
    // Check email format
    if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            return false;
        }
    }
    
    // Check URL format
    if (field.type === 'url' && field.value) {
        try {
            new URL(field.value);
        } catch (_) {
            return false;
        }
    }
    
    return true;
}

/**
 * Validate an entire form
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Initialize class-dependent fields
 */
function initClassDependentFields() {
    const classSelect = document.getElementById('classe_id');
    
    if (classSelect) {
        // When class changes, update related fields
        classSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            // Example: Update fees based on class
            const feesField = document.getElementById('frais_inscription');
            if (feesField && selectedOption.dataset.fees) {
                feesField.value = selectedOption.dataset.fees;
            }
            
            // You can add more field updates here based on class selection
        });
    }
}

/**
 * Initialize delete confirmation handlers
 */
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modalId = this.dataset.target || 'deleteConfirmationModal';
            const modal = document.getElementById(modalId);
            
            if (modal) {
                // Set up the form action if specified
                const formId = this.dataset.formId || 'deleteForm';
                const form = document.getElementById(formId);
                
                if (form && this.dataset.deleteUrl) {
                    form.action = this.dataset.deleteUrl;
                }
                
                // Show the modal
                window.showModal(modalId);
            } else {
                // Fallback to browser confirm
                if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                    if (this.dataset.deleteUrl) {
                        // Create a form and submit it
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = this.dataset.deleteUrl;
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.content;
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    } else if (this.form) {
                        // If it's a form submit button, submit the form
                        this.form.submit();
                    }
                }
            }
        });
    });
}

/**
 * Handle AJAX form submission
 */
function handleAjaxForm(form, options = {}) {
    return new Promise((resolve, reject) => {
        if (!form || form.tagName !== 'FORM') {
            reject(new Error('Invalid form element'));
            return;
        }
        
        const url = form.action;
        const method = form.method.toUpperCase();
        const formData = new FormData(form);
        
        // Add any additional data
        if (options.data) {
            Object.entries(options.data).forEach(([key, value]) => {
                formData.append(key, value);
            });
        }
        
        // Add CSRF token if not already in form data
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && !formData.has('_token')) {
            formData.append('_token', csrfToken.content);
        }
        
        // Add method spoofing for PUT, PATCH, DELETE
        if (['PUT', 'PATCH', 'DELETE'].includes(method)) {
            formData.append('_method', method);
        }
        
        // Show loading state
        const submitButton = form.querySelector('[type="submit"]');
        const originalButtonText = submitButton ? submitButton.innerHTML : '';
        
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Traitement...';
        }
        
        // Make the request
        fetch(url, {
            method: 'POST', // Always use POST for form submissions
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options.fetchOptions
        })
        .then(response => {
            // Reset button state
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                }).catch(() => {
                    throw new Error('Une erreur est survenue. Veuillez réessayer.');
                });
            }
            
            return response.json();
        })
        .then(data => {
            // Handle success
            if (options.onSuccess) {
                options.onSuccess(data);
            } else {
                // Default success handling
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.message) {
                    showToast('success', data.message);
                }
            }
            
            resolve(data);
        })
        .catch(error => {
            // Reset button state
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            
            // Handle error
            const errorMessage = error.message || 'Une erreur est survenue. Veuillez réessayer.';
            
            if (options.onError) {
                options.onError(error);
            } else {
                // Default error handling
                showToast('error', errorMessage);
                
                // Display validation errors if any
                if (error.errors) {
                    Object.entries(error.errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorElement = document.createElement('div');
                            errorElement.className = 'invalid-feedback d-block';
                            errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;
                            
                            // Remove existing error message if any
                            const existingError = input.parentNode.querySelector('.invalid-feedback');
                            if (existingError) {
                                existingError.remove();
                            }
                            
                            input.classList.add('is-invalid');
                            input.parentNode.insertBefore(errorElement, input.nextSibling);
                        }
                    });
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            }
            
            reject(error);
        });
    });
}

/**
 * Show a toast notification
 */
function showToast(type, message, duration = 5000) {
    // Create toast container if it doesn't exist
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-3 w-80';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `p-4 rounded-lg shadow-lg text-white ${getToastClasses(type)}`;
    toast.role = 'alert';
    
    // Add message
    const messageElement = document.createElement('div');
    messageElement.className = 'flex items-center';
    messageElement.innerHTML = `
        <i class="${getToastIcon(type)} mr-3"></i>
        <span>${message}</span>
    `;
    
    // Add close button
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'ml-auto text-white opacity-75 hover:opacity-100';
    closeButton.innerHTML = '&times;';
    closeButton.onclick = () => toast.remove();
    
    messageElement.appendChild(closeButton);
    toast.appendChild(messageElement);
    container.appendChild(toast);
    
    // Auto-remove after duration
    if (duration > 0) {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease-in-out';
            
            setTimeout(() => {
                toast.remove();
                
                // Remove container if no more toasts
                if (container.children.length === 0) {
                    container.remove();
                }
            }, 500);
        }, duration);
    }
    
    return toast;
}

/**
 * Get CSS classes for toast based on type
 */
function getToastClasses(type) {
    const classes = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        warning: 'bg-yellow-600',
        info: 'bg-blue-600',
        default: 'bg-gray-800'
    };
    
    return classes[type] || classes.default;
}

/**
 * Get icon for toast based on type
 */
function getToastIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle',
        default: 'fas fa-info-circle'
    };
    
    return icons[type] || icons.default;
}

// Make functions available globally
window.validateForm = validateForm;
window.validateField = validateField;
window.handleAjaxForm = handleAjaxForm;
window.showToast = showToast;
