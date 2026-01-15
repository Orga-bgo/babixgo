(() => {
  const yearTargets = document.querySelectorAll('.year-auto');
  if (yearTargets.length > 0) {
    const year = new Date().getFullYear();
    yearTargets.forEach((el) => {
      el.textContent = year;
    });
  }

  const hideOnErrorImages = document.querySelectorAll('.js-hide-on-error');
  if (hideOnErrorImages.length > 0) {
    hideOnErrorImages.forEach((img) => {
      if (img.dataset.hideOnErrorAttached === 'true') {
        return;
      }
      img.dataset.hideOnErrorAttached = 'true';
      img.addEventListener('error', () => {
        img.classList.add('is-hidden');
      }, { once: true });
    });
  }

  const trackingConfig = document.getElementById('trackingConfig');
  if (trackingConfig) {
    const gaId = trackingConfig.dataset.gaId || '';
    const fbId = trackingConfig.dataset.fbId || '';

    const loadTrackingScripts = () => {
      if (loadTrackingScripts.loaded) {
        return;
      }
      loadTrackingScripts.loaded = true;

      if (gaId && !window.gtag) {
        const gaScript = document.createElement('script');
        gaScript.async = true;
        gaScript.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
        document.head.appendChild(gaScript);

        gaScript.onload = () => {
          window.dataLayer = window.dataLayer || [];
          function gtag(){window.dataLayer.push(arguments);}
          window.gtag = window.gtag || gtag;
          window.gtag('js', new Date());
          window.gtag('config', gaId);
        };
      }

      if (fbId && !window.fbq) {
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        window.fbq('init', fbId);
        window.fbq('track', 'PageView');

        const fbPixel = document.createElement('img');
        fbPixel.height = 1;
        fbPixel.width = 1;
        fbPixel.className = 'is-hidden';
        fbPixel.src = `https://www.facebook.com/tr?id=${fbId}&ev=PageView&noscript=1`;
        document.body.appendChild(fbPixel);
      }
    };

    window.babixTracking = window.babixTracking || {};
    if (typeof window.babixTracking.enable !== 'function') {
      window.babixTracking.enable = loadTrackingScripts;
    }

    const consent = localStorage.getItem('cookie-consent');
    if (consent === 'accepted') {
      loadTrackingScripts();
    }
  }

  const cookieBanner = document.getElementById('cookieConsent');
  if (cookieBanner) {
    const consent = localStorage.getItem('cookie-consent');
    if (!consent) {
      setTimeout(() => {
        cookieBanner.classList.add('show');
      }, 1000);
    }

    const consentButtons = cookieBanner.querySelectorAll('[data-consent-action]');
    consentButtons.forEach((button) => {
      if (button.dataset.listenerAttached === 'true') {
        return;
      }
      button.dataset.listenerAttached = 'true';
      button.addEventListener('click', () => {
        const action = button.dataset.consentAction;
        const value = action === 'accept' ? 'accepted' : 'declined';
        localStorage.setItem('cookie-consent', value);
        cookieBanner.classList.remove('show');

        if (value === 'accepted' && window.babixTracking && typeof window.babixTracking.enable === 'function') {
          window.babixTracking.enable();
        }
      });
    });
  }

  document.querySelectorAll('a[href*="wa.me"]').forEach((link) => {
    if (link.dataset.waTrackingAttached === 'true') {
      return;
    }
    link.dataset.waTrackingAttached = 'true';
    link.addEventListener('click', () => {
      if (window.gtag) {
        window.gtag('event', 'contact_whatsapp', {
          event_category: 'Contact',
          event_label: link.href
        });
      }
      if (window.fbq) {
        window.fbq('track', 'Contact', { method: 'WhatsApp' });
      }
    });
  });

  const contactForm = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const statusMessage = document.getElementById('statusMessage');

  const showStatus = (message, type) => {
    if (!statusMessage) {
      return;
    }
    statusMessage.textContent = message;
    statusMessage.className = `status-message ${type} show`;
    statusMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    setTimeout(() => {
      statusMessage.classList.remove('show');
    }, 5000);
  };

  if (contactForm && submitBtn && window.fetch) {
    contactForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      const privacyCheckbox = document.getElementById('privacy');
      if (privacyCheckbox && !privacyCheckbox.checked) {
        showStatus('❌ Bitte akzeptiere die Datenschutzerklärung.', 'error');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Wird gesendet...';

      const formData = new FormData(contactForm);
      const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        whatsapp: formData.get('whatsapp') || null,
        message: formData.get('message'),
        privacy: Boolean(formData.get('privacy')),
        website: formData.get('website') || '',
        form_time: formData.get('form_time') || ''
      };

      try {
        const response = await fetch(contactForm.action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data)
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok && result.success) {
          showStatus('✅ Nachricht erfolgreich gesendet! Wir melden uns bald bei dir.', 'success');
          contactForm.reset();
        } else {
          throw new Error(result.message || 'Fehler beim Senden');
        }
      } catch (error) {
        console.error('Error:', error);
        showStatus('❌ Fehler beim Senden. Bitte versuche es erneut oder kontaktiere uns direkt per WhatsApp.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Nachricht senden';
      }
    });
  }

  const dialogs = document.querySelectorAll('dialog');
  if (dialogs.length > 0) {
    document.addEventListener('click', (event) => {
      const openBtn = event.target.closest('[data-open]');
      const closeBtn = event.target.closest('[data-close]');

      if (openBtn) {
        event.preventDefault();
        const dlg = document.getElementById(openBtn.dataset.open);
        if (dlg && !dlg.open) {
          dlg.showModal();
        }
      }

      if (closeBtn) {
        const dlg = closeBtn.closest('dialog');
        if (dlg) {
          dlg.close();
        }
      }
    });

    dialogs.forEach((dlg) => {
      if (dlg.dataset.outsideCloseAttached === 'true') {
        return;
      }
      dlg.dataset.outsideCloseAttached = 'true';
      dlg.addEventListener('click', (event) => {
        const rect = dlg.getBoundingClientRect();
        const inside = event.clientX >= rect.left
          && event.clientX <= rect.right
          && event.clientY >= rect.top
          && event.clientY <= rect.bottom;
        if (!inside) {
          dlg.close();
        }
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        dialogs.forEach((dlg) => {
          if (dlg.open) {
            dlg.close();
          }
        });
      }
    });

    document.addEventListener('click', (event) => {
      const pill = event.target.closest('.pill');
      const section = event.target.closest('.section-content');

      if (!pill && !section) {
        return;
      }

      const modal = (pill || section).closest('dialog');
      if (!modal) {
        return;
      }

      const sectionId = pill ? pill.dataset.section : section.id;

      modal.querySelectorAll('.pill').forEach((button) => {
        button.classList.toggle('active', button.dataset.section === sectionId);
      });

      modal.querySelectorAll('.section-content').forEach((content) => {
        content.classList.remove('active');
      });

      const activeSection = modal.querySelector(`#${sectionId}`);
      if (activeSection) {
        activeSection.classList.add('active');
        activeSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
  }

  // Fbar Slider (Freundschaftsbalken Guide)
  const fbarSliderTrack = document.getElementById('fbarSliderTrack');
  const fbarSliderPrev = document.getElementById('fbarSliderPrev');
  const fbarSliderNext = document.getElementById('fbarSliderNext');
  const fbarSliderDotsContainer = document.getElementById('fbarSliderDots');

  if (fbarSliderTrack && fbarSliderPrev && fbarSliderNext && fbarSliderDotsContainer) {
    const fbarSlides = fbarSliderTrack.querySelectorAll('.slider-item');
    let fbarCurrentIndex = 0;

    // Create dots
    fbarSlides.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.className = 'slider-dot';
      dot.setAttribute('aria-label', `Gehe zu Bild ${index + 1}`);
      if (index === 0) {
        dot.classList.add('active');
      }
      dot.addEventListener('click', () => {
        fbarGoToSlide(index);
      });
      fbarSliderDotsContainer.appendChild(dot);
    });

    const fbarDots = fbarSliderDotsContainer.querySelectorAll('.slider-dot');

    const fbarUpdateSlider = () => {
      fbarSliderTrack.style.transform = `translateX(-${fbarCurrentIndex * 100}%)`;
      fbarDots.forEach((dot, index) => {
        dot.classList.toggle('active', index === fbarCurrentIndex);
      });
    };

    const fbarGoToSlide = (index) => {
      fbarCurrentIndex = index;
      fbarUpdateSlider();
    };

    const fbarNextSlide = () => {
      fbarCurrentIndex = (fbarCurrentIndex + 1) % fbarSlides.length;
      fbarUpdateSlider();
    };

    const fbarPrevSlide = () => {
      fbarCurrentIndex = (fbarCurrentIndex - 1 + fbarSlides.length) % fbarSlides.length;
      fbarUpdateSlider();
    };

    fbarSliderNext.addEventListener('click', fbarNextSlide);
    fbarSliderPrev.addEventListener('click', fbarPrevSlide);

    // Keyboard navigation
    const fbarSliderContainer = fbarSliderTrack.closest('.slider-container');
    if (fbarSliderContainer) {
      document.addEventListener('keydown', (e) => {
        if (fbarSliderContainer.matches(':hover')) {
          if (e.key === 'ArrowLeft') {
            fbarPrevSlide();
          } else if (e.key === 'ArrowRight') {
            fbarNextSlide();
          }
        }
      });
    }
  }

  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  if (menuToggle && mobileMenu) {
    if (menuToggle.dataset.listenerAttached !== 'true') {
      menuToggle.dataset.listenerAttached = 'true';

      const closeMenu = () => {
        menuToggle.classList.remove('active');
        mobileMenu.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
        
        // Close any open dropdowns
        mobileMenu.querySelectorAll('.menu-dropdown.active').forEach((dropdown) => {
          dropdown.classList.remove('active');
          const toggle = dropdown.querySelector('.menu-dropdown-toggle');
          if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
      };

      menuToggle.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const isActive = menuToggle.classList.toggle('active');
        mobileMenu.classList.toggle('active', isActive);
        menuToggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        mobileMenu.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        
        // Reset dropdowns when closing menu
        if (!isActive) {
          mobileMenu.querySelectorAll('.menu-dropdown.active').forEach((dropdown) => {
            dropdown.classList.remove('active');
            const toggle = dropdown.querySelector('.menu-dropdown-toggle');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
          });
        }
      });

      mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
          closeMenu();
        });
      });

      mobileMenu.addEventListener('click', (event) => {
        event.stopPropagation();
      });

      document.addEventListener('click', (event) => {
        if (!menuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
          closeMenu();
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          closeMenu();
        }
      });

      // Dropdown menu toggle
      const dropdownToggles = mobileMenu.querySelectorAll('.menu-dropdown-toggle');
      dropdownToggles.forEach((toggle) => {
        if (toggle.dataset.listenerAttached === 'true') return;
        toggle.dataset.listenerAttached = 'true';
        
        const handleDropdownToggle = (event) => {
          event.preventDefault();
          event.stopPropagation();
          const dropdown = toggle.closest('.menu-dropdown');
          const isActive = dropdown.classList.toggle('active');
          toggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        };
        
        toggle.addEventListener('click', handleDropdownToggle);
        toggle.addEventListener('touchend', (event) => {
          event.preventDefault();
          handleDropdownToggle(event);
        }, { passive: false });
      });
    }
  }
})();

// ===== ENHANCED SLIDER =====
(() => {
  const initSlider = () => {
    const sliders = document.querySelectorAll('.slider-container');
    
    sliders.forEach((container) => {
      const track = container.querySelector('.slider-track');
      const prevBtn = container.querySelector('.slider-btn-prev');
      const nextBtn = container.querySelector('.slider-btn-next');
      const dotsContainer = container.querySelector('.slider-dots');

      if (!track || !prevBtn || !nextBtn) return;
      if (track.dataset.sliderInitialized === 'true') return;
      
      track.dataset.sliderInitialized = 'true';

      const slides = Array.from(track.querySelectorAll('.slider-item'));
      if (slides.length === 0) return;

      let index = 0;
      let autoplayInterval = null;
      const autoplayDelay = parseInt(container.dataset.autoplay) || 0;
      const isLoop = container.dataset.loop !== 'false';

      // Create counter
      const counter = document.createElement('div');
      counter.className = 'slider-counter';
      container.appendChild(counter);

      // Create progress bar
      const progress = document.createElement('div');
      progress.className = 'slider-progress';
      progress.innerHTML = '<div class="slider-progress-bar"></div>';
      container.appendChild(progress);
      const progressBar = progress.querySelector('.slider-progress-bar');

      // Create autoplay indicator if autoplay enabled
      let autoplayIndicator = null;
      if (autoplayDelay > 0) {
        autoplayIndicator = document.createElement('button');
        autoplayIndicator.className = 'slider-autoplay-indicator playing';
        autoplayIndicator.setAttribute('aria-label', 'Autoplay pausieren');
        container.appendChild(autoplayIndicator);
      }

      // Lazy load images
      const loadImage = (slide) => {
        const img = slide.querySelector('img[data-src]');
        if (img && !img.src) {
          img.src = img.dataset.src;
          img.onload = () => slide.classList.add('loaded');
        }
      };

      const renderDots = () => {
        if (!dotsContainer) return;
        
        dotsContainer.innerHTML = '';
        slides.forEach((_, i) => {
          const dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'slider-dot';
          dot.setAttribute('aria-label', `Bild ${i + 1} von ${slides.length}`);
          if (i === index) dot.classList.add('active');
          
          dot.addEventListener('click', () => {
            goToSlide(i);
          });
          
          dotsContainer.appendChild(dot);
        });
      };

      const updateCounter = () => {
        counter.textContent = `${index + 1} / ${slides.length}`;
      };

      const updateProgress = () => {
        const progress = ((index + 1) / slides.length) * 100;
        progressBar.style.transform = `scaleX(${progress / 100})`;
      };

      const updateButtons = () => {
        if (isLoop) {
          prevBtn.disabled = false;
          nextBtn.disabled = false;
        } else {
          prevBtn.disabled = index === 0;
          nextBtn.disabled = index === slides.length - 1;
        }
      };

      const update = () => {
        track.style.transform = `translateX(-${index * 100}%)`;
        
        if (dotsContainer) {
          const dots = dotsContainer.querySelectorAll('.slider-dot');
          dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
          });
        }
        
        updateCounter();
        updateProgress();
        updateButtons();
        
        loadImage(slides[index]);
        if (slides[index + 1]) loadImage(slides[index + 1]);
        
        const announcement = `Bild ${index + 1} von ${slides.length}`;
        announceToScreenReader(announcement);
      };

      const goToSlide = (newIndex) => {
        index = newIndex;
        update();
        resetAutoplay();
      };

      const nextSlide = () => {
        if (isLoop) {
          index = (index + 1) % slides.length;
        } else if (index < slides.length - 1) {
          index++;
        }
        update();
      };

      const prevSlide = () => {
        if (isLoop) {
          index = (index - 1 + slides.length) % slides.length;
        } else if (index > 0) {
          index--;
        }
        update();
      };

      const startAutoplay = () => {
        if (autoplayDelay > 0 && !autoplayInterval) {
          autoplayInterval = setInterval(nextSlide, autoplayDelay);
          if (autoplayIndicator) {
            autoplayIndicator.classList.remove('paused');
            autoplayIndicator.classList.add('playing');
            autoplayIndicator.setAttribute('aria-label', 'Autoplay pausieren');
          }
        }
      };

      const stopAutoplay = () => {
        if (autoplayInterval) {
          clearInterval(autoplayInterval);
          autoplayInterval = null;
          if (autoplayIndicator) {
            autoplayIndicator.classList.remove('playing');
            autoplayIndicator.classList.add('paused');
            autoplayIndicator.setAttribute('aria-label', 'Autoplay fortsetzen');
          }
        }
      };

      const resetAutoplay = () => {
        if (autoplayDelay > 0) {
          stopAutoplay();
          startAutoplay();
        }
      };

      prevBtn.addEventListener('click', () => {
        prevSlide();
        resetAutoplay();
      });

      nextBtn.addEventListener('click', () => {
        nextSlide();
        resetAutoplay();
      });

      if (autoplayIndicator) {
        autoplayIndicator.addEventListener('click', () => {
          if (autoplayInterval) {
            stopAutoplay();
          } else {
            startAutoplay();
          }
        });
      }

      container.addEventListener('mouseenter', () => {
        if (autoplayDelay > 0) stopAutoplay();
      });

      container.addEventListener('mouseleave', () => {
        if (autoplayDelay > 0 && autoplayIndicator?.classList.contains('playing')) {
          startAutoplay();
        }
      });

      let startX = null;
      let startY = null;
      let isDragging = false;

      container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        stopAutoplay();
      }, { passive: true });

      container.addEventListener('touchmove', (e) => {
        if (!isDragging || startX === null) return;
        
        const currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = Math.abs(currentX - startX);
        const diffY = Math.abs(currentY - startY);
        
        if (diffX > diffY && diffX > 10) {
          e.preventDefault();
        }
      }, { passive: false });

      container.addEventListener('touchend', (e) => {
        if (!isDragging || startX === null) return;
        
        const endX = e.changedTouches[0].clientX;
        const diff = endX - startX;
        
        if (Math.abs(diff) > 50) {
          if (diff < 0) {
            nextSlide();
          } else {
            prevSlide();
          }
        }
        
        startX = null;
        startY = null;
        isDragging = false;
        
        if (autoplayIndicator?.classList.contains('playing')) {
          startAutoplay();
        }
      });

      container.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          prevSlide();
          resetAutoplay();
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          nextSlide();
          resetAutoplay();
        } else if (e.key === 'Home') {
          e.preventDefault();
          goToSlide(0);
        } else if (e.key === 'End') {
          e.preventDefault();
          goToSlide(slides.length - 1);
        }
      });

      if (!container.hasAttribute('tabindex')) {
        container.setAttribute('tabindex', '0');
      }

      renderDots();
      update();
      loadImage(slides[0]);
      
      if (autoplayDelay > 0) {
        startAutoplay();
      }

      window.addEventListener('beforeunload', () => {
        stopAutoplay();
      });
    });
  };

  const announceToScreenReader = (message) => {
    let announcer = document.getElementById('slider-announcer');
    if (!announcer) {
      announcer = document.createElement('div');
      announcer.id = 'slider-announcer';
      announcer.className = 'visually-hidden';
      announcer.setAttribute('aria-live', 'polite');
      announcer.setAttribute('aria-atomic', 'true');
      document.body.appendChild(announcer);
    }
    announcer.textContent = message;
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSlider, { once: true });
  } else {
    initSlider();
  }
})();

// ===== FAQ ACCORDION (FIXED) =====
(() => {
  const initFaq = () => {
    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length > 0) {
      faqItems.forEach((item) => {
        const question = item.querySelector('.faq-question');
        if (!question) return;
        
        if (question.dataset.listenerAttached === 'true') return;
        question.dataset.listenerAttached = 'true';

        question.setAttribute('aria-expanded', 'false');

        question.addEventListener('click', (e) => {
          e.preventDefault();
          const isActive = item.classList.contains('active');
          
          faqItems.forEach((other) => {
            if (other !== item) {
              other.classList.remove('active');
              const otherQuestion = other.querySelector('.faq-question');
              if (otherQuestion) {
                otherQuestion.setAttribute('aria-expanded', 'false');
              }
            }
          });
          
          if (isActive) {
            item.classList.remove('active');
            question.setAttribute('aria-expanded', 'false');
          } else {
            item.classList.add('active');
            question.setAttribute('aria-expanded', 'true');
          }
        });
      });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFaq, { once: true });
  } else {
    initFaq();
  }
})();

(() => {
  const initGallery = () => {
    const galleries = document.querySelectorAll('.gallery');
    
    galleries.forEach((gallery) => {
      const mainImg = gallery.querySelector('.gallery__main-img');
      const thumbs = gallery.querySelectorAll('.gallery__thumb');
      const thumbsContainer = gallery.querySelector('.gallery__thumbs');
      const navBtns = gallery.querySelectorAll('.gallery__nav-btn');
      
      if (!mainImg || thumbs.length === 0) return;
      
      let scrollIndex = 0;
      const visibleCount = 3;
      const thumbHeight = 56;
      
      const updateScroll = () => {
        if (!thumbsContainer) return;
        const offset = scrollIndex * thumbHeight;
        thumbsContainer.style.transform = `translateY(-${offset}px)`;
      };
      
      thumbs.forEach((thumb) => {
        if (thumb.dataset.listenerAttached === 'true') return;
        thumb.dataset.listenerAttached = 'true';
        
        thumb.addEventListener('click', () => {
          const newSrc = thumb.dataset.src;
          if (!newSrc) return;
          
          mainImg.src = newSrc;
          
          const newWidth = thumb.dataset.width;
          const newHeight = thumb.dataset.height;
          if (newWidth && newHeight) {
            mainImg.setAttribute('width', newWidth);
            mainImg.setAttribute('height', newHeight);
          }
          
          thumbs.forEach((t) => t.classList.remove('gallery__thumb--active'));
          thumb.classList.add('gallery__thumb--active');
        });
      });
      
      navBtns.forEach((btn) => {
        if (btn.dataset.listenerAttached === 'true') return;
        btn.dataset.listenerAttached = 'true';
        
        btn.addEventListener('click', () => {
          const dir = btn.dataset.dir;
          const maxIndex = Math.max(0, thumbs.length - visibleCount);
          
          if (dir === 'up' && scrollIndex > 0) {
            scrollIndex--;
          } else if (dir === 'down' && scrollIndex < maxIndex) {
            scrollIndex++;
          }
          updateScroll();
        });
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGallery, { once: true });
  } else {
    initGallery();
  }

  // Service Worker Registrierung für PWA
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/public/sw.js')
        .then((registration) => {
          console.log('Service Worker registriert:', registration.scope);
        })
        .catch((error) => {
          console.log('Service Worker Registrierung fehlgeschlagen:', error);
        });
    });
  }

  // PWA Install Prompt
  let deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Optional: Zeige Custom Install Button
    const installButton = document.getElementById('pwa-install-button');
    if (installButton) {
      installButton.style.display = 'block';
      installButton.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`PWA Install: ${outcome}`);
        deferredPrompt = null;
        installButton.style.display = 'none';
      });
    }
  });

  // PWA Installation erfolgreich
  window.addEventListener('appinstalled', () => {
    console.log('PWA erfolgreich installiert');
    deferredPrompt = null;
  });

  // Online/Offline Status
  const updateOnlineStatus = () => {
    const statusIndicator = document.getElementById('online-status');
    if (statusIndicator) {
      if (navigator.onLine) {
        statusIndicator.classList.remove('offline');
        statusIndicator.classList.add('online');
      } else {
        statusIndicator.classList.remove('online');
        statusIndicator.classList.add('offline');
      }
    }
  };

  window.addEventListener('online', updateOnlineStatus);
  window.addEventListener('offline', updateOnlineStatus);
  updateOnlineStatus();
})();
