/**
 * OTE App Bar Navigation JavaScript
 * Handles mobile menu toggle and theme switching for WordPress blocks
 */

class AppBarNavigation {
  constructor() {
    console.log('AppBarNavigation constructor called');
    this.mobileMenuOpen = false;
    this.init();
  }

  init() {
    console.log('AppBarNavigation init() called');
    this.cleanupMobileButtons();
    this.createMobileMenu();
    this.createBottomNavigation();
    this.bindEvents();
    this.initTheme();
    console.log('AppBarNavigation init() completed');
  }

  cleanupMobileButtons() {
    // Completely remove the duplicate custom button from the DOM
    const removeCustomButton = () => {
      const customButton = document.querySelector('.ote-mobile-menu-trigger');
      if (customButton) {
        customButton.remove();
        console.log('Removed custom mobile menu button from DOM');
      }
    };
    
    // Remove it immediately
    removeCustomButton();
    
    // Also watch for it being added dynamically and remove it
    const observer = new MutationObserver(() => {
      removeCustomButton();
    });
    
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
    
    // Force show the WordPress button on mobile
    const wpButton = document.querySelector('.wp-block-navigation__responsive-container-open');
    if (wpButton) {
      // Remove inline display: none and let CSS take over
      wpButton.removeAttribute('style');
      console.log('Cleaned WordPress mobile menu button styles');
    }
  }

  createMobileMenu() {
    // Mobile menu logo will be handled by PHP function in functions.php
    // This function is kept for any additional mobile menu functionality
    return;
  }

  createBottomNavigation() {
    // Don't create if already exists
    if (document.querySelector('.bottom-nav')) {
      console.log('Bottom nav already exists');
      return;
    }

    console.log('Creating bottom navigation...');

    // Define our bottom nav items (Home, News, Clubs, Events)
    const bottomNavItems = [
      {
        href: '/',
        label: 'Home',
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                 <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                 <polyline points="9,22 9,12 15,12 15,22"></polyline>
               </svg>`
      },
      {
        href: '/news/',
        label: 'News',
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                 <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                 <path d="M18 14h-8"></path>
                 <path d="M15 18h-5"></path>
                 <path d="M10 6h8v4h-8z"></path>
               </svg>`
      },
      {
        href: '/clubs',
        label: 'Clubs',
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                 <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                 <circle cx="9" cy="7" r="4"></circle>
                 <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                 <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
               </svg>`
      },
      {
        href: '/calendar',
        label: 'Events', 
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                 <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                 <path d="M16 2v4M8 2v4M3 10h18"></path>
               </svg>`
      }
    ];

    // Create bottom navigation
    const bottomNav = document.createElement('nav');
    bottomNav.id = 'mobile-nav';
    bottomNav.className = 'bottom-nav';
    bottomNav.setAttribute('aria-label', 'Bottom navigation');

    // Generate navigation items
    const currentPath = window.location.pathname;
    console.log('Current path:', currentPath);
    
    const navItemsHTML = bottomNavItems.map(item => {
      const isActive = currentPath === item.href || 
                      (currentPath === '/' && item.href === '/') ||
                      (currentPath.includes(item.href.replace('/', '')) && item.href !== '/');
      
      console.log(`Item ${item.label}: href=${item.href}, isActive=${isActive}`);
      
      return `
        <a class="bottom-nav__link ${isActive ? 'is-active' : ''}" href="${item.href}">
          <span class="icon" aria-hidden="true">
            ${item.icon}
          </span>
          <span>${item.label}</span>
        </a>
      `;
    }).join('');

    bottomNav.innerHTML = navItemsHTML;

    // Append to body
    document.body.appendChild(bottomNav);
    console.log('Bottom navigation appended to body');

    // Add bottom padding to body to account for fixed bottom nav on mobile
    this.addBottomNavPadding();
  }

  addBottomNavPadding() {
    // Add bottom padding on mobile to prevent content being hidden behind bottom nav
    // Only add if not already added to prevent duplicates
    if (!document.getElementById('ote-bottom-nav-padding')) {
      const style = document.createElement('style');
      style.id = 'ote-bottom-nav-padding';
      style.textContent = `
        @media (max-width: 899px) {
          body {
            padding-bottom: 70px; /* Height of bottom nav + safe area */
          }
        }
      `;
      document.head.appendChild(style);
    }
  }

  bindEvents() {
    // Let WordPress handle the mobile menu - we don't need to interfere
    console.log('WordPress mobile menu will handle hamburger functionality');
    
    // We only handle our bottom navigation active states
    this.updateBottomNavActiveStates();

    // Theme toggle - works with your existing dark mode shortcode (desktop only)
    const themeToggles = document.querySelectorAll('[class*="dark-mode"]');
    themeToggles.forEach(toggle => {
      toggle.addEventListener('click', () => this.toggleTheme());
    });
  }

  updateBottomNavActiveStates() {
    // Update active states for bottom navigation
    const currentPath = window.location.pathname;
    const bottomNavLinks = document.querySelectorAll('.bottom-nav__link');
    
    bottomNavLinks.forEach(link => {
      link.classList.remove('is-active');
      
      const linkPath = new URL(link.href).pathname;
      if (linkPath === currentPath || 
          (currentPath === '/' && linkPath === '/') ||
          (currentPath.includes(linkPath.replace('/', '')) && linkPath !== '/')) {
        link.classList.add('is-active');
      }
    });
  }

  initTheme() {
    // Don't override theme - let the main theme system handle it
    // Just update UI elements if needed
    this.updateThemeUI();
  }

  toggleTheme() {
    // Delegate to main theme system instead of handling ourselves
    const toggleEvent = new CustomEvent('theme-toggle-request');
    document.dispatchEvent(toggleEvent);
  }

  updateThemeUI() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    
    // Update theme toggle aria-label
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
      const label = currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode';
      themeToggle.setAttribute('aria-label', label);
    }
  }

  // Deprecated - use main theme system instead
  setTheme(theme) {
    console.warn('AppBarNavigation.setTheme() is deprecated - use main theme system');
  }

}

// Initialize only once when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new AppBarNavigation();
  });
} else {
  new AppBarNavigation();
}