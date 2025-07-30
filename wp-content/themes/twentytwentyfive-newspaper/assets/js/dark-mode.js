/**
 * Dark Mode Toggle Functionality
 * Uses shadcn/ui approach with CSS classes
 */

(function() {
    'use strict';
    
    // Initialize dark mode
    function initDarkMode() {
        const themeToggle = document.getElementById('theme-toggle');
        const lightIcon = document.querySelector('.theme-icon-light');
        const darkIcon = document.querySelector('.theme-icon-dark');
        const body = document.body;
        
        if (!themeToggle) {
            console.warn('Dark mode toggle button not found');
            return;
        }
        
        // Check for saved theme preference or default to light mode
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
        
        // Apply initial theme
        if (shouldUseDark) {
            enableDarkMode();
        } else {
            enableLightMode();
        }
        
        // Add click event listener
        themeToggle.addEventListener('click', toggleTheme);
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    enableDarkMode();
                } else {
                    enableLightMode();
                }
            }
        });
        
        function toggleTheme() {
            if (body.classList.contains('dark')) {
                enableLightMode();
                localStorage.setItem('theme', 'light');
            } else {
                enableDarkMode();
                localStorage.setItem('theme', 'dark');
            }
        }
        
        function enableDarkMode() {
            body.classList.add('dark');
            updateIcons(true);
            
            // Dispatch custom event for other components
            window.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme: 'dark' } 
            }));
        }
        
        function enableLightMode() {
            body.classList.remove('dark');
            updateIcons(false);
            
            // Dispatch custom event for other components
            window.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme: 'light' } 
            }));
        }
        
        function updateIcons(isDark) {
            if (lightIcon && darkIcon) {
                if (isDark) {
                    lightIcon.style.display = 'inline';
                    darkIcon.style.display = 'none';
                } else {
                    lightIcon.style.display = 'none';
                    darkIcon.style.display = 'inline';
                }
            }
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDarkMode);
    } else {
        initDarkMode();
    }
    
    // Keyboard accessibility
    document.addEventListener('keydown', function(e) {
        // Alt + D to toggle dark mode
        if (e.altKey && e.key === 'd') {
            e.preventDefault();
            const toggleButton = document.getElementById('theme-toggle');
            if (toggleButton) {
                toggleButton.click();
            }
        }
    });
    
})();