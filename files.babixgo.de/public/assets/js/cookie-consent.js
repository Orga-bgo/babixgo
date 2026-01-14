/**
 * Cookie Consent & Google Analytics Integration
 * GDPR-compliant consent management with localStorage
 */

(function() {
  'use strict';

  const CONSENT_KEY = 'cookie-consent';
  const CONSENT_ACCEPTED = 'accepted';
  const CONSENT_DECLINED = 'declined';

  /**
   * Initialize cookie consent system
   */
  function initCookieConsent() {
    const banner = document.getElementById('cookieConsent');
    if (!banner) {
      console.warn('Cookie banner element not found');
      return;
    }

    // Check if user has already made a choice
    const consent = localStorage.getItem(CONSENT_KEY);

    if (!consent) {
      // Show banner after 1 second delay for better UX
      setTimeout(() => {
        banner.classList.add('show');
      }, 1000);
    } else if (consent === CONSENT_ACCEPTED) {
      // Load Google Analytics if consent was previously given
      loadGoogleAnalytics();
    }

    // Add event listeners to consent buttons
    const consentButtons = document.querySelectorAll('[data-consent]');
    consentButtons.forEach(button => {
      button.addEventListener('click', handleConsentClick);
    });
  }

  /**
   * Handle consent button clicks
   */
  function handleConsentClick(event) {
    const action = event.target.dataset.consent;
    const banner = document.getElementById('cookieConsent');

    if (action === 'accept') {
      localStorage.setItem(CONSENT_KEY, CONSENT_ACCEPTED);
      loadGoogleAnalytics();
      
      // Log for debugging (can be removed in production)
      if (window.console) {
        console.log('Cookie consent accepted - Google Analytics loaded');
      }
    } else if (action === 'decline') {
      localStorage.setItem(CONSENT_KEY, CONSENT_DECLINED);
      
      // Log for debugging
      if (window.console) {
        console.log('Cookie consent declined - Google Analytics not loaded');
      }
    }

    // Hide banner with animation
    if (banner) {
      banner.classList.remove('show');
    }
  }

  /**
   * Load Google Analytics script
   */
  function loadGoogleAnalytics() {
    const configElement = document.getElementById('trackingConfig');
    if (!configElement) {
      console.warn('Tracking config element not found');
      return;
    }

    const gaId = configElement.dataset.gaId;
    
    // Don't load if GA ID is not configured or is placeholder
    if (!gaId || gaId === 'G-XXXXXXXXXX') {
      console.warn('Google Analytics ID not configured');
      return;
    }

    // Check if GA is already loaded
    if (window.gtag) {
      console.log('Google Analytics already loaded');
      return;
    }

    // Create and append the Google Analytics script
    const script = document.createElement('script');
    script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
    script.async = true;
    
    script.onload = function() {
      // Initialize dataLayer and gtag
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        window.dataLayer.push(arguments);
      }
      window.gtag = gtag;
      
      // Configure Google Analytics
      // Note: SameSite=None requires HTTPS. For local development without HTTPS,
      // cookies may not work properly but analytics will still function.
      gtag('js', new Date());
      gtag('config', gaId, {
        'anonymize_ip': true, // IP anonymization for GDPR compliance
        'cookie_flags': 'SameSite=None;Secure' // Requires HTTPS
      });
      
      console.log('Google Analytics initialized with ID:', gaId);
    };

    script.onerror = function() {
      console.error('Failed to load Google Analytics script');
    };

    document.head.appendChild(script);
  }

  /**
   * Export function to reset consent (for testing/user preference changes)
   */
  window.resetCookieConsent = function() {
    localStorage.removeItem(CONSENT_KEY);
    const banner = document.getElementById('cookieConsent');
    if (banner) {
      banner.classList.add('show');
    }
    console.log('Cookie consent reset');
  };

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCookieConsent);
  } else {
    initCookieConsent();
  }

})();
