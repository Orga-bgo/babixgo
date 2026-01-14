/**
 * Header Navigation JavaScript
 * Handles mobile menu toggle - supports both class and id selectors
 */

document.addEventListener('DOMContentLoaded', () => {
  // Support both id and class selectors for flexibility
  const menuToggle = document.getElementById('menuToggle') || document.querySelector('.menu-toggle');
  const mobileMenu = document.getElementById('mobileMenu') || document.querySelector('.mobile-menu');
  const dropdownToggles = document.querySelectorAll('.menu-dropdown-toggle');

  // Mobile Menu Toggle
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const isActive = menuToggle.classList.toggle('active');
      mobileMenu.classList.toggle('active');
      menuToggle.setAttribute('aria-expanded', isActive);
    });
  }

  // Dropdown Toggles (Mobile) - for category dropdowns if present
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      const dropdown = toggle.closest('.menu-dropdown');
      const isActive = dropdown.classList.toggle('active');
      toggle.setAttribute('aria-expanded', isActive);
    });
  });

  // Close mobile menu on resize to desktop
  if (menuToggle && mobileMenu) {
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024) {
        mobileMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
});
