<?php
/**
 * Debug script to verify styles are loading
 * Add to functions.php temporarily: require_once get_stylesheet_directory() . '/debug-styles.php';
 */

add_action('admin_footer', 'ote_debug_editor_styles');
add_action('wp_footer', 'ote_debug_frontend_styles');

function ote_debug_editor_styles() {
    if (!is_admin()) return;
    ?>
    <script>
        console.log('=== OTE Editor Styles Debug ===');
        
        // Check for editor wrapper
        const editorWrapper = document.querySelector('.editor-styles-wrapper');
        if (editorWrapper) {
            console.log('Editor wrapper found');
            const computedStyles = window.getComputedStyle(editorWrapper);
            console.log('Background:', computedStyles.backgroundColor);
            console.log('Font Family:', computedStyles.fontFamily);
        } else {
            console.log('Editor wrapper not found');
        }
        
        // Check for loaded stylesheets
        const stylesheets = Array.from(document.styleSheets);
        const oteStyles = stylesheets.filter(sheet => {
            try {
                return sheet.href && sheet.href.includes('twentytwentyfive-ote-child');
            } catch(e) {
                return false;
            }
        });
        
        console.log('OTE Stylesheets loaded:', oteStyles.length);
        oteStyles.forEach(sheet => {
            console.log('- ' + sheet.href);
        });
        
        // Check for custom properties
        const root = document.documentElement;
        const rootStyles = window.getComputedStyle(root);
        console.log('CSS Variables:');
        console.log('--brand:', rootStyles.getPropertyValue('--brand'));
        console.log('--font-ui:', rootStyles.getPropertyValue('--font-ui'));
        console.log('--font-serif:', rootStyles.getPropertyValue('--font-serif'));
    </script>
    <?php
}

function ote_debug_frontend_styles() {
    ?>
    <script>
        console.log('=== OTE Frontend Styles Debug ===');
        
        // Check for loaded stylesheets
        const stylesheets = Array.from(document.styleSheets);
        const oteStyles = stylesheets.filter(sheet => {
            try {
                return sheet.href && sheet.href.includes('twentytwentyfive-ote-child');
            } catch(e) {
                return false;
            }
        });
        
        console.log('OTE Stylesheets loaded:', oteStyles.length);
        oteStyles.forEach(sheet => {
            console.log('- ' + sheet.href);
        });
        
        // Check for custom properties
        const root = document.documentElement;
        const rootStyles = window.getComputedStyle(root);
        console.log('CSS Variables:');
        console.log('--brand:', rootStyles.getPropertyValue('--brand'));
        console.log('--font-ui:', rootStyles.getPropertyValue('--font-ui'));
        console.log('--font-serif:', rootStyles.getPropertyValue('--font-serif'));
        
        // Check for OTE styled elements
        const oteCards = document.querySelectorAll('.is-style-ote-card, .is-style-ote-card-animated');
        console.log('OTE Card elements found:', oteCards.length);
        
        const oteButtons = document.querySelectorAll('.is-style-ote-primary, .is-style-ote-outline');
        console.log('OTE Button elements found:', oteButtons.length);
    </script>
    <?php
}