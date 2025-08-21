<?php
/**
 * Theme Setup and Configuration
 * 
 * Handles theme support, menus, and editor configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Navigation Menu Setup
 */
function ote_child_register_menus() {
    register_nav_menus(array(
        'primary-menu' => __('Primary Menu', 'ote-child-theme'),
        'footer-menu' => __('Footer Menu', 'ote-child-theme'),
        'social-menu' => __('Social Links Menu', 'ote-child-theme'),
    ));
}
add_action('init', 'ote_child_register_menus');

/**
 * Add Theme Support
 */
function ote_child_theme_support() {
    // Add support for block styles
    add_theme_support('wp-block-styles');
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for full and wide align images
    add_theme_support('align-wide');
    
    // Add custom image sizes
    add_image_size('ote-card-thumbnail', 400, 300, true);
    add_image_size('ote-hero-image', 1200, 600, true);
    
    // Editor styles - using enqueue instead of add_editor_style to avoid double-loading
    // Editor styles are now handled in enqueue.php via enqueue_block_assets
}
add_action('after_setup_theme', 'ote_child_theme_support');