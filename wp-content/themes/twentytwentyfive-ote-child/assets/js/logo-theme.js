/**
 * Logo theme switcher for SVG text colors
 * Updates OTE logo text to match dark/light theme
 */

// Logo theme updater function
function updateOTELogoColors() {
    const theme = document.documentElement.getAttribute('data-theme');
    const logoTexts = document.querySelectorAll('.ote-logo .logo-text path[fill="currentColor"]');
    
    // Color for the small text based on theme
    const textColor = theme === 'dark' ? 'hsl(210, 40%, 98%)' : '#282828';
    
    logoTexts.forEach(path => {
        path.setAttribute('fill', textColor);
    });
    
    // Also update CSS custom property for currentColor
    document.documentElement.style.setProperty('--ote-logo-text-color', textColor);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    updateOTELogoColors();
});

// Watch for theme changes using MutationObserver
const logoObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
            updateOTELogoColors();
        }
    });
});

// Start observing theme changes on document element
logoObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-theme']
});

// Also listen for custom theme change events
window.addEventListener('themechange', function(event) {
    updateOTELogoColors();
});

// Run immediately in case the script loads after theme is set
updateOTELogoColors();