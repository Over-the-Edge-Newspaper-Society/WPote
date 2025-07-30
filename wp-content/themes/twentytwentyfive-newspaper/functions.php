<?php
/**
 * Twenty Twenty-Five Newspaper Child Theme Functions
 * 
 * @package Twenty_Twenty_Five_Newspaper
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function twentytwentyfive_newspaper_enqueue_styles() {
    // Get parent theme
    $parent_style = 'twentytwentyfive-style';
    
    // Enqueue parent theme stylesheet
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    
    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'twentytwentyfive-newspaper-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue dark mode script
    wp_enqueue_script(
        'twentytwentyfive-newspaper-darkmode',
        get_stylesheet_directory_uri() . '/assets/js/dark-mode.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_newspaper_enqueue_styles');

/**
 * Add theme support for various features
 */
function twentytwentyfive_newspaper_setup() {
    // Add support for editor color palette
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Primary', 'twentytwentyfive-newspaper'),
            'slug'  => 'primary',
            'color' => 'hsl(222.2, 47.4%, 11.2%)',
        ),
        array(
            'name'  => __('Secondary', 'twentytwentyfive-newspaper'),
            'slug'  => 'secondary',
            'color' => 'hsl(210, 40%, 96%)',
        ),
        array(
            'name'  => __('Accent', 'twentytwentyfive-newspaper'),
            'slug'  => 'accent',
            'color' => 'hsl(210, 40%, 96%)',
        ),
        array(
            'name'  => __('Muted', 'twentytwentyfive-newspaper'),
            'slug'  => 'muted',
            'color' => 'hsl(215.4, 16.3%, 46.9%)',
        ),
    ));
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for custom line heights
    add_theme_support('custom-line-height');
    
    // Add support for custom units
    add_theme_support('custom-units');
}
add_action('after_setup_theme', 'twentytwentyfive_newspaper_setup');

/**
 * Add custom CSS variables to the editor
 */
function twentytwentyfive_newspaper_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'twentytwentyfive_newspaper_editor_styles');

/**
 * Add dark mode toggle to site header
 */
function twentytwentyfive_newspaper_add_dark_mode_toggle() {
    ?>
    <button class="theme-toggle" id="theme-toggle" title="Toggle dark mode" aria-label="Toggle dark mode">
        <span class="theme-icon-light" style="display: none;">☀️</span>
        <span class="theme-icon-dark" style="display: inline;">🌙</span>
    </button>
    <?php
}
add_action('wp_body_open', 'twentytwentyfive_newspaper_add_dark_mode_toggle');

/**
 * Add custom body classes for dark mode
 */
function twentytwentyfive_newspaper_body_classes($classes) {
    // Add class for JavaScript to detect
    $classes[] = 'supports-dark-mode';
    
    return $classes;
}
add_filter('body_class', 'twentytwentyfive_newspaper_body_classes');

/**
 * Customize excerpt length for newspaper-style previews
 */
function twentytwentyfive_newspaper_excerpt_length($length) {
    return 25; // Shorter excerpts for newspaper layout
}
add_filter('excerpt_length', 'twentytwentyfive_newspaper_excerpt_length');

/**
 * Customize excerpt more text
 */
function twentytwentyfive_newspaper_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'twentytwentyfive_newspaper_excerpt_more');

/**
 * Add custom post meta for featured articles
 */
function twentytwentyfive_newspaper_add_meta_boxes() {
    add_meta_box(
        'newspaper_meta',
        'Newspaper Options',
        'twentytwentyfive_newspaper_meta_box_callback',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'twentytwentyfive_newspaper_add_meta_boxes');

/**
 * Meta box callback function
 */
function twentytwentyfive_newspaper_meta_box_callback($post) {
    wp_nonce_field('twentytwentyfive_newspaper_meta_box_nonce', 'newspaper_nonce');
    $featured = get_post_meta($post->ID, '_newspaper_featured', true);
    $breaking = get_post_meta($post->ID, '_newspaper_breaking', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="newspaper_featured" value="1" <?php checked($featured, '1'); ?>>
            Featured Article
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" name="newspaper_breaking" value="1" <?php checked($breaking, '1'); ?>>
            Breaking News
        </label>
    </p>
    <?php
}

/**
 * Save meta box data
 */
function twentytwentyfive_newspaper_save_meta_box($post_id) {
    if (!isset($_POST['newspaper_nonce']) || !wp_verify_nonce($_POST['newspaper_nonce'], 'twentytwentyfive_newspaper_meta_box_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $featured = isset($_POST['newspaper_featured']) ? '1' : '0';
    $breaking = isset($_POST['newspaper_breaking']) ? '1' : '0';
    
    update_post_meta($post_id, '_newspaper_featured', $featured);
    update_post_meta($post_id, '_newspaper_breaking', $breaking);
}
add_action('save_post', 'twentytwentyfive_newspaper_save_meta_box');

/**
 * Add featured post class to post articles
 */
function twentytwentyfive_newspaper_post_class($classes, $class, $post_id) {
    if (get_post_meta($post_id, '_newspaper_featured', true) === '1') {
        $classes[] = 'featured-post';
    }
    
    if (get_post_meta($post_id, '_newspaper_breaking', true) === '1') {
        $classes[] = 'breaking-news';
    }
    
    return $classes;
}
add_filter('post_class', 'twentytwentyfive_newspaper_post_class', 10, 3);