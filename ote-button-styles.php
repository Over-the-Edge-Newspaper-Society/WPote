<?php
/**
 * Register custom button block styles for OTE theme
 * Add this to your theme's functions.php file
 */

function register_ote_button_styles() {
    // Register OTE Primary button style
    register_block_style(
        'core/button',
        array(
            'name'  => 'ote-primary',
            'label' => 'OTE Primary',
        )
    );
    
    // Register OTE Ghost button style
    register_block_style(
        'core/button',
        array(
            'name'  => 'ote-ghost',
            'label' => 'OTE Ghost',
        )
    );
    
    // Register OTE Outline button style
    register_block_style(
        'core/button',
        array(
            'name'  => 'ote-outline',
            'label' => 'OTE Outline',
        )
    );
}
add_action('init', 'register_ote_button_styles');

// Enqueue the custom CSS for the button styles
function enqueue_ote_button_styles() {
    wp_enqueue_style(
        'ote-button-styles',
        get_template_directory_uri() . '/ote-buttons.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_ote_button_styles');
add_action('enqueue_block_editor_assets', 'enqueue_ote_button_styles');