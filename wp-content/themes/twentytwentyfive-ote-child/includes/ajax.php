<?php
/**
 * AJAX Handlers
 * 
 * Handles all AJAX requests for the theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Theme Toggle AJAX Request
 */
function ote_child_ajax_theme_toggle() {
    if (!isset($_POST['theme'])) {
        wp_die('Invalid theme');
    }
    
    $theme = sanitize_text_field($_POST['theme']);
    $allowed_themes = array('light', 'dark', 'system');
    
    if (!in_array($theme, $allowed_themes)) {
        wp_die('Invalid theme option');
    }
    
    // Set cookie for 30 days
    setcookie('ote_theme', $theme, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
    
    wp_die(); // Proper way to terminate AJAX requests
}
add_action('wp_ajax_theme_toggle', 'ote_child_ajax_theme_toggle');
add_action('wp_ajax_nopriv_theme_toggle', 'ote_child_ajax_theme_toggle');