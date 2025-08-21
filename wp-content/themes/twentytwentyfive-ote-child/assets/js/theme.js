/**
 * Over the Edge WordPress Theme JavaScript
 * Handles dark mode toggle blocks, mobile menu, and card interactions
 */

(function() {
    'use strict';
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        initializeTheme();
        initializeDarkModeToggles();
        initializeMobileMenu();
        // initializeSearch(); // Disabled - using unified mobile search system
        initializeCardInteractions();
        initializeOrganizationCards();
        initializeNewsCards();
        initializeOrganizationSearch();
        initializeAnimations();
    }
    
    // ---------------- Theme Management ----------------
    function initializeTheme() {
        const html = document.documentElement;
        
        // Get system theme preference
        function getSystemTheme() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Get stored theme preference
        function getStoredTheme() {
            return getCookie('ote_theme') || 'system';
        }
        
        // Apply theme
        function applyTheme(mode) {
            const resolved = mode === 'system' ? getSystemTheme() : mode;
            html.setAttribute('data-theme', resolved);
            html.dataset.themeMode = mode;
            
            // Save preference via AJAX
            if (typeof ote_ajax !== 'undefined') {
                fetch(ote_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'theme_toggle',
                        theme: mode,
                        nonce: ote_ajax.nonce
                    })
                }).catch(console.error);
            }
            
            // Update all toggle states
            updateAllToggles(mode);
        }
        
        // Initialize current theme - but don't override if already set correctly
        const currentTheme = getStoredTheme();
        const currentDataTheme = html.getAttribute('data-theme');
        
        // Only apply if not already set correctly by server-side script
        if (!currentDataTheme || 
            (currentTheme !== 'system' && currentDataTheme !== currentTheme) ||
            (currentTheme === 'system' && currentDataTheme !== getSystemTheme())) {
            applyTheme(currentTheme);
        } else {
            // Just update toggles without changing the theme
            updateAllToggles(currentTheme);
        }
        
        // Monitor system preference changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleChange = () => {
                if ((html.dataset.themeMode || getStoredTheme()) === 'system') {
                    applyTheme('system');
                }
            };
            
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', handleChange);
            } else {
                // Fallback for older browsers
                mediaQuery.addListener(handleChange);
            }
        }
        
        // Expose theme functions globally
        window.oteTheme = {
            applyTheme,
            getStoredTheme,
            getSystemTheme
        };
    }
    
    // ---------------- Dark Mode Toggle Blocks ----------------
    function initializeDarkModeToggles() {
        // Initialize all dark mode toggle blocks
        const toggleBlocks = document.querySelectorAll('.wp-block-ote-dark-mode-toggle');
        
        toggleBlocks.forEach(block => {
            const style = block.dataset.style || 'button';
            
            switch (style) {
                case 'segmented':
                    initializeSegmentedToggle(block);
                    break;
                case 'pill':
                    initializePillToggle(block);
                    break;
                default:
                    initializeButtonToggle(block);
                    break;
            }
        });
        
        // Use event delegation for theme toggles to avoid conflicts with dynamic content
        document.addEventListener('click', function(e) {
            // Check if clicked element is a theme toggle
            const toggle = e.target.closest('.ote-theme-toggle, .theme-toggle, #themeToggle, #darkModeToggle, #mobileThemeToggle');
            if (!toggle) return;
            
            e.preventDefault();
            e.stopPropagation(); // Prevent event bubbling
            
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const next = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Apply theme immediately
            html.setAttribute('data-theme', next);
            html.dataset.themeMode = next;
            
            // Set cookie for persistence
            document.cookie = `ote_theme=${next}; path=/; max-age=2592000`; // 30 days
            
            // Update aria-label for better accessibility
            toggle.setAttribute('aria-label', `Theme ${next}`);
            
            // Save via AJAX if available
            if (typeof ote_ajax !== 'undefined') {
                fetch(ote_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'theme_toggle',
                        theme: next,
                        nonce: ote_ajax.nonce
                    })
                }).catch(console.error);
            }
        });
        
        // Update initial states - use local functions instead of window.oteTheme
        const html = document.documentElement;
        const currentMode = html.dataset.themeMode || getCookie('ote_theme') || 'system';
        updateAllToggles(currentMode);
    }
    
    function initializeButtonToggle(block) {
        const button = block.querySelector('.ote-theme-button');
        if (!button) return;
        
        button.addEventListener('click', () => {
            const currentMode = document.documentElement.dataset.themeMode || 'system';
            const next = currentMode === 'dark' ? 'light' : 'dark';
            window.oteTheme.applyTheme(next);
        });
    }
    
    function initializePillToggle(block) {
        const toggle = block.querySelector('.ote-theme-pill__toggle');
        if (!toggle) return;
        
        toggle.addEventListener('click', () => {
            const currentMode = document.documentElement.dataset.themeMode || 'system';
            const next = currentMode === 'dark' ? 'light' : 'dark';
            window.oteTheme.applyTheme(next);
        });
    }
    
    function initializeSegmentedToggle(block) {
        const options = block.querySelectorAll('.ote-theme-segmented__option');
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const theme = option.dataset.theme;
                if (theme) {
                    window.oteTheme.applyTheme(theme);
                }
            });
        });
    }
    
    function updateAllToggles(currentMode) {
        // Get system theme directly
        function getSystemTheme() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Update header theme toggle if it exists
        const headerToggle = document.getElementById('themeToggle') || document.querySelector('.theme-toggle');
        if (headerToggle) {
            const resolved = currentMode === 'system' ? getSystemTheme() : currentMode;
            headerToggle.setAttribute('aria-label', `Theme: ${resolved}`);
        }
        
        // Update all dark mode toggle blocks
        const toggleBlocks = document.querySelectorAll('.wp-block-ote-dark-mode-toggle');
        
        toggleBlocks.forEach(block => {
            const style = block.dataset.style || 'button';
            
            if (style === 'segmented') {
                // Update segmented control
                const options = block.querySelectorAll('.ote-theme-segmented__option');
                options.forEach(option => {
                    const isActive = option.dataset.theme === currentMode;
                    option.setAttribute('aria-checked', String(isActive));
                });
            } else if (style === 'pill') {
                // Update pill toggle
                const pillContainer = block.querySelector('.ote-theme-pill');
                if (pillContainer) {
                    const resolved = currentMode === 'system' ? getSystemTheme() : currentMode;
                    const isChecked = resolved === 'dark';
                    pillContainer.setAttribute('aria-checked', String(isChecked));
                }
            }
        });
    }
    
    // ---------------- Organization Cards ----------------
    function initializeOrganizationCards() {
        // Make organization cards clickable
        const orgCards = document.querySelectorAll('.ote-organization-grid .wp-block-post-template > li');
        
        orgCards.forEach(card => {
            // Find the "View Details" button link
            const detailsButton = card.querySelector('.wp-block-button__link');
            const titleLink = card.querySelector('.wp-block-post-title a');
            
            if (titleLink || detailsButton) {
                // Prioritize title link URL, fallback to button URL
                const targetUrl = titleLink ? titleLink.href : (detailsButton ? detailsButton.href : null);
                
                if (targetUrl) {
                    // Make entire card clickable
                    card.addEventListener('click', (e) => {
                        // Prevent default behavior
                        e.preventDefault();
                        
                        // Navigate to the organization page (same as title link)
                        window.location.href = targetUrl;
                    });
                    
                    // Add hover effect indication
                    card.style.cursor = 'pointer';
                    
                    // Sync button URL to match title link URL
                    if (detailsButton && titleLink) {
                        detailsButton.href = titleLink.href;
                    }
                }
            }
        });
    }
    
    // ---------------- News Cards ----------------
    function initializeNewsCards() {
        // Make news cards clickable
        const newsCards = document.querySelectorAll('.ote-news-grid .wp-block-post-template > li');
        
        newsCards.forEach(card => {
            // Find the title link and button
            const titleLink = card.querySelector('.wp-block-post-title a');
            const readButton = card.querySelector('.wp-block-button__link');
            
            if (titleLink) {
                const targetUrl = titleLink.href;
                
                if (targetUrl) {
                    // Make entire card clickable
                    card.addEventListener('click', (e) => {
                        // Don't navigate if clicking on interactive elements
                        if (e.target.closest('a, button')) {
                            return;
                        }
                        
                        // Prevent default behavior
                        e.preventDefault();
                        
                        // Navigate to the post page
                        window.location.href = targetUrl;
                    });
                    
                    // Make the Read button link to the same URL as the title
                    if (readButton) {
                        readButton.href = targetUrl;
                    }
                    
                    // Card is already styled as clickable in CSS
                }
            }
        });
    }
    
    // ---------------- Organization Search Filter ----------------
    function initializeOrganizationSearch() {
        // Handle existing search inputs (from shortcode)
        const searchInputs = document.querySelectorAll('.ote-org-search-input');
        
        searchInputs.forEach(searchInput => {
            setupSearchFunctionality(searchInput);
        });
        
        // Auto-inject search if enabled (optional - can be disabled)
        const autoInject = false; // Set to true if you want automatic search bar injection
        if (autoInject) {
            const orgGrid = document.querySelector('.ote-organization-grid');
            if (orgGrid && !orgGrid.parentNode.querySelector('.ote-org-search-wrapper')) {
                injectSearchBar(orgGrid);
            }
        }
    }
    
    function setupSearchFunctionality(searchInput) {
        // Get target grid selector from data attribute
        const targetSelector = searchInput.dataset.target || '.ote-organization-grid';
        const showCount = searchInput.dataset.showCount !== 'false';
        const searchCount = searchInput.parentElement.querySelector('.ote-org-search-count');
        
        // Find the target grid
        const targetGrid = document.querySelector(targetSelector);
        if (!targetGrid) return;
        
        const orgCards = targetGrid.querySelectorAll('.wp-block-post-template > li');
        
        // Search function
        function filterOrganizations() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            orgCards.forEach(card => {
                // Get searchable content
                const title = card.querySelector('.wp-block-post-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.organization-field-content')?.textContent.toLowerCase() || '';
                
                // Check if search term matches
                if (searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update count
            if (searchCount && showCount) {
                if (searchTerm) {
                    searchCount.textContent = `${visibleCount} of ${orgCards.length} organizations`;
                } else {
                    searchCount.textContent = '';
                }
            }
            
            // Show no results message
            let noResultsMsg = targetGrid.querySelector('.ote-no-results');
            if (visibleCount === 0 && searchTerm) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'ote-no-results';
                    noResultsMsg.innerHTML = `
                        <p>No organizations found matching "${searchTerm}"</p>
                        <p class="ote-no-results-hint">Try adjusting your search terms</p>
                    `;
                    targetGrid.appendChild(noResultsMsg);
                } else {
                    noResultsMsg.querySelector('p').textContent = `No organizations found matching "${searchTerm}"`;
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
        
        // Add event listeners
        searchInput.addEventListener('input', filterOrganizations);
        
        // Clear button functionality
        searchInput.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    }
    
    function injectSearchBar(orgGrid) {
        // Create search bar
        const searchWrapper = document.createElement('div');
        searchWrapper.className = 'ote-org-search-wrapper';
        searchWrapper.innerHTML = `
            <div class="ote-org-search-container">
                <div class="ote-org-search-field">
                    <svg class="ote-org-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" 
                           class="ote-org-search-input" 
                           placeholder="Search organizations..." 
                           aria-label="Search organizations"
                           data-target=".ote-organization-grid"
                           data-show-count="true">
                    <span class="ote-org-search-count"></span>
                </div>
            </div>
        `;
        
        // Insert search bar before the grid
        orgGrid.parentNode.insertBefore(searchWrapper, orgGrid);
        
        // Setup functionality for the new input
        const searchInput = searchWrapper.querySelector('.ote-org-search-input');
        setupSearchFunctionality(searchInput);
    }
    
    // ---------------- Mobile Menu ----------------
    function initializeMobileMenu() {
        const menuToggle = document.getElementById('mobile-menu-toggle') || document.querySelector('.app-bar__menu');
        let mobileNav = document.getElementById('mobile-nav');
        
        if (!menuToggle) return;
        
        // If no mobile nav exists, create a simple one
        if (!mobileNav) {
            mobileNav = createMobileNav();
        }
        
        // Toggle menu
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileNav.hasAttribute('data-open');
            
            if (isOpen) {
                mobileNav.removeAttribute('data-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            } else {
                mobileNav.setAttribute('data-open', 'true');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuToggle.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.removeAttribute('data-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileNav.hasAttribute('data-open')) {
                mobileNav.removeAttribute('data-open');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
        });
    }
    
    function createMobileNav() {
        const nav = document.createElement('nav');
        nav.id = 'mobile-nav';
        nav.className = 'mobile-nav';
        nav.setAttribute('aria-label', 'Mobile navigation');
        
        // Get navigation items from main menu
        const mainNav = document.querySelector('nav[role="navigation"], .primary-menu, .main-navigation');
        const links = mainNav ? Array.from(mainNav.querySelectorAll('a')).map(link => ({
            href: link.href,
            text: link.textContent.trim()
        })) : [
            { href: '/', text: 'Home' },
            { href: '/news', text: 'News' },
            { href: '/clubs', text: 'Clubs' },
            { href: '/events', text: 'Events' }
        ];
        
        nav.innerHTML = links.map(link => 
            `<a href="${link.href}" class="mobile-nav__link">${link.text}</a>`
        ).join('');
        
        // Insert after header or at beginning of body
        const header = document.querySelector('header') || document.body.firstChild;
        if (header && header.nextSibling) {
            header.parentNode.insertBefore(nav, header.nextSibling);
        } else {
            document.body.appendChild(nav);
        }
        
        return nav;
    }
    
    // ---------------- Search ----------------
    function initializeSearch() {
        // Search is now handled by the mobile search system which works on desktop too
        // No additional initialization needed
        return;
    }
    
    // ---------------- Card Interactions ----------------
    function initializeCardInteractions() {
        // Add click handlers for animated cards
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.is-style-ote-card-animated, .card--animated');
            if (card && !e.target.closest('a, button')) {
                // Find the first link in the card and navigate to it
                const link = card.querySelector('a');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
        
        // Add keyboard navigation for cards
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const card = e.target.closest('.is-style-ote-card-animated, .card--animated');
                if (card && !e.target.closest('a, button')) {
                    e.preventDefault();
                    const link = card.querySelector('a');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            }
        });
    }
    
    // ---------------- Animations & Effects ----------------
    function initializeAnimations() {
        // Add intersection observer for fade-in animations
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Observe cards and sections
            document.querySelectorAll('.is-style-ote-card, .is-style-ote-card-animated, .is-style-ote-section').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        }
        
        // Add cursor tracking for hero sections
        const heroes = document.querySelectorAll('.is-style-ote-hero, .hero');
        heroes.forEach(hero => {
            // Create cursor glow element if it doesn't exist
            let glow = hero.querySelector('.hero__cursor-glow');
            if (!glow) {
                glow = document.createElement('div');
                glow.className = 'hero__cursor-glow';
                glow.style.cssText = `
                    position: absolute;
                    top: var(--gy, 50%);
                    left: var(--gx, 50%);
                    width: 300px;
                    height: 300px;
                    background: radial-gradient(circle, rgba(77, 150, 255, 0.15) 0%, transparent 70%);
                    border-radius: 50%;
                    pointer-events: none;
                    transform: translate(-50%, -50%);
                    transition: opacity 0.3s ease;
                    opacity: 0;
                `;
                hero.style.position = 'relative';
                hero.appendChild(glow);
            }
            
            hero.addEventListener('mouseenter', () => {
                glow.style.opacity = '1';
            });
            
            hero.addEventListener('mouseleave', () => {
                glow.style.opacity = '0';
            });
            
            hero.addEventListener('mousemove', (e) => {
                const rect = hero.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                
                glow.style.setProperty('--gx', `${x}%`);
                glow.style.setProperty('--gy', `${y}%`);
            });
        });
    }
    
    // ---------------- Utility Functions ----------------
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `ote-notification ote-notification--${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 4px solid var(--${type === 'success' ? 'success' : 'danger'}, #35C291);
            border-radius: var(--radius-s);
            padding: 1rem;
            box-shadow: var(--elev-2);
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 300px;
            color: var(--text);
        `;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        });
        
        // Auto remove
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Make utility functions globally available
    window.oteUtils = {
        showNotification,
        getCookie
    };
    
})();