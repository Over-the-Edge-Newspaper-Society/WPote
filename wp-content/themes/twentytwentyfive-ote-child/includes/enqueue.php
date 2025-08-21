<?php
/**
 * Enqueue Scripts and Styles
 * 
 * Handles all style and script enqueuing for the theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles for frontend
 */
function ote_child_enqueue_styles() {
    $template_dir = get_template_directory();
    $stylesheet_dir = get_stylesheet_directory();
    $template_uri = get_template_directory_uri();
    $stylesheet_uri = get_stylesheet_directory_uri();
    
    // Parent theme stylesheet
    wp_enqueue_style(
        'twentytwentyfive-style',
        $template_uri . '/style.css',
        array(),
        filemtime($template_dir . '/style.css')
    );
    
    // Child theme stylesheet (without imports to avoid double-loading)
    wp_enqueue_style(
        'ote-child-style',
        $stylesheet_uri . '/style.css',
        array('twentytwentyfive-style'),
        filemtime($stylesheet_dir . '/style.css')
    );
    
    // Design system CSS
    wp_enqueue_style(
        'ote-design-system',
        $stylesheet_uri . '/assets/css/design-system.css',
        array('ote-child-style'),
        filemtime($stylesheet_dir . '/assets/css/design-system.css')
    );
    
    // Card grid styles
    wp_enqueue_style(
        'ote-card-grid',
        $stylesheet_uri . '/assets/css/card-grid.css',
        array('ote-design-system'),
        filemtime($stylesheet_dir . '/assets/css/card-grid.css')
    );
    
    // Button animations
    wp_enqueue_style(
        'ote-button-animations',
        $stylesheet_uri . '/assets/css/button-animations.css',
        array('ote-card-grid'),
        filemtime($stylesheet_dir . '/assets/css/button-animations.css')
    );
    
    // Chips and tabs components
    wp_enqueue_style(
        'ote-chips-tabs',
        $stylesheet_uri . '/assets/css/chips-tabs.css',
        array('ote-button-animations'),
        filemtime($stylesheet_dir . '/assets/css/chips-tabs.css')
    );
    
    // News grid styling
    wp_enqueue_style(
        'ote-news-grid',
        $stylesheet_uri . '/assets/css/news-grid.css',
        array('ote-chips-tabs'),
        filemtime($stylesheet_dir . '/assets/css/news-grid.css')
    );
    
    // Organization grid styles (moved from inline)
    wp_enqueue_style(
        'ote-organization-grid',
        $stylesheet_uri . '/assets/css/organization-grid.css',
        array('ote-design-system'),
        filemtime($stylesheet_dir . '/assets/css/organization-grid.css')
    );
    
    // Staff profile styles
    wp_enqueue_style(
        'ote-staff-profiles',
        $stylesheet_uri . '/assets/css/staff-profiles.css',
        array('ote-design-system'),
        filemtime($stylesheet_dir . '/assets/css/staff-profiles.css')
    );
}
add_action('wp_enqueue_scripts', 'ote_child_enqueue_styles');

/**
 * Enqueue theme JavaScript with file existence checks
 */
function ote_child_enqueue_scripts() {
    $stylesheet_dir = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();
    
    // Theme JavaScript
    $theme_js = $stylesheet_dir . '/assets/js/theme.js';
    if (file_exists($theme_js)) {
        wp_enqueue_script(
            'ote-theme-js',
            $stylesheet_uri . '/assets/js/theme.js',
            array(),
            filemtime($theme_js),
            true
        );
    }
    
    // Dark mode toggle JavaScript
    $dark_mode_js = $stylesheet_dir . '/assets/js/dark-mode-toggle.js';
    if (file_exists($dark_mode_js)) {
        wp_enqueue_script(
            'ote-dark-mode',
            $stylesheet_uri . '/assets/js/dark-mode-toggle.js',
            array('jquery'),
            filemtime($dark_mode_js),
            true
        );
    }
    
    // Club cards conditional display JavaScript (only on organization pages)
    if (is_singular('organization')) {
        $club_cards_js = $stylesheet_dir . '/assets/js/club-cards.js';
        if (file_exists($club_cards_js)) {
            wp_enqueue_script(
                'ote-club-cards',
                $stylesheet_uri . '/assets/js/club-cards.js',
                array(),
                filemtime($club_cards_js),
                false // Load in head for faster execution
            );
        }
    }
    
    // Category filters script is loaded by shortcode when needed
    
    // Mobile menu script
    $mobile_menu_js = $stylesheet_dir . '/assets/js/mobile-menu.js';
    if (file_exists($mobile_menu_js)) {
        wp_enqueue_script(
            'ote-mobile-menu',
            $stylesheet_uri . '/assets/js/mobile-menu.js',
            array(),
            filemtime($mobile_menu_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'ote_child_enqueue_scripts');

/**
 * Enqueue block editor assets
 * Only loads editor-specific styles in the admin area
 */
function ote_child_enqueue_block_assets() {
    // Only load in admin/editor context
    if (is_admin()) {
        $stylesheet_dir = get_stylesheet_directory();
        $stylesheet_uri = get_stylesheet_directory_uri();
        
        // Editor-specific styles only
        wp_enqueue_style(
            'ote-editor-styles',
            $stylesheet_uri . '/assets/css/editor-style.css',
            array('wp-edit-blocks'),
            filemtime($stylesheet_dir . '/assets/css/editor-style.css')
        );
    }
}
add_action('enqueue_block_assets', 'ote_child_enqueue_block_assets');

/**
 * Enqueue app bar navigation assets
 */
function ote_enqueue_app_bar_assets() {
    $stylesheet_dir = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();
    
    // App bar navigation styles
    $app_bar_css = $stylesheet_dir . '/assets/css/app-bar-nav.css';
    if (file_exists($app_bar_css)) {
        wp_enqueue_style(
            'ote-app-bar-nav',
            $stylesheet_uri . '/assets/css/app-bar-nav.css',
            array('ote-design-system'),
            filemtime($app_bar_css)
        );
    }
    
    // App bar navigation script
    $app_bar_js = $stylesheet_dir . '/assets/js/app-bar-nav.js';
    if (file_exists($app_bar_js)) {
        wp_enqueue_script(
            'ote-app-bar-nav',
            $stylesheet_uri . '/assets/js/app-bar-nav.js',
            array(),
            filemtime($app_bar_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'ote_enqueue_app_bar_assets');