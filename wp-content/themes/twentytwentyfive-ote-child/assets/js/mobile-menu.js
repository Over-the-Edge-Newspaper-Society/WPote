/**
 * Mobile Menu JavaScript
 */
(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenu = document.getElementById('mobile-menu-sheet');
        const closeButton = mobileMenu ? mobileMenu.querySelector('[data-mm-close]') : null;
        const body = document.body;

        // Function to open menu
        function openMenu() {
            if (mobileMenu) {
                mobileMenu.setAttribute('data-open', 'true');
                body.style.overflow = 'hidden';
                
                // Trap focus
                const focusableElements = mobileMenu.querySelectorAll(
                    'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            }
        }

        // Function to close menu
        function closeMenu() {
            if (mobileMenu) {
                mobileMenu.setAttribute('data-open', 'false');
                body.style.overflow = 'auto';
            }
        }

        // Toggle menu function
        function toggleMenu() {
            const isOpen = mobileMenu && mobileMenu.getAttribute('data-open') === 'true';
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        // Override WordPress mobile menu button clicks
        document.addEventListener('click', function(e) {
            // Check if clicked element is WordPress mobile menu button
            const wpMenuButton = e.target.closest('.wp-block-navigation__responsive-container-open');
            if (wpMenuButton) {
                e.preventDefault();
                e.stopPropagation();
                openMenu();
                return;
            }

            // Check for custom menu trigger
            const customTrigger = e.target.closest('.ote-mobile-menu-trigger');
            if (customTrigger) {
                e.preventDefault();
                toggleMenu();
                return;
            }

            // Check for any button with data-menu-open attribute
            const openButton = e.target.closest('[data-menu-open]');
            if (openButton) {
                e.preventDefault();
                openMenu();
                return;
            }
        });

        // Close button click
        if (closeButton) {
            closeButton.addEventListener('click', function(e) {
                e.preventDefault();
                closeMenu();
            });
        }

        // Click outside panel to close
        if (mobileMenu) {
            mobileMenu.addEventListener('click', function(e) {
                if (e.target === mobileMenu) {
                    closeMenu();
                }
            });
        }

        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu && mobileMenu.getAttribute('data-open') === 'true') {
                closeMenu();
            }
        });

        // Handle focus trap
        if (mobileMenu) {
            mobileMenu.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && mobileMenu.getAttribute('data-open') === 'true') {
                    const focusableElements = mobileMenu.querySelectorAll(
                        'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
                    );
                    const firstElement = focusableElements[0];
                    const lastElement = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey && document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    } else if (!e.shiftKey && document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            });
        }

        // Close menu on window resize if open (optional)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768 && mobileMenu && mobileMenu.getAttribute('data-open') === 'true') {
                    closeMenu();
                }
            }, 250);
        });

        // Use the native WordPress mobile menu button; no custom trigger injection
        // The click handler above already opens our custom sheet when the WP button is clicked
    });
})();
