<?php
/**
 * Block Editor Customizations
 * 
 * Handles block categories, styles, and colors
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Block Categories
 */
function ote_child_block_categories($categories, $post) {
    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'ote-blocks',
                'title' => __('Over the Edge Blocks', 'ote-child-theme'),
                'icon'  => 'admin-appearance',
            ),
        )
    );
}
add_filter('block_categories_all', 'ote_child_block_categories', 10, 2);

/**
 * Add Custom Block Styles
 */
function ote_child_block_styles() {
    // Card styles for Query Loop and Group blocks
    register_block_style('core/group', array(
        'name'  => 'ote-card',
        'label' => __('OTE Card', 'ote-child-theme'),
    ));
    
    register_block_style('core/group', array(
        'name'  => 'ote-card-animated',
        'label' => __('OTE Card with Hover Animation', 'ote-child-theme'),
    ));
    
    register_block_style('core/group', array(
        'name'  => 'ote-card-compact',
        'label' => __('OTE Card Compact', 'ote-child-theme'),
    ));
    
    // Card grid styles for Query Loop
    register_block_style('core/query', array(
        'name'  => 'ote-card-grid',
        'label' => __('OTE Card Grid', 'ote-child-theme'),
    ));
    
    register_block_style('core/query', array(
        'name'  => 'ote-card-grid-animated',
        'label' => __('OTE Card Grid with Animation', 'ote-child-theme'),
    ));
    
    register_block_style('core/query', array(
        'name'  => 'ote-card-grid-compact',
        'label' => __('OTE Card Grid Compact', 'ote-child-theme'),
    ));
    
    // Hero styles
    register_block_style('core/group', array(
        'name'  => 'ote-hero',
        'label' => __('OTE Hero Section', 'ote-child-theme'),
    ));
    
    // Section styles
    register_block_style('core/group', array(
        'name'  => 'ote-section',
        'label' => __('OTE Section', 'ote-child-theme'),
    ));
    
    // Button styles
    register_block_style('core/button', array(
        'name'  => 'ote-primary',
        'label' => __('OTE Primary', 'ote-child-theme'),
    ));
    
    register_block_style('core/button', array(
        'name'  => 'ote-outline',
        'label' => __('OTE Outline', 'ote-child-theme'),
    ));
    
    register_block_style('core/button', array(
        'name'  => 'ote-ghost',
        'label' => __('OTE Ghost', 'ote-child-theme'),
    ));
    
    register_block_style('core/button', array(
        'name'  => 'ote-chip',
        'label' => __('OTE Chip', 'ote-child-theme'),
    ));
    
    register_block_style('core/button', array(
        'name'  => 'ote-tab',
        'label' => __('OTE Tab', 'ote-child-theme'),
    ));
}
add_action('init', 'ote_child_block_styles');

/**
 * Add Theme Color Palette
 */
function ote_child_theme_colors() {
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Brand Green', 'ote-child-theme'),
            'slug'  => 'brand',
            'color' => '#2d5f3f',
        ),
        array(
            'name'  => __('Brand Dark', 'ote-child-theme'),
            'slug'  => 'brand-dark',
            'color' => '#1a3624',
        ),
        array(
            'name'  => __('Text Primary', 'ote-child-theme'),
            'slug'  => 'text',
            'color' => '#1a1a1a',
        ),
        array(
            'name'  => __('Text Secondary', 'ote-child-theme'),
            'slug'  => 'text-secondary',
            'color' => '#666666',
        ),
        array(
            'name'  => __('Surface', 'ote-child-theme'),
            'slug'  => 'surface',
            'color' => '#ffffff',
        ),
        array(
            'name'  => __('Background', 'ote-child-theme'),
            'slug'  => 'background',
            'color' => '#fafafa',
        ),
        array(
            'name'  => __('Muted', 'ote-child-theme'),
            'slug'  => 'muted',
            'color' => '#f5f5f5',
        ),
        array(
            'name'  => __('Border', 'ote-child-theme'),
            'slug'  => 'border',
            'color' => '#e0e0e0',
        ),
    ));
}
add_action('after_setup_theme', 'ote_child_theme_colors');