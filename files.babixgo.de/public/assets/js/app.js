/**
 * BabixGO Files - Main Application JavaScript
 */

// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        console.log('ServiceWorker registered:', registration.scope);
      })
      .catch(error => {
        console.log('ServiceWorker registration failed:', error);
      });
  });
}

// DOM Ready
document.addEventListener('DOMContentLoaded', () => {
  initApp();
});

/**
 * Initialize Application
 */
function initApp() {
  // Initialize comment forms
  initCommentForms();
  
  // Initialize delete confirmations
  initDeleteConfirmations();
  
  // Initialize form validations
  initFormValidations();
  
  // Initialize mobile menu toggle (if exists)
  initMobileMenu();
  
  // Initialize auto-dismiss alerts
  initAlertDismiss();
}

/**
 * Initialize Comment Forms
 */
function initCommentForms() {
  const commentForms = document.querySelectorAll('.comment-form');
  
  commentForms.forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const submitBtn = form.querySelector('button[type="submit"]');
      const textarea = form.querySelector('textarea');
      const originalText = submitBtn.textContent;
      
      // Disable button and show loading
      submitBtn.disabled = true;
      submitBtn.textContent = 'Wird gesendet...';
      
      try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Clear textarea
          textarea.value = '';
          
          // Add new comment to list
          const commentsContainer = form.closest('.comments-section').querySelector('.comments-list');
          if (commentsContainer && result.comment) {
            const commentHtml = createCommentHtml(result.comment);
            commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
          }
          
          // Update comment count
          const countEl = form.closest('.download-card')?.querySelector('.comment-count');
          if (countEl) {
            const currentCount = parseInt(countEl.textContent) || 0;
            countEl.textContent = currentCount + 1;
          }
          
          showAlert('Kommentar erfolgreich hinzugefügt!', 'success');
        } else {
          showAlert(result.message || 'Fehler beim Senden des Kommentars', 'error');
        }
      } catch (error) {
        console.error('Comment submit error:', error);
        showAlert('Ein Fehler ist aufgetreten. Bitte versuche es erneut.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    });
  });
}

/**
 * Create comment HTML
 */
function createCommentHtml(comment) {
  return `
    <div class="comment" data-comment-id="${escapeHtml(String(comment.id))}">
      <div class="comment-header">
        <span class="comment-author">${escapeHtml(comment.username)}</span>
        <span class="comment-date">gerade eben</span>
      </div>
      <p class="comment-text">${escapeHtmlWithNewlines(comment.comment_text)}</p>
    </div>
  `;
}

/**
 * Initialize Delete Confirmations
 */
function initDeleteConfirmations() {
  const deleteButtons = document.querySelectorAll('[data-confirm]');
  
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const message = btn.dataset.confirm || 'Bist du sicher?';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });
}

/**
 * Initialize Form Validations
 */
function initFormValidations() {
  // Password confirmation
  const registerForm = document.querySelector('form[data-validate="register"]');
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      const password = registerForm.querySelector('input[name="password"]');
      const confirmPassword = registerForm.querySelector('input[name="password_confirm"]');
      
      if (password && confirmPassword && password.value !== confirmPassword.value) {
        e.preventDefault();
        showAlert('Die Passwörter stimmen nicht überein.', 'error');
        confirmPassword.focus();
      }
    });
  }
  
  // Real-time password strength indicator
  const passwordInputs = document.querySelectorAll('input[type="password"][data-strength]');
  passwordInputs.forEach(input => {
    const strengthIndicator = document.querySelector(input.dataset.strength);
    if (strengthIndicator) {
      input.addEventListener('input', () => {
        const strength = getPasswordStrength(input.value);
        updateStrengthIndicator(strengthIndicator, strength);
      });
    }
  });
}

/**
 * Get password strength
 */
function getPasswordStrength(password) {
  let score = 0;
  
  if (password.length >= 8) score++;
  if (password.length >= 12) score++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^a-zA-Z0-9]/.test(password)) score++;
  
  if (score <= 1) return { level: 'weak', text: 'Schwach', color: 'var(--md-error)' };
  if (score <= 3) return { level: 'medium', text: 'Mittel', color: 'var(--md-tertiary)' };
  return { level: 'strong', text: 'Stark', color: 'var(--md-success)' };
}

/**
 * Update strength indicator
 */
function updateStrengthIndicator(element, strength) {
  element.textContent = strength.text;
  element.style.color = strength.color;
}

/**
 * Initialize Mobile Menu
 */
function initMobileMenu() {
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  const nav = document.querySelector('.header-nav');
  
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('open');
      menuToggle.setAttribute('aria-expanded', nav.classList.contains('open'));
    });
  }
}

/**
 * Initialize Alert Auto-Dismiss
 */
function initAlertDismiss() {
  const alerts = document.querySelectorAll('.alert[data-dismiss]');
  
  alerts.forEach(alert => {
    const delay = parseInt(alert.dataset.dismiss) || 5000;
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    }, delay);
  });
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
  const alertContainer = document.querySelector('.alert-container') || createAlertContainer();
  
  const alert = document.createElement('div');
  alert.className = `alert alert-${type}`;
  alert.textContent = message;
  alert.setAttribute('role', 'alert');
  
  alertContainer.appendChild(alert);
  
  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 300);
  }, 5000);
}

/**
 * Create alert container if not exists
 */
function createAlertContainer() {
  const container = document.createElement('div');
  container.className = 'alert-container';
  container.style.cssText = 'position: fixed; top: 100px; right: 20px; z-index: 1002; display: flex; flex-direction: column; gap: 10px;';
  document.body.appendChild(container);
  return container;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

/**
 * Escape HTML and convert newlines to <br> tags
 */
function escapeHtmlWithNewlines(text) {
  return escapeHtml(text).replace(/\n/g, '<br>');
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
  const units = ['B', 'KB', 'MB', 'GB', 'TB'];
  let unitIndex = 0;
  
  while (bytes >= 1024 && unitIndex < units.length - 1) {
    bytes /= 1024;
    unitIndex++;
  }
  
  return `${bytes.toFixed(2)} ${units[unitIndex]}`;
}

/**
 * Format relative time
 */
function formatRelativeTime(date) {
  const now = new Date();
  const diff = (now - new Date(date)) / 1000;
  
  if (diff < 60) return 'gerade eben';
  if (diff < 3600) {
    const minutes = Math.floor(diff / 60);
    return `vor ${minutes} Minute${minutes > 1 ? 'n' : ''}`;
  }
  if (diff < 86400) {
    const hours = Math.floor(diff / 3600);
    return `vor ${hours} Stunde${hours > 1 ? 'n' : ''}`;
  }
  if (diff < 604800) {
    const days = Math.floor(diff / 86400);
    return `vor ${days} Tag${days > 1 ? 'en' : ''}`;
  }
  
  return new Date(date).toLocaleDateString('de-DE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

// Export functions for use in other scripts
window.BabixGOFiles = {
  showAlert,
  escapeHtml,
  formatFileSize,
  formatRelativeTime
};
