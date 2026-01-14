/**
 * Form Validation
 * Client-side form validation for authentication forms
 */

// Validate username
function validateUsername(username) {
    const errors = [];
    
    if (username.length < 3 || username.length > 50) {
        errors.push('Username must be between 3 and 50 characters');
    }
    
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        errors.push('Username can only contain letters, numbers, and underscores');
    }
    
    return errors;
}

// Validate email
function validateEmail(email) {
    const errors = [];
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        errors.push('Please enter a valid email address');
    }
    
    return errors;
}

// Validate password
function validatePassword(password) {
    const errors = [];
    
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
    }
    
    if (!/[A-Z]/.test(password)) {
        errors.push('Password must contain at least one uppercase letter');
    }
    
    if (!/[a-z]/.test(password)) {
        errors.push('Password must contain at least one lowercase letter');
    }
    
    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number');
    }
    
    return errors;
}

// Validate password confirmation
function validatePasswordConfirmation(password, confirmation) {
    const errors = [];
    
    if (password !== confirmation) {
        errors.push('Passwords do not match');
    }
    
    return errors;
}

// Show field error
function showFieldError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

// Clear field error
function clearFieldError(fieldId) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Username validation
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            const errors = validateUsername(this.value);
            if (errors.length > 0) {
                showFieldError('username', errors[0]);
            } else {
                clearFieldError('username');
            }
        });
        
        usernameInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearFieldError('username');
            }
        });
    }
    
    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const errors = validateEmail(this.value);
            if (errors.length > 0) {
                showFieldError('email', errors[0]);
            } else {
                clearFieldError('email');
            }
        });
        
        emailInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearFieldError('email');
            }
        });
    }
    
    // Password validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('blur', function() {
            const errors = validatePassword(this.value);
            if (errors.length > 0) {
                showFieldError('password', errors[0]);
            } else {
                clearFieldError('password');
            }
        });
        
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearFieldError('password');
            }
        });
    }
    
    // Password confirmation validation
    const passwordConfirmInput = document.getElementById('password_confirm');
    if (passwordConfirmInput && passwordInput) {
        passwordConfirmInput.addEventListener('blur', function() {
            const errors = validatePasswordConfirmation(passwordInput.value, this.value);
            if (errors.length > 0) {
                showFieldError('password-confirm', errors[0]);
            } else {
                clearFieldError('password-confirm');
            }
        });
        
        passwordConfirmInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearFieldError('password-confirm');
            }
        });
    }
});

// Password strength indicator
function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

function getPasswordStrengthLabel(strength) {
    if (strength < 3) return 'Weak';
    if (strength < 5) return 'Medium';
    return 'Strong';
}

function getPasswordStrengthColor(strength) {
    if (strength < 3) return '#e74c3c';
    if (strength < 5) return '#f39c12';
    return '#27ae60';
}
