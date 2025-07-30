<?php
/**
 * Twenty Twenty-Five Franken UI Child Theme Functions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles plus Franken UI
 */
function twentytwentyfive_frankenui_enqueue_styles() {
    // Get parent theme version for cache busting
    $parent_theme = wp_get_theme('twentytwentyfive');
    $parent_version = $parent_theme->get('Version');
    
    // Get child theme version
    $child_theme = wp_get_theme();
    $child_version = $child_theme->get('Version');
    
    // Enqueue Franken UI CSS from CDN (before parent theme)
    wp_enqueue_style(
        'franken-ui-core',
        'https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/core.min.css',
        array(),
        '2.1.0-next.16'
    );
    
    // Enqueue Franken UI utilities
    wp_enqueue_style(
        'franken-ui-utilities',
        'https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/utilities.min.css',
        array('franken-ui-core'),
        '2.1.0-next.16'
    );
    
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'twentytwentyfive-style',
        get_template_directory_uri() . '/style.css',
        array('franken-ui-utilities'),
        $parent_version
    );
    
    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'twentytwentyfive-frankenui-style',
        get_stylesheet_uri(),
        array('twentytwentyfive-style'),
        $child_version
    );
    
    // Enqueue Franken UI JavaScript
    wp_enqueue_script(
        'franken-ui-core-js',
        'https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/js/core.iife.js',
        array(),
        '2.1.0-next.16',
        true
    );
    
    wp_enqueue_script(
        'franken-ui-icon-js',
        'https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/js/icon.iife.js',
        array('franken-ui-core-js'),
        '2.1.0-next.16',
        true
    );
    
    // Add custom theme JavaScript
    wp_enqueue_script(
        'twentytwentyfive-frankenui-script',
        get_stylesheet_directory_uri() . '/assets/js/theme.js',
        array('franken-ui-icon-js'),
        $child_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_frankenui_enqueue_styles');

/**
 * Enqueue block editor styles
 */
function twentytwentyfive_frankenui_editor_styles() {
    // Add Franken UI to block editor
    add_editor_style('https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/core.min.css');
    add_editor_style('https://cdn.jsdelivr.net/npm/franken-ui@2.1.0-next.16/dist/css/utilities.min.css');
    
    // Add custom editor styles
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'twentytwentyfive_frankenui_editor_styles');

/**
 * Add Franken UI theme initialization script to head
 */
function twentytwentyfive_frankenui_head_scripts() {
    ?>
    <script>
        // Initialize Franken UI theme settings
        const htmlElement = document.documentElement;
        const __FRANKEN__ = JSON.parse(localStorage.getItem("__FRANKEN__") || "{}");

        if (
            __FRANKEN__.mode === "dark" ||
            (!__FRANKEN__.mode && window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            htmlElement.classList.add("dark");
        } else {
            htmlElement.classList.remove("dark");
        }

        htmlElement.classList.add(__FRANKEN__.theme || "uk-theme-default");
        htmlElement.classList.add(__FRANKEN__.radii || "uk-radii-md");
        htmlElement.classList.add(__FRANKEN__.shadows || "uk-shadows-sm");
        htmlElement.classList.add(__FRANKEN__.font || "uk-font-sm");
        htmlElement.classList.add(__FRANKEN__.chart || "uk-chart-default");
    </script>
    <?php
}
add_action('wp_head', 'twentytwentyfive_frankenui_head_scripts', 5);

/**
 * Add theme support features
 */
function twentytwentyfive_frankenui_theme_support() {
    // Add support for custom spacing in blocks
    add_theme_support('custom-spacing');
    
    // Add support for custom units
    add_theme_support('custom-units', array('px', 'rem', 'em', '%', 'vw', 'vh'));
    
    // Add support for appearance tools
    add_theme_support('appearance-tools');
    
    // Register additional image sizes for newspaper
    add_image_size('article-card', 400, 250, true);
    add_image_size('featured-hero', 1200, 600, true);
}
add_action('after_setup_theme', 'twentytwentyfive_frankenui_theme_support');

/**
 * Register custom block styles
 */
function twentytwentyfive_frankenui_register_block_styles() {
    // Card style for groups
    register_block_style(
        'core/group',
        array(
            'name'  => 'card',
            'label' => __('Card', 'twentytwentyfive-frankenui'),
            'style_handle' => 'twentytwentyfive-frankenui-style',
        )
    );
    
    // Primary button style
    register_block_style(
        'core/button',
        array(
            'name'  => 'uk-primary',
            'label' => __('Primary', 'twentytwentyfive-frankenui'),
        )
    );
    
    // Secondary button style
    register_block_style(
        'core/button',
        array(
            'name'  => 'uk-secondary',
            'label' => __('Secondary', 'twentytwentyfive-frankenui'),
        )
    );
}
add_action('init', 'twentytwentyfive_frankenui_register_block_styles');

/**
 * Add custom block patterns
 */
function twentytwentyfive_frankenui_register_patterns() {
    register_block_pattern_category(
        'newspaper',
        array('label' => __('Newspaper', 'twentytwentyfive-frankenui'))
    );
    
    // Featured article pattern
    register_block_pattern(
        'twentytwentyfive-frankenui/featured-article',
        array(
            'title'       => __('Featured Article', 'twentytwentyfive-frankenui'),
            'description' => __('A featured article layout with image and content', 'twentytwentyfive-frankenui'),
            'categories'  => array('newspaper'),
            'content'     => '<!-- wp:group {"className":"uk-card uk-card-default","layout":{"type":"constrained"}} -->
<div class="wp-block-group uk-card uk-card-default">
<!-- wp:post-featured-image {"isLink":true} /-->
<!-- wp:group {"style":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}}} -->
<div class="wp-block-group" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem">
<!-- wp:post-title {"isLink":true} /-->
<!-- wp:post-date /-->
<!-- wp:post-excerpt /-->
<!-- wp:read-more {"content":"Read Full Article →"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        )
    );
}
add_action('init', 'twentytwentyfive_frankenui_register_patterns');

/**
 * Add footer for dark mode toggle
 */
function twentytwentyfive_frankenui_footer() {
    ?>
    <button class="theme-toggle" id="theme-toggle" title="Toggle dark mode">
        <span class="theme-icon-light">☀️</span>
        <span class="theme-icon-dark" style="display:none;">🌙</span>
    </button>
    <?php
}
add_action('wp_footer', 'twentytwentyfive_frankenui_footer');

/**
 * Filter navigation block to add Franken UI classes
 */
function twentytwentyfive_frankenui_navigation_block($block_content, $block) {
    if ($block['blockName'] === 'core/navigation') {
        // Add UK navigation classes
        $block_content = str_replace(
            'wp-block-navigation__container',
            'wp-block-navigation__container uk-nav uk-nav-default',
            $block_content
        );
    }
    return $block_content;
}
add_filter('render_block', 'twentytwentyfive_frankenui_navigation_block', 10, 2);

/**
 * Customize query loop block
 */
function twentytwentyfive_frankenui_query_loop_block($block_content, $block) {
    if ($block['blockName'] === 'core/post-template') {
        // Add grid classes for better layout
        $block_content = str_replace(
            'wp-block-post-template',
            'wp-block-post-template uk-grid-match',
            $block_content
        );
    }
    return $block_content;
}
add_filter('render_block', 'twentytwentyfive_frankenui_query_loop_block', 10, 2);