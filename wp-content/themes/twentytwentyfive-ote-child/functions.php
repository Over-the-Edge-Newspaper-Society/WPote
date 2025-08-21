<?php
/**
 * Over the Edge - Twenty Twenty-Five Child Theme Functions
 * Based on the New Implementation design system
 * 
 * Refactored into organized modules for better maintainability
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('OTE_THEME_VERSION', wp_get_theme()->get('Version'));
define('OTE_THEME_PATH', get_stylesheet_directory());
define('OTE_THEME_URI', get_stylesheet_directory_uri());

/**
 * Load theme modules
 * All functionality has been organized into separate files
 */

// Core functionality
require_once OTE_THEME_PATH . '/includes/enqueue.php';        // Asset enqueuing
require_once OTE_THEME_PATH . '/includes/theme-setup.php';    // Theme setup and support
require_once OTE_THEME_PATH . '/includes/blocks.php';         // Block customizations
require_once OTE_THEME_PATH . '/includes/shortcodes.php';     // Theme shortcodes
require_once OTE_THEME_PATH . '/includes/ajax.php';          // AJAX handlers
require_once OTE_THEME_PATH . '/includes/mobile-menu.php';   // Mobile menu functionality
require_once OTE_THEME_PATH . '/includes/search.php'; // Search functionality (mobile + desktop)
require_once OTE_THEME_PATH . '/includes/inline-styles.php'; // Inline styles
require_once OTE_THEME_PATH . '/includes/admin.php';         // Admin functionality
require_once OTE_THEME_PATH . '/includes/staff-functions.php'; // Staff profile functionality

/**
 * Theme initialization
 */
function ote_theme_init() {
    // Theme is now fully loaded and organized
    // Individual modules handle their own initialization
}
add_action('init', 'ote_theme_init');

/**
 * Add theme preference as inline script for immediate application
 */
function ote_theme_inline_init() {
    ?>
    <script>
    (function() {
        // Get stored theme preference immediately
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        
        function getSystemTheme() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Apply theme immediately
        const storedTheme = getCookie('ote_theme') || 'light';
        const resolvedTheme = storedTheme === 'system' ? getSystemTheme() : storedTheme;
        
        document.documentElement.setAttribute('data-theme', resolvedTheme);
        document.documentElement.dataset.themeMode = storedTheme;
    })();
    </script>
    <?php
}
add_action('wp_head', 'ote_theme_inline_init', 0);