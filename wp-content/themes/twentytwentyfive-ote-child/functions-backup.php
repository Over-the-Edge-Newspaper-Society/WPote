<?php
/**
 * Over the Edge - Twenty Twenty-Five Child Theme Functions
 * Based on the New Implementation design system
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function ote_child_enqueue_styles() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'twentytwentyfive-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // Child theme stylesheet (this includes all imports)
    wp_enqueue_style(
        'ote-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('twentytwentyfive-style'),
        wp_get_theme()->get('Version')
    );
    
    // Ensure design system is loaded on frontend
    wp_enqueue_style(
        'ote-design-system',
        get_stylesheet_directory_uri() . '/assets/css/design-system.css',
        array('ote-child-style'),
        wp_get_theme()->get('Version')
    );
    
    // Ensure card grid styles are loaded on frontend
    wp_enqueue_style(
        'ote-card-grid',
        get_stylesheet_directory_uri() . '/assets/css/card-grid.css',
        array('ote-design-system'),
        wp_get_theme()->get('Version')
    );
    
    // Button animations for View Details buttons
    wp_enqueue_style(
        'ote-button-animations',
        get_stylesheet_directory_uri() . '/assets/css/button-animations.css',
        array('ote-card-grid'),
        wp_get_theme()->get('Version')
    );
    
    // Chips and tabs components
    wp_enqueue_style(
        'ote-chips-tabs',
        get_stylesheet_directory_uri() . '/assets/css/chips-tabs.css',
        array('ote-button-animations'),
        wp_get_theme()->get('Version')
    );
    
    // News grid styling
    wp_enqueue_style(
        'ote-news-grid',
        get_stylesheet_directory_uri() . '/assets/css/news-grid.css',
        array('ote-chips-tabs'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'ote_child_enqueue_styles');

/**
 * Enqueue theme JavaScript
 */
function ote_child_enqueue_scripts() {
    // Theme JavaScript
    wp_enqueue_script(
        'ote-theme-js',
        get_stylesheet_directory_uri() . '/assets/js/theme.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Dark mode toggle JavaScript
    wp_enqueue_script(
        'ote-dark-mode',
        get_stylesheet_directory_uri() . '/assets/js/dark-mode-toggle.js',
        array('ote-theme-js'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Club cards conditional display JavaScript (load in head for faster execution)
    // Only load on organization post type pages
    if (is_singular('organization')) {
        wp_enqueue_script(
            'ote-club-cards',
            get_stylesheet_directory_uri() . '/assets/js/club-cards.js',
            array(),
            wp_get_theme()->get('Version'),
            false  // Load in head instead of footer
        );
    }
    
    // Logo theme switcher JavaScript
    wp_enqueue_script(
        'ote-logo-theme',
        get_stylesheet_directory_uri() . '/assets/js/logo-theme.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Category filters JavaScript
    wp_enqueue_script(
        'ote-category-filters',
        get_stylesheet_directory_uri() . '/assets/js/category-filters.js',
        array('ote-theme-js'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('ote-theme-js', 'ote_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ote_nonce'),
    ));
    
    // Enqueue mobile menu script
    wp_enqueue_script(
        'ote-mobile-menu',
        get_stylesheet_directory_uri() . '/assets/js/mobile-menu.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'ote_child_enqueue_scripts');

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
 * Dark Mode Toggle Shortcode
 * Usage: [dark_mode_toggle] or [dark_mode_toggle style="button" size="large"]
 */
function ote_dark_mode_toggle_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'button',  // button, icon, pill
        'size' => 'medium',   // small, medium, large
        'show_label' => 'false', // true, false
        'align' => 'left'     // left, center, right
    ), $atts);
    
    $wrapper_classes = array(
        'ote-dark-mode-toggle',
        'ote-toggle--' . $atts['style'],
        'ote-toggle--' . $atts['size'],
        'ote-toggle--align-' . $atts['align']
    );
    
    $show_label = $atts['show_label'] === 'true';
    
    $sun_icon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
    </svg>';
    
    $moon_icon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"/>
    </svg>';
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
        <button type="button" class="theme-toggle" id="darkModeToggle" 
                aria-label="<?php esc_attr_e('Toggle dark/light theme', 'ote-child-theme'); ?>">
            <span class="theme-toggle__icon theme-toggle__icon--sun">
                <?php echo $sun_icon; ?>
            </span>
            <span class="theme-toggle__icon theme-toggle__icon--moon">
                <?php echo $moon_icon; ?>
            </span>
            <?php if ($show_label && $atts['style'] !== 'pill'): ?>
                <span class="theme-toggle__label"><?php _e('Toggle Theme', 'ote-child-theme'); ?></span>
            <?php endif; ?>
        </button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dark_mode_toggle', 'ote_dark_mode_toggle_shortcode');

/**
 * Organization Search Bar Shortcode
 * Usage: [organization_search] or [organization_search placeholder="Search clubs..." target=".ote-organization-grid"]
 */
function ote_organization_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'placeholder' => 'Search organizations...',
        'target' => '.ote-organization-grid',  // CSS selector for the grid to filter
        'style' => 'default',  // default, inline, compact
        'show_count' => 'true',
        'align' => 'center'  // left, center, right
    ), $atts);
    
    $wrapper_classes = array(
        'ote-org-search-wrapper',
        'ote-org-search--' . $atts['style'],
        'ote-org-search--align-' . $atts['align']
    );
    
    $unique_id = 'org-search-' . wp_generate_uuid4();
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" data-search-id="<?php echo esc_attr($unique_id); ?>">
        <div class="ote-org-search-container">
            <div class="ote-org-search-field">
                <svg class="ote-org-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" 
                       class="ote-org-search-input" 
                       placeholder="<?php echo esc_attr($atts['placeholder']); ?>" 
                       aria-label="<?php echo esc_attr($atts['placeholder']); ?>"
                       data-target="<?php echo esc_attr($atts['target']); ?>"
                       data-show-count="<?php echo esc_attr($atts['show_count']); ?>">
                <?php if ($atts['show_count'] === 'true'): ?>
                    <span class="ote-org-search-count"></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('organization_search', 'ote_organization_search_shortcode');

/**
 * Icon Shortcode for Organization Pages
 * Usage: [icon name="users"] or [icon name="globe" size="32"]
 */
function ote_icon_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => 'users',
        'size' => '24',
        'color' => 'currentColor'
    ), $atts);
    
    $icons = array(
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'share' => '<path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16,6 12,2 8,6"/><line x1="12" y1="2" x2="12" y2="15"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
        'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'twitter' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
        'x' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
        'linktree' => '<path d="M7.953 15.066c-.08 0-.158-.004-.235-.011l-.005-2.419c.077.007.155.011.24.011.486 0 .962-.112 1.373-.323l1.386 2.401c-.746.213-1.511.341-2.759.341zM4.307 13.068a5.82 5.82 0 0 1-.72-2.754 5.822 5.822 0 0 1 .72-2.757l2.174 1.253c-.17.461-.267.967-.267 1.504 0 .536.097 1.042.267 1.501l-2.174 1.253zM7.953 8.934c0 0 .158-.007.235-.011l.005-2.42c-.077.008-.155.012-.24.012-.486 0-.962.112-1.373.322L5.194 4.436c.746-.213 1.511-.341 2.759-.341zM11.597 10.31a5.82 5.82 0 0 1 .72 2.754 5.822 5.822 0 0 1-.72 2.757l-2.174-1.253c.17-.461.267-.967.267-1.504 0-.536-.097-1.042-.267-1.501l2.174-1.253z"/><circle cx="12" cy="12" r="2"/><path d="M16.5 9c1.381 0 2.5 1.119 2.5 2.5S17.881 14 16.5 14 14 12.881 14 11.5 15.119 9 16.5 9m0-2C13.462 7 11 9.462 11 12.5S13.462 18 16.5 18 22 15.538 22 12.5 19.538 7 16.5 7z"/>',
        'youtube' => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75,15.02 15.5,11.75 9.75,8.48"/>',
        'discord' => '<path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0189 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0189 1.3332-.946 2.4189-2.1569 2.4189Z"/>',
        'link' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>'
    );
    
    $icon_path = isset($icons[$atts['name']]) ? $icons[$atts['name']] : $icons['users'];
    
    return sprintf(
        '<svg width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="%s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">%s</svg>',
        esc_attr($atts['size']),
        esc_attr($atts['size']),
        esc_attr($atts['color']),
        $icon_path
    );
}
add_shortcode('icon', 'ote_icon_shortcode');

/**
 * OTE Logo Shortcode with Dark Mode Support
 * Usage: [ote_logo] or [ote_logo type="text" size="200" link="true" align="center"]
 */
function ote_logo_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'symbol',     // symbol, text, full
        'size' => '100',        // Size in pixels (height for symbol, width for text/full)
        'link' => 'false',      // true, false - wrap in home link
        'align' => 'left',      // left, center, right
        'class' => '',          // Additional CSS classes
        'variant' => 'default', // default, white - color variant
    ), $atts);
    
    $wrapper_classes = array(
        'ote-logo-wrapper',
        'ote-logo--' . $atts['type'],
        'ote-logo--align-' . $atts['align']
    );
    
    if (!empty($atts['class'])) {
        $wrapper_classes[] = $atts['class'];
    }
    
    $size = intval($atts['size']);
    $should_link = $atts['link'] === 'true';
    
    // Generate the appropriate logo SVG
    $svg_content = '';
    switch ($atts['type']) {
        case 'text':
            $svg_content = ote_get_text_logo_svg($size, $atts['variant']);
            break;
        case 'full':
            $svg_content = ote_get_full_logo_svg($size);
            break;
        default: // symbol
            $svg_content = ote_get_symbol_logo_svg($size);
            break;
    }
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
        <?php if ($should_link): ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ote-logo-link" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <?php echo $svg_content; ?>
            </a>
        <?php else: ?>
            <?php echo $svg_content; ?>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Generate OTE Symbol Logo using inline SVG from file
 */
function ote_get_symbol_logo_svg($height = 100) {
    $logo_path = get_stylesheet_directory() . '/assets/images/ote-logo.svg';
    $width = ($height * 208.67) / 300; // Maintain aspect ratio for symbol only
    
    if (file_exists($logo_path)) {
        $svg_content = file_get_contents($logo_path);
        // Update width and height attributes
        $svg_content = preg_replace('/width="[^"]*"/', 'width="' . $width . '"', $svg_content);
        $svg_content = preg_replace('/height="[^"]*"/', 'height="' . $height . '"', $svg_content);
        // Add our CSS classes
        $svg_content = str_replace('<svg', '<svg class="ote-logo ote-logo-symbol"', $svg_content);
        return $svg_content;
    }
    
    // Fallback if file doesn't exist
    return sprintf('<div class="ote-logo-placeholder" style="width: %dpx; height: %dpx; background: #2c6753; display: flex; align-items: center; justify-content: center; color: white;">OTE</div>', $width, $height);
}

/**
 * Generate OTE Text Logo using inline SVG from file
 */
function ote_get_text_logo_svg($width = 485, $variant = 'default') {
    // Choose logo based on variant
    if ($variant === 'white') {
        $logo_path = get_stylesheet_directory() . '/assets/images/ote-logo-full-no-subtext-white.svg';
    } else {
        $logo_path = get_stylesheet_directory() . '/assets/images/ote-logo-full-no-subtext.svg';
    }
    
    $height = ($width * 60.1) / 485; // Maintain aspect ratio for logo without subtext
    
    if (file_exists($logo_path)) {
        $svg_content = file_get_contents($logo_path);
        // Update width and height attributes
        $svg_content = preg_replace('/width="[^"]*"/', 'width="' . $width . '"', $svg_content);
        $svg_content = preg_replace('/height="[^"]*"/', 'height="' . $height . '"', $svg_content);
        // Add our CSS classes
        $svg_content = str_replace('<svg', '<svg class="ote-logo ote-logo-text"', $svg_content);
        return $svg_content;
    }
    
    // Fallback if file doesn't exist
    return sprintf('<div class="ote-logo-placeholder" style="width: %dpx; height: %dpx; background: #2c6753; display: flex; align-items: center; justify-content: center; color: white;">OTE TEXT</div>', $width, $height);
}

/**
 * Generate OTE Full Logo SVG (Symbol + Text)
 */
function ote_get_full_logo_svg($width = 485) {
    $logo_path = get_stylesheet_directory() . '/assets/images/ote-text-logo-themed.svg';
    $height = ($width * 80.16) / 485; // Maintain aspect ratio for full logo with subtext
    
    if (file_exists($logo_path)) {
        $svg_content = file_get_contents($logo_path);
        // Update width and height attributes
        $svg_content = preg_replace('/width="[^"]*"/', 'width="' . $width . '"', $svg_content);
        $svg_content = preg_replace('/height="[^"]*"/', 'height="' . $height . '"', $svg_content);
        // Note: CSS classes already exist in the SVG file
        return $svg_content;
    }
    
    // Fallback if file doesn't exist
    return sprintf('<div class="ote-logo-placeholder" style="width: %dpx; height: %dpx; background: #2c6753; display: flex; align-items: center; justify-content: center; color: white;">OTE FULL</div>', $width, $height);
}

add_shortcode('ote_logo', 'ote_logo_shortcode');

/**
 * Category Filter Tabs Shortcode
 * Usage: [category_filters] or [category_filters post_type="post" show_all="true" style="tabs"]
 */
function ote_category_filters_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'post',     // Post type to filter
        'taxonomy' => 'category',   // Taxonomy to use for filtering
        'show_all' => 'true',      // Show "All" option
        'all_text' => 'All',       // Text for "All" button
        'style' => 'chips',        // chips, tabs
        'target' => '.wp-block-query', // CSS selector for query loop to filter
        'ajax' => 'false',         // Enable AJAX filtering (requires custom JS)
        'categories' => '',        // Comma-separated category slugs (empty = all)
        'show_count' => 'false'    // Show post count in each filter
    ), $atts);

    $taxonomy = $atts['taxonomy'];
    $post_type = $atts['post_type'];
    $show_all = ($atts['show_all'] === 'true');
    $style = $atts['style'];
    $target = $atts['target'];
    $ajax_enabled = ($atts['ajax'] === 'true');
    $show_count = ($atts['show_count'] === 'true');

    // Get categories
    $categories = array();
    if (!empty($atts['categories'])) {
        $category_slugs = array_map('trim', explode(',', $atts['categories']));
        $categories = get_terms(array(
            'taxonomy' => $taxonomy,
            'slug' => $category_slugs,
            'hide_empty' => true,
        ));
    } else {
        $categories = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));
    }

    if (empty($categories) || is_wp_error($categories)) {
        return '<p>No categories found.</p>';
    }

    ob_start();
    ?>
    <div class="ote-category-filters" data-target="<?php echo esc_attr($target); ?>" data-ajax="<?php echo $ajax_enabled ? 'true' : 'false'; ?>">
        <div class="ote-<?php echo esc_attr($style); ?>" role="tablist" aria-label="Category filters">
            <?php if ($show_all): ?>
                <button class="ote-<?php echo esc_attr($style === 'tabs' ? 'tab' : 'chip'); ?> ote-filter-item is-active" 
                        role="tab" 
                        aria-selected="true" 
                        data-category="all"
                        data-category-id=""
                        onclick="oteFilterByCategory(this, 'all')">
                    <?php echo esc_html($atts['all_text']); ?>
                    <?php if ($show_count): ?>
                        <span class="count"><?php echo wp_count_posts($post_type)->publish; ?></span>
                    <?php endif; ?>
                </button>
            <?php endif; ?>
            
            <?php foreach ($categories as $category): 
                $count = $show_count ? $category->count : 0;
                ?>
                <button class="ote-<?php echo esc_attr($style === 'tabs' ? 'tab' : 'chip'); ?> ote-filter-item" 
                        role="tab" 
                        aria-selected="false" 
                        data-category="<?php echo esc_attr($category->slug); ?>"
                        data-category-id="<?php echo esc_attr($category->term_id); ?>"
                        onclick="oteFilterByCategory(this, '<?php echo esc_js($category->slug); ?>')">
                    <?php echo esc_html($category->name); ?>
                    <?php if ($show_count): ?>
                        <span class="count"><?php echo $count; ?></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('category_filters', 'ote_category_filters_shortcode');


/**
 * Add Shortcode button to Block Editor
 * DISABLED - Gutenberg block removed per user request
 */
// function ote_add_shortcode_block_editor_button() {
//     wp_enqueue_script(
//         'ote-shortcode-button',
//         get_stylesheet_directory_uri() . '/assets/js/shortcode-block-button.js',
//         array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'),
//         wp_get_theme()->get('Version'),
//         true
//     );
// }
// add_action('enqueue_block_editor_assets', 'ote_add_shortcode_block_editor_button');


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
            'name'  => __('Brand Green 2', 'ote-child-theme'),
            'slug'  => 'brand-2',
            'color' => '#4a7c59',
        ),
        array(
            'name'  => __('Accent Blue', 'ote-child-theme'),
            'slug'  => 'accent-blue',
            'color' => '#0ea5e9',
        ),
        array(
            'name'  => __('Text Primary', 'ote-child-theme'),
            'slug'  => 'text',
            'color' => '#1e293b',
        ),
        array(
            'name'  => __('Text Secondary', 'ote-child-theme'),
            'slug'  => 'text-secondary',
            'color' => '#64748b',
        ),
        array(
            'name'  => __('Background', 'ote-child-theme'),
            'slug'  => 'background',
            'color' => '#ffffff',
        ),
        array(
            'name'  => __('Surface', 'ote-child-theme'),
            'slug'  => 'surface',
            'color' => '#f8fafc',
        ),
    ));
}
add_action('after_setup_theme', 'ote_child_theme_colors');

/**
 * AJAX Handler for Theme Toggle
 */
function ote_child_ajax_theme_toggle() {
    check_ajax_referer('ote_nonce', 'nonce');
    
    $theme = sanitize_text_field($_POST['theme']);
    $allowed_themes = array('light', 'dark', 'system');
    
    if (in_array($theme, $allowed_themes)) {
        setcookie('ote_theme', $theme, time() + (86400 * 30), '/');
        wp_send_json_success(array('theme' => $theme));
    } else {
        wp_send_json_error(array('message' => 'Invalid theme'));
    }
}
add_action('wp_ajax_theme_toggle', 'ote_child_ajax_theme_toggle');
add_action('wp_ajax_nopriv_theme_toggle', 'ote_child_ajax_theme_toggle');

/**
 * Add Body Classes for Theme
 */
function ote_child_body_classes($classes) {
    $theme = isset($_COOKIE['ote_theme']) ? $_COOKIE['ote_theme'] : 'system';
    $classes[] = 'theme-' . $theme;
    return $classes;
}
add_filter('body_class', 'ote_child_body_classes');

/**
 * Navigation Menu Setup
 */
function ote_child_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'ote-child-theme'),
        'footer' => __('Footer Menu', 'ote-child-theme'),
        'mobile' => __('Mobile Menu', 'ote-child-theme'),
    ));
}
add_action('init', 'ote_child_register_menus');

/**
 * Theme Support
 */
function ote_child_theme_support() {
    // Add theme support features
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    
    // Add editor styles - include both design system and editor-specific styles
    add_editor_style('assets/css/design-system.css');
    add_editor_style('assets/css/card-grid.css'); 
    add_editor_style('assets/css/editor-style.css');
}

add_action('after_setup_theme', 'ote_child_theme_support');

/**
 * Enqueue block assets for both editor and frontend
 */
function ote_child_enqueue_block_assets() {
    // Only load in admin/editor context
    if (is_admin()) {
        // Enqueue design system for block editor
        wp_enqueue_style(
            'ote-design-system-editor',
            get_stylesheet_directory_uri() . '/assets/css/design-system.css',
            array(),
            wp_get_theme()->get('Version')
        );
        
        // Enqueue card grid styles for block editor
        wp_enqueue_style(
            'ote-card-grid-editor',
            get_stylesheet_directory_uri() . '/assets/css/card-grid.css',
            array('ote-design-system-editor'),
            wp_get_theme()->get('Version')
        );
        
        // Enqueue editor-specific styles
        wp_enqueue_style(
            'ote-editor-styles',
            get_stylesheet_directory_uri() . '/assets/css/editor-style.css',
            array('ote-card-grid-editor'),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('enqueue_block_assets', 'ote_child_enqueue_block_assets');


/**
 * Add inline styles for theme functionality
 */
function ote_child_inline_styles() {
    ?>
    <style id="ote-inline-styles">
        /* Ensure dark mode toggle works */
        html {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Typography - Headline styles for post titles */
        .wp-block-post-title,
        h1.wp-block-post-title {
            font-size: clamp(24px, 2.6vw, 32px);
            line-height: 1.2;
            letter-spacing: -0.01em;
            font-weight: 600;
        }
        
        /* Extra large headlines for main pages */
        .page-template-default h1.wp-block-post-title,
        .single h1.wp-block-post-title {
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        
        /* Medium headlines */
        h2.wp-block-heading {
            font-size: clamp(20px, 2.2vw, 28px);
            letter-spacing: -0.01em;
            font-weight: 600;
        }
        
        /* Small headlines */
        h3.wp-block-heading {
            font-size: 18px;
            font-weight: 600;
        }
        
        /* Dark theme variables */
        [data-theme="dark"] {
            --brand: #4a7c59;
            --brand-2: #86a58e;
            --accent-blue: #38bdf8;
            --bg: #0f172a;
            --bg-elev: #1e293b;
            --surface: #1e293b;
            --text: #e6eaf2;
            --text-sec: #94a3b8;
            --border: #334155;
            --muted: #1e293b;
        }
        
        [data-theme="dark"] body {
            background: var(--bg);
            color: var(--text);
        }
        
        /* WordPress block elements in dark mode */
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] .wp-block-heading,
        [data-theme="dark"] .wp-block-post-title {
            color: var(--text) !important;
        }
        
        [data-theme="dark"] .wp-block-post-title a,
        [data-theme="dark"] .wp-block-heading a,
        [data-theme="dark"] h1 a,
        [data-theme="dark"] h2 a,
        [data-theme="dark"] h3 a,
        [data-theme="dark"] h4 a,
        [data-theme="dark"] h5 a,
        [data-theme="dark"] h6 a {
            color: var(--text) !important;
            text-decoration: none;
        }
        
        [data-theme="dark"] .wp-block-post-title a:hover,
        [data-theme="dark"] .wp-block-heading a:hover,
        [data-theme="dark"] a:hover {
            opacity: 0.8;
        }
        
        [data-theme="dark"] p,
        [data-theme="dark"] .wp-block-paragraph,
        [data-theme="dark"] .wp-block-post-excerpt,
        [data-theme="dark"] .wp-block-post-content {
            color: var(--text) !important;
        }
        
        /* Removed global brand color for all links - let them inherit naturally */
        
        [data-theme="dark"] .wp-block-post-date,
        [data-theme="dark"] .wp-block-post-author,
        [data-theme="dark"] .wp-block-post-terms,
        [data-theme="dark"] small,
        [data-theme="dark"] .meta {
            color: var(--text-sec) !important;
        }
        
        /* Dark mode for containers and blocks */
        [data-theme="dark"] .wp-block-group,
        [data-theme="dark"] .wp-block-columns,
        [data-theme="dark"] .wp-block-column,
        [data-theme="dark"] .wp-block-cover,
        [data-theme="dark"] article,
        [data-theme="dark"] section,
        [data-theme="dark"] .entry-content {
            background: transparent;
            color: var(--text);
        }
        
        [data-theme="dark"] .wp-block-separator,
        [data-theme="dark"] hr {
            border-color: var(--border) !important;
            background-color: var(--border) !important;
        }
        
        /* Dark mode for forms */
        [data-theme="dark"] input,
        [data-theme="dark"] textarea,
        [data-theme="dark"] select,
        [data-theme="dark"] .wp-block-search__input {
            background: var(--surface) !important;
            color: var(--text) !important;
            border-color: var(--border) !important;
        }
        
        [data-theme="dark"] input:focus,
        [data-theme="dark"] textarea:focus,
        [data-theme="dark"] select:focus {
            border-color: var(--text-sec) !important;
            box-shadow: 0 0 0 2px color-mix(in oklab, var(--text-sec), transparent 70%) !important;
        }
        
        /* Dark mode for buttons - removed brand colors */
        
        /* Button hover states handled by individual components */
        
        /* Dark mode for navigation */
        [data-theme="dark"] .wp-block-navigation,
        [data-theme="dark"] .wp-block-navigation-link,
        [data-theme="dark"] nav {
            color: var(--text) !important;
        }
        
        [data-theme="dark"] .wp-block-navigation-link a {
            color: var(--text) !important;
        }
        
        [data-theme="dark"] .wp-block-navigation-link a:hover {
            color: var(--text) !important;
            opacity: 0.8;
        }
        
        /* Dark mode toggle styles moved to component-specific styles above */
        
        /* Dark Mode Toggle Shortcode Styles */
        .ote-dark-mode-toggle {
            margin: 16px 0;
        }
        
        .ote-toggle--align-left { text-align: left; }
        .ote-toggle--align-center { text-align: center; }
        .ote-toggle--align-right { text-align: right; }
        
        /* Button Style */
        .ote-toggle--button .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: transparent;
            border: 2px solid currentColor;
            border-radius: 10px;
            color: #1e293b;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        [data-theme="dark"] .ote-toggle--button .theme-toggle {
            color: #ffffff;
        }
        
        .ote-toggle--button .theme-toggle:hover {
            background: currentColor;
            transform: translateY(-2px);
        }
        
        .ote-toggle--button .theme-toggle:hover span {
            color: var(--bg, #ffffff);
        }
        
        /* Icon Only Style - Enhanced with overlapping icons */
        .ote-toggle--icon .theme-toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 10px;
            color: currentColor;
            cursor: pointer;
            overflow: hidden;
            transition: background 160ms cubic-bezier(.2,.6,.2,1), 
                        color 160ms cubic-bezier(.2,.6,.2,1), 
                        transform 160ms cubic-bezier(.2,.6,.2,1),
                        border-color 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        /* Light mode icon colors */
        .ote-toggle--icon .theme-toggle {
            color: #1e293b;
        }
        
        /* Dark mode icon colors */
        [data-theme="dark"] .ote-toggle--icon .theme-toggle {
            color: #ffffff;
        }
        
        .ote-toggle--icon .theme-toggle:hover {
            background: rgba(128, 128, 128, 0.1);
            transform: translateY(-1px);
        }
        
        [data-theme="dark"] .ote-toggle--icon .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .ote-toggle--icon .theme-toggle:active {
            transform: translateY(1px);
        }
        
        /* Remove focus outline for cleaner look */
        .ote-dark-mode-toggle .theme-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(128, 128, 128, 0.2);
        }
        
        [data-theme="dark"] .ote-dark-mode-toggle .theme-toggle:focus {
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }
        
        /* Pill Style */
        .ote-toggle--pill .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #1e293b;
            border: none;
            border-radius: 25px;
            color: white;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        [data-theme="dark"] .ote-toggle--pill .theme-toggle {
            background: #ffffff;
            color: #1e293b;
        }
        
        .ote-toggle--pill .theme-toggle:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        /* Size Variations */
        .ote-toggle--small .theme-toggle {
            padding: 6px 12px;
            font-size: 14px;
        }
        
        .ote-toggle--small.ote-toggle--icon .theme-toggle {
            width: 36px;
            height: 36px;
        }
        
        .ote-toggle--large .theme-toggle {
            padding: 12px 20px;
            font-size: 18px;
        }
        
        .ote-toggle--large.ote-toggle--icon .theme-toggle {
            width: 52px;
            height: 52px;
        }
        
        /* Icon animations - Enhanced with vertical slide transition */
        .ote-toggle--icon .theme-toggle__icon {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 240ms cubic-bezier(.2,.6,.2,1), 
                        opacity 240ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-toggle--icon .theme-toggle__icon--sun {
            transform: translateY(0);
            opacity: 1;
        }
        
        .ote-toggle--icon .theme-toggle__icon--moon {
            transform: translateY(10px);
            opacity: 0;
        }
        
        [data-theme="dark"] .ote-toggle--icon .theme-toggle__icon--sun {
            transform: translateY(-10px);
            opacity: 0;
        }
        
        [data-theme="dark"] .ote-toggle--icon .theme-toggle__icon--moon {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Default icon animations for other styles */
        .ote-toggle--button .theme-toggle__icon,
        .ote-toggle--pill .theme-toggle__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .ote-toggle--button .theme-toggle__icon--moon,
        .ote-toggle--pill .theme-toggle__icon--moon {
            position: absolute;
            opacity: 0;
            transform: rotate(180deg);
        }
        
        [data-theme="dark"] .ote-toggle--button .theme-toggle__icon--sun,
        [data-theme="dark"] .ote-toggle--pill .theme-toggle__icon--sun {
            opacity: 0;
            transform: rotate(-180deg);
        }
        
        [data-theme="dark"] .ote-toggle--button .theme-toggle__icon--moon,
        [data-theme="dark"] .ote-toggle--pill .theme-toggle__icon--moon {
            opacity: 1;
            transform: rotate(0deg);
        }
        
        .theme-toggle__label {
            white-space: nowrap;
        }
        
        /* Custom Organization Card Grid */
        .ote-organization-grid .wp-block-post-template {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: var(--space-4, 16px);
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        /* Mobile: Fix grid container margins */
        @media (max-width: 639px) {
            /* Fix alignfull containers extending beyond viewport */
            .alignfull {
                margin-left: 0 !important;
                margin-right: 0 !important;
                max-width: 100vw !important;
                overflow-x: hidden !important;
            }
            
            .wp-block-group.alignfull {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            
            .wp-block-post-content.alignfull {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            
            .ote-organization-grid {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                max-width: 100vw !important;
                overflow-x: hidden;
            }
            
            .ote-organization-grid .wp-block-post-template {
                gap: 12px;
                padding: 0 16px;
                margin: 0;
                max-width: 100%;
            }
        }
        
        /* Desktop: Multi-column layout */
        @media (min-width: 640px) {
            .ote-organization-grid .wp-block-post-template {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
        @media (min-width: 1000px) {
            .ote-organization-grid .wp-block-post-template {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        
        .ote-organization-grid .wp-block-post-template > li {
            background: var(--surface, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: var(--radius-m, 14px);
            padding: 0;
            box-shadow: var(--elev-1, 0 2px 8px rgba(0,0,0,.08));
            transition: transform 240ms cubic-bezier(.2,.6,.2,1), box-shadow 240ms cubic-bezier(.2,.6,.2,1), border-color 240ms cubic-bezier(.2,.6,.2,1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            text-decoration: none;
        }
        
        /* Remove any focus outlines from the entire card */
        .ote-organization-grid .wp-block-post-template > li:focus {
            outline: none !important;
            box-shadow: var(--elev-2, 0 8px 24px rgba(0,0,0,.15)) !important;
        }
        
        
        /* Tilt hover animation */
        .ote-organization-grid .wp-block-post-template > li:hover {
            transform: translateY(-3px) translateX(1px) rotate(-0.4deg);
            box-shadow: var(--elev-2, 0 8px 24px rgba(0,0,0,.15));
            border-color: color-mix(in oklab, var(--brand, #2d5f3f), transparent 30%);
        }
        
        
        /* Keep card layout vertical with logo on top */
        .ote-organization-grid .wp-block-post-template > li {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        /* Mobile: Horizontal layout with text beside logo */
        @media (max-width: 639px) {
            .ote-organization-grid .wp-block-post-template > li {
                display: grid;
                grid-template-columns: auto 1fr;
                grid-template-rows: auto auto auto;
                gap: 0;
                align-items: start;
                padding: var(--space-4, 16px);
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
            }
            
            .ote-organization-grid .wp-block-post-template > li:hover {
                transform: translateY(-2px);
                rotate: 0deg;
            }
        }
        
        .ote-organization-grid .wp-block-post-featured-image {
            margin: var(--space-4, 16px) 0 0 var(--space-4, 16px);
            width: 80px;
            height: 80px;
            aspect-ratio: 1;
            background: linear-gradient(135deg, var(--brand, #2d5f3f), var(--brand-2, #4a7c59));
            border-radius: var(--radius-s, 10px);
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .ote-organization-grid .wp-block-post-featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius-s, 10px);
            display: block;
        }
        
        /* Placeholder when no image is present */
        .ote-organization-grid .wp-block-post-featured-image:not(:has(img))::after {
            content: "🏛️";
            font-size: 32px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Create placeholder when featured image block doesn't exist at all */
        .ote-organization-grid .wp-block-post-template > li:not(:has(.wp-block-post-featured-image))::before {
            content: "🏛️";
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            margin: var(--space-4, 16px) 0 0 var(--space-4, 16px);
            background: linear-gradient(135deg, var(--brand, #2d5f3f), var(--brand-2, #4a7c59));
            border-radius: var(--radius-s, 10px);
            font-size: 32px;
            color: rgba(255, 255, 255, 0.8);
            flex-shrink: 0;
        }
        
        /* Mobile: Image positioned in first column, spanning all rows, centered vertically */
        @media (max-width: 639px) {
            .ote-organization-grid .wp-block-post-featured-image {
                grid-column: 1;
                grid-row: 1 / -1;
                width: 64px;
                height: 64px;
                margin: 0 var(--space-4, 16px) 0 0;
                border-radius: var(--radius-s, 10px);
                aspect-ratio: 1;
                align-self: center;
            }
            
            .ote-organization-grid .wp-block-post-featured-image img {
                border-radius: var(--radius-s, 10px);
            }
            
            .ote-organization-grid .wp-block-post-featured-image:not(:has(img))::after {
                font-size: 24px;
            }
            
            .ote-organization-grid .wp-block-post-template > li:not(:has(.wp-block-post-featured-image))::before {
                grid-column: 1;
                grid-row: 1 / -1;
                width: 64px;
                height: 64px;
                margin: 0 var(--space-4, 16px) 0 0;
                border-radius: var(--radius-s, 10px);
                font-size: 24px;
                align-self: center;
            }
        }
        
        .ote-organization-grid .wp-block-post-title {
            padding: var(--space-4, 16px) var(--space-4, 16px) var(--space-2, 8px);
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            line-height: 1.25;
        }
        
        .ote-organization-grid .wp-block-post-title a {
            text-decoration: none;
            color: var(--text, #1e293b);
            outline: none !important;
            box-shadow: none !important;
        }
        
        .ote-organization-grid .wp-block-post-title a:hover {
            color: var(--brand, #2d5f3f);
        }
        
        .ote-organization-grid .wp-block-post-title a:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        .ote-organization-grid .wp-block-post-title a:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Mobile: Title in second column, first row */
        @media (max-width: 639px) {
            .ote-organization-grid .wp-block-post-title {
                grid-column: 2;
                grid-row: 1;
                padding: 0;
                font-size: 17px;
                line-height: 1.2;
                margin: 0 0 var(--space-2, 8px) 0;
                align-self: start;
            }
        }
        
        .ote-organization-grid .wp-block-unbc-organization-field {
            padding: 0 var(--space-4, 16px);
            margin: 0 0 var(--space-4, 16px) 0;
            flex: 1;
        }
        
        .ote-organization-grid .organization-field-content {
            color: var(--text-sec, #64748b);
            font-size: 14px;
            line-height: 1.5;
            display: block;
        }
        
        /* Mobile: Description in second column, second row */
        @media (max-width: 639px) {
            .ote-organization-grid .wp-block-unbc-organization-field {
                grid-column: 2;
                grid-row: 2;
                padding: 0;
                margin: 0 0 var(--space-3, 12px) 0;
                flex: 1;
            }
            
            .ote-organization-grid .organization-field-content {
                font-size: 12px;
                line-height: 1.4;
            }
        }
        
        .ote-organization-grid .wp-block-buttons {
            padding: 0 var(--space-4, 16px) var(--space-4, 16px);
            margin: 0;
            margin-top: auto;
        }
        
        .ote-organization-grid .wp-block-button {
            margin: 0;
        }
        
        .ote-organization-grid .wp-block-button__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(180deg, #475569, #334155);
            color: #fff;
            border: 1px solid color-mix(in oklab, #1e293b, black 25%);
            border-radius: 10px;
            padding: 10px 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            width: 100%;
            transition: all 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        /* Add arrow icon */
        .ote-organization-grid .wp-block-button__link::after {
            content: "→";
            margin-left: 2px;
            transition: transform 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-organization-grid .wp-block-button__link:hover {
            background: linear-gradient(180deg, #64748b, #475569);
            border-color: color-mix(in oklab, #1e293b, black 25%);
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(0,0,0,.18);
        }
        
        .ote-organization-grid .wp-block-button__link:hover::after {
            transform: translateX(3px);
        }
        
        .ote-organization-grid .wp-block-button__link:active {
            transform: translateY(1px) scale(0.99);
        }
        
        .ote-organization-grid .wp-block-button__link:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(128, 128, 128, 0.3) !important;
        }
        
        .ote-organization-grid .wp-block-button__link:focus-visible {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(128, 128, 128, 0.3) !important;
        }
        
        /* Mobile: Button in second column, third row */
        @media (max-width: 639px) {
            .ote-organization-grid .wp-block-buttons {
                grid-column: 2;
                grid-row: 3;
                padding: 0;
                margin: 0;
                align-self: end;
            }
            
            .ote-organization-grid .wp-block-button__link {
                padding: 6px 8px;
                font-size: 13px;
            }
        }
        
        /* Organization Search Bar */
        .ote-org-search-wrapper {
            margin-bottom: var(--space-6, 24px);
            position: relative;
        }
        
        .ote-org-search-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Search bar alignment options */
        .ote-org-search--align-left .ote-org-search-container {
            margin: 0;
        }
        
        .ote-org-search--align-right .ote-org-search-container {
            margin: 0 0 0 auto;
        }
        
        /* Inline style for header integration */
        .ote-org-search--inline {
            margin-bottom: 0;
            display: inline-block;
        }
        
        .ote-org-search--inline .ote-org-search-container {
            max-width: 300px;
        }
        
        .ote-org-search--inline .ote-org-search-field {
            border-radius: 999px;
            background: color-mix(in oklab, var(--muted, #f8fafc), transparent 10%);
        }
        
        .ote-org-search--inline .ote-org-search-input {
            padding: 10px 14px 10px 42px;
            font-size: 14px;
        }
        
        .ote-org-search--inline .ote-org-search-icon {
            width: 18px;
            height: 18px;
            left: 14px;
        }
        
        .ote-org-search--inline .ote-org-search-count {
            display: none;
        }
        
        /* Compact style */
        .ote-org-search--compact {
            margin-bottom: var(--space-4, 16px);
        }
        
        .ote-org-search--compact .ote-org-search-container {
            max-width: 400px;
        }
        
        .ote-org-search--compact .ote-org-search-input {
            padding: 10px 14px 10px 42px;
            font-size: 14px;
        }
        
        .ote-org-search-field {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--surface, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: var(--radius-m, 14px);
            padding: 0;
            overflow: hidden;
            transition: border-color 240ms cubic-bezier(.2,.6,.2,1), box-shadow 240ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-org-search-field:focus-within {
            border-color: var(--brand, #2d5f3f);
            box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand, #2d5f3f), transparent 85%);
        }
        
        .ote-org-search-icon {
            position: absolute;
            left: 16px;
            color: var(--text-sec, #64748b);
            pointer-events: none;
        }
        
        .ote-org-search-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 16px;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text, #1e293b);
        }
        
        .ote-org-search-input::placeholder {
            color: var(--text-sec, #64748b);
        }
        
        .ote-org-search-count {
            position: absolute;
            right: 16px;
            font-size: 13px;
            color: var(--text-sec, #64748b);
            white-space: nowrap;
            pointer-events: none;
        }
        
        /* No results message */
        .ote-no-results {
            text-align: center;
            padding: var(--space-10, 40px) var(--space-4, 16px);
            color: var(--text-sec, #64748b);
        }
        
        .ote-no-results p {
            margin: 0 0 var(--space-2, 8px);
            font-size: 18px;
            color: var(--text, #1e293b);
        }
        
        .ote-no-results-hint {
            font-size: 14px;
            color: var(--text-sec, #64748b) !important;
        }
        
        /* Mobile adjustments */
        @media (max-width: 639px) {
            .ote-org-search-wrapper {
                margin-bottom: var(--space-4, 16px);
            }
            
            .ote-org-search-input {
                padding: 12px 16px 12px 44px;
                font-size: 15px;
            }
            
            .ote-org-search-count {
                display: none;
            }
            
            .ote-org-search-icon {
                width: 18px;
                height: 18px;
                left: 14px;
            }
        }
        
        /* Dark mode support for search */
        [data-theme="dark"] .ote-org-search-field {
            background: var(--surface);
            border-color: var(--border);
        }
        
        [data-theme="dark"] .ote-org-search-input {
            color: var(--text);
        }
        
        /* Organization Detail Page Styling */
        .single-organization .wp-block-group[data-align="full"] {
            background: linear-gradient(135deg, 
                        color-mix(in oklab, var(--brand, #2d5f3f), transparent 95%), 
                        color-mix(in oklab, var(--brand-2, #4a7c59), transparent 95%));
        }
        
        /* Organization Hero Section */
        .single-organization .wp-block-post-featured-image {
            display: flex;
            justify-content: center;
            margin-bottom: var(--space-6, 24px);
        }
        
        .single-organization .wp-block-post-featured-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--surface, #ffffff);
            box-shadow: var(--elev-2, 0 8px 24px rgba(0,0,0,.10));
        }
        
        .single-organization .wp-block-post-title {
            text-align: center;
            font-family: var(--font-serif, serif);
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            line-height: 1.1;
            margin: 0 0 var(--space-4, 16px);
            color: var(--text, #1e293b);
        }
        
        .single-organization .wp-block-unbc-organization-field:first-of-type {
            text-align: center;
            font-size: 1.125rem;
            line-height: 1.5;
            margin: 0 0 var(--space-8, 32px);
            color: var(--text-sec, #64748b);
        }
        
        /* Organization Links Section */
        .single-organization .wp-block-group .wp-block-unbc-organization-field[data-field="org_website"],
        .single-organization .wp-block-group .wp-block-unbc-organization-field[data-field="org_contact_email"] {
            display: inline-block;
            margin-right: var(--space-4, 16px);
        }
        
        .single-organization .wp-block-group .wp-block-unbc-organization-field a {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2, 8px);
            padding: var(--space-2, 8px) var(--space-4, 16px);
            background: var(--brand, #2d5f3f);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-l, 20px);
            font-weight: 500;
            transition: var(--trans-fast, 160ms cubic-bezier(.2,.6,.2,1));
        }
        
        .single-organization .wp-block-group .wp-block-unbc-organization-field a:hover {
            background: var(--brand-2, #4a7c59);
            transform: translateY(-1px);
        }
        
        /* Organization Info Cards */
        .single-organization main > .wp-block-group:nth-child(2) {
            padding: var(--space-12, 48px) 0;
            background: none !important;
        }
        
        .single-organization main > .wp-block-group:nth-child(2) > .wp-block-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-6, 24px);
        }
        
        /* Individual info cards with icon integration */
        .single-organization .wp-block-group[data-metadata-name="Club Info"],
        .single-organization .wp-block-group[data-metadata-name="Socials"],
        .single-organization .wp-block-group[data-metadata-name="Executives"] {
            background: var(--surface, #ffffff);
            border-radius: var(--radius-m, 14px);
            padding: var(--space-6, 24px);
            box-shadow: var(--elev-1, 0 2px 8px rgba(0,0,0,.08));
        }
        
        /* Icon containers */
        .single-organization .wp-block-group[data-metadata-name*="Icon"] {
            width: 48px;
            height: 48px;
            background: color-mix(in oklab, var(--brand, #2d5f3f), transparent 90%);
            border-radius: var(--radius-s, 10px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand, #2d5f3f);
            flex-shrink: 0;
            margin-bottom: var(--space-4, 16px);
        }
        
        .single-organization .wp-block-group[data-metadata-name*="Icon"] svg {
            width: 24px;
            height: 24px;
        }
        
        /* Card headings */
        .single-organization .wp-block-group[data-metadata-name="Club Info"] h2,
        .single-organization .wp-block-group[data-metadata-name="Socials"] h2,
        .single-organization .wp-block-group[data-metadata-name="Executives"] h2 {
            font-weight: 600;
            margin: 0 0 var(--space-4, 16px);
            color: var(--text, #1e293b);
        }
        
        /* Social Media Layout - each social link with its icon */
        .single-organization .wp-block-group[data-metadata-name="Social Media"] > .wp-block-group {
            display: flex;
            align-items: center;
            gap: var(--space-3, 12px);
            margin-bottom: var(--space-3, 12px);
            padding: var(--space-2, 8px);
            border-radius: var(--radius-s, 10px);
            background: color-mix(in oklab, var(--muted, #f8fafc), transparent 50%);
        }
        
        .single-organization .wp-block-group[data-metadata-name="Social Media"] .wp-block-unbc-organization-field {
            flex: 1;
        }
        
        .single-organization .wp-block-group[data-metadata-name="Social Media"] .wp-block-unbc-organization-field a {
            font-size: 0.875rem;
            padding: var(--space-2, 8px) var(--space-3, 12px);
            background: var(--brand, #2d5f3f);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-xs, 6px);
            transition: var(--trans-fast, 160ms cubic-bezier(.2,.6,.2,1));
            display: inline-block;
        }
        
        .single-organization .wp-block-group[data-metadata-name="Social Media"] .wp-block-unbc-organization-field a:hover {
            background: var(--brand-2, #4a7c59);
        }
        
        .single-organization .wp-block-group[data-metadata-name="Social Media"] .wp-block-shortcode {
            width: 24px;
            height: 24px;
            color: var(--brand, #2d5f3f);
            flex-shrink: 0;
        }
        
        .single-organization .wp-block-group[data-metadata-name="Social Media"] .wp-block-shortcode svg {
            width: 24px;
            height: 24px;
        }
        
        /* Content and Calendar section layout */
        .single-organization main > .wp-block-group:nth-child(3) {
            padding: var(--space-12, 48px) 0;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: var(--space-10, 40px);
            align-items: start;
        }
        
        .single-organization .wp-block-group[data-metadata-name="Overview"] h2 {
            font-family: var(--font-serif, serif);
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0 0 var(--space-5, 20px);
            color: var(--text, #1e293b);
        }
        
        .single-organization .wp-block-post-content {
            line-height: 1.7;
            color: var(--text, #1e293b);
        }
        
        .single-organization .wp-block-post-content p {
            margin: 0 0 var(--space-4, 16px);
        }
        
        /* Calendar sidebar styling */
        .single-organization .wp-block-group[data-metadata-name="Calendar"] {
            background: var(--surface, #ffffff);
            border-radius: var(--radius-m, 14px);
            padding: var(--space-6, 24px);
            box-shadow: var(--elev-1, 0 2px 8px rgba(0,0,0,.08));
            position: sticky;
            top: var(--space-6, 24px);
        }
        
        /* Hide cards with no content */
        .single-organization .wp-block-group[data-metadata-name="Club Info"]:not(:has(.organization-field-content:not(:empty))),
        .single-organization .wp-block-group[data-metadata-name="Socials"]:not(:has(.organization-field-content:not(:empty))),
        .single-organization .wp-block-group[data-metadata-name="Executives"]:not(:has(.organization-field-content:not(:empty))) {
            display: none;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            .single-organization main > .wp-block-group:nth-child(2) > .wp-block-group {
                grid-template-columns: 1fr;
                gap: var(--space-4, 16px);
            }
            
            .single-organization .wp-block-group[data-metadata-name="Club Info"],
            .single-organization .wp-block-group[data-metadata-name="Socials"],
            .single-organization .wp-block-group[data-metadata-name="Executives"] {
                padding: var(--space-4, 16px);
            }
            
            .single-organization main > .wp-block-group:nth-child(3) {
                grid-template-columns: 1fr;
                gap: var(--space-6, 24px);
            }
            
            .single-organization .wp-block-group[data-metadata-name="Calendar"] {
                order: -1;
                position: static;
                margin-bottom: var(--space-6, 24px);
            }
            
            .single-organization .wp-block-post-featured-image img {
                width: 90px;
                height: 90px;
                border-width: 3px;
            }
            
            .single-organization .wp-block-post-title {
                font-size: clamp(1.75rem, 4vw, 2.25rem);
            }
        }
        
        /* Dark mode support for organization cards */
        [data-theme="dark"] .ote-organization-grid .wp-block-post-template > li {
            background: var(--surface);
            border-color: var(--border);
        }
        
        [data-theme="dark"] .ote-organization-grid .wp-block-post-template > li:hover {
            border-color: var(--text-sec);
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.1), 0 0 0 1px var(--text-sec);
        }
        
        
        [data-theme="dark"] .ote-organization-grid .wp-block-post-title a {
            color: var(--text);
        }
        
        [data-theme="dark"] .ote-organization-grid .wp-block-post-title a:hover {
            color: var(--text);
            opacity: 0.8;
        }
        
        [data-theme="dark"] .ote-organization-grid .organization-field-content {
            color: var(--text-sec);
        }
        
        /* Dark mode button styles for organization grid */
        [data-theme="dark"] .ote-organization-grid .wp-block-button__link {
            background: linear-gradient(180deg, #e2e8f0, #cbd5e1) !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
            box-shadow: 0 8px 24px rgba(0,0,0,.2) !important;
        }
        
        [data-theme="dark"] .ote-organization-grid .wp-block-button__link:hover {
            background: linear-gradient(180deg, #f1f5f9, #e2e8f0) !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            filter: brightness(1.05);
            box-shadow: 0 12px 32px rgba(0,0,0,.25) !important;
        }
        
        [data-theme="dark"] .ote-organization-grid .wp-block-button__link:active {
            transform: translateY(1px) scale(0.99);
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .ote-organization-grid .wp-block-post-template {
                grid-template-columns: 1fr;
                gap: var(--space-4, 16px);
            }
        }
        
        /* Mobile Menu Sheet (Centered Modal) */
        .mobile-menu-sheet {
            position: fixed;
            inset: 0;
            background: color-mix(in oklab, var(--bg, #ffffff), transparent 35%);
            backdrop-filter: blur(4px);
            display: grid;
            place-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 240ms cubic-bezier(.2,.6,.2,1);
            z-index: 100;
        }
        
        [data-theme="dark"] .mobile-menu-sheet {
            background: color-mix(in oklab, var(--bg, #0f172a), transparent 35%);
        }
        
        .mobile-menu-sheet[data-open="true"] {
            opacity: 1;
            pointer-events: auto;
        }
        
        .mobile-menu-sheet__panel {
            width: min(560px, 92vw);
            max-height: min(80vh, 640px);
            background: var(--surface, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            box-shadow: 0 16px 40px rgba(0,0,0,.16);
            border-radius: 16px;
            padding: 24px 20px 32px;
            display: grid;
            gap: 20px;
            transform: translateY(12px) scale(.98);
            opacity: 0;
            transition: transform 240ms cubic-bezier(.2,.6,.2,1), opacity 240ms cubic-bezier(.2,.6,.2,1);
        }
        
        [data-theme="dark"] .mobile-menu-sheet__panel {
            background: var(--surface, #1e293b);
            border-color: var(--border, #334155);
            box-shadow: 0 16px 40px rgba(0,0,0,.3);
        }
        
        .mobile-menu-sheet[data-open="true"] .mobile-menu-sheet__panel {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        
        .mobile-menu-sheet__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        
        .brand__logo {
            display: flex;
            align-items: center;
            color: var(--text, #1e293b);
        }
        
        [data-theme="dark"] .brand__logo {
            color: var(--text, #ffffff);
        }
        
        .brand__name {
            letter-spacing: -0.01em;
            color: var(--text, #1e293b);
        }
        
        [data-theme="dark"] .brand__name {
            color: var(--text, #ffffff);
        }
        
        .icon-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 160ms cubic-bezier(.2,.6,.2,1), 
                        background 160ms cubic-bezier(.2,.6,.2,1), 
                        color 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        .icon-btn:hover {
            background: var(--muted, #f8fafc);
        }
        
        .icon-btn:active {
            transform: translateY(1px);
        }
        
        [data-theme="dark"] .icon-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .mobile-menu-sheet__nav {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .mm-link {
            display: block;
            padding: 12px 14px;
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 12px;
            background: var(--surface, #ffffff);
            color: var(--text, #1e293b);
            text-decoration: none;
            transition: background 160ms cubic-bezier(.2,.6,.2,1), 
                        border-color 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        .mm-link:hover {
            background: var(--muted, #f8fafc);
        }
        
        .mm-link.current-menu-item {
            background: rgba(128, 128, 128, 0.1);
            font-weight: 600;
        }
        
        [data-theme="dark"] .mm-link {
            border-color: var(--border, #334155);
            background: var(--surface, #1e293b);
            color: var(--text, #ffffff);
        }
        
        [data-theme="dark"] .mm-link:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        [data-theme="dark"] .mm-link.current-menu-item {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .mobile-menu-sheet__actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 20px 0;
        }
        
        .mobile-menu-theme-toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            cursor: pointer;
            overflow: hidden;
            transition: all 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        [data-theme="dark"] .mobile-menu-theme-toggle {
            border-color: #334155;
            color: #ffffff;
        }
        
        .mobile-menu-theme-toggle:hover {
            background: rgba(128, 128, 128, 0.1);
            transform: translateY(-1px);
        }
        
        [data-theme="dark"] .mobile-menu-theme-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        
        /* Hide on desktop */
        @media (min-width: 900px) {
            .mobile-menu-sheet {
                display: none;
            }
        }
        
        /* Hide WordPress default mobile menu */
        .wp-block-navigation__responsive-container.is-menu-open {
            display: none;
        }
        
        /* OTE Logo Shortcode Styles */
        .ote-logo-wrapper {
            display: inline-block;
            line-height: 0;
        }
        
        .ote-logo-wrapper.ote-logo--align-center {
            text-align: center;
            display: block;
        }
        
        .ote-logo-wrapper.ote-logo--align-right {
            text-align: right;
            display: block;
        }
        
        .ote-logo-link {
            display: inline-block;
            text-decoration: none;
            outline: none;
            transition: opacity 160ms cubic-bezier(.2,.6,.2,1);
        }
        
        .ote-logo-link:hover {
            opacity: 0.8;
        }
        
        .ote-logo {
            display: block;
            max-width: 100%;
            height: auto;
        }
        
        /* Dark mode support for logo text - matches documentation approach */
        .ote-logo .logo-text path {
            fill: var(--text, #282828) !important;
        }
        
        [data-theme="dark"] .ote-logo .logo-text path {
            fill: var(--text, hsl(210, 40%, 98%)) !important;
        }
        
        
        /* Full logo layout */
        .ote-logo-full {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 640px) {
            .ote-logo-full {
                flex-direction: column;
                text-align: center;
                gap: 8px !important;
            }
            
            .ote-logo-full .ote-logo-symbol {
                margin-bottom: 4px;
            }
        }
        
        /* Logo responsiveness */
        @media (max-width: 480px) {
            .ote-logo-wrapper.ote-logo--text .ote-logo,
            .ote-logo-wrapper.ote-logo--full .ote-logo-full {
                max-width: 100%;
                width: 100%;
            }
            
            .ote-logo-wrapper.ote-logo--symbol .ote-logo {
                max-height: 80px;
            }
        }
    </style>
    
    <script>
        // Initialize theme immediately to prevent flash
        (function() {
            function getCookie(name) {
                const value = "; " + document.cookie;
                const parts = value.split("; " + name + "=");
                if (parts.length === 2) return parts.pop().split(";").shift();
                return null;
            }
            
            function getSystemTheme() {
                return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            
            const storedTheme = getCookie('ote_theme') || 'system';
            const resolvedTheme = storedTheme === 'system' ? getSystemTheme() : storedTheme;
            
            document.documentElement.setAttribute('data-theme', resolvedTheme);
            document.documentElement.dataset.themeMode = storedTheme;
            
            console.log('OTE Theme initialized:', resolvedTheme, 'from stored:', storedTheme);
        })();
    </script>
    <?php
}
add_action('wp_head', 'ote_child_inline_styles');

/**
 * Add admin notices for shortcode info
 */
function ote_child_admin_notices() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen && in_array($screen->base, ['post', 'edit'])) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>OTE Dark Mode Toggle:</strong> Use the shortcode <code>[dark_mode_toggle]</code> to add a dark mode toggle anywhere.</p>
            <p><strong>With options:</strong> <code>[dark_mode_toggle style="pill" size="large" show_label="true" align="center"]</code></p>
            <hr style="margin: 12px 0;">
            <p><strong>OTE Logo:</strong> Use the shortcode <code>[ote_logo]</code> to add the OTE logo.</p>
            <p><strong>Logo options:</strong> <code>[ote_logo type="text" size="200" link="true" align="center" variant="white"]</code></p>
            <p><strong>Logo types:</strong> <code>symbol</code> (icon only), <code>text</code> (full logo without subtext), <code>full</code> (full logo with subtext)</p>
            <p><strong>Variant option:</strong> <code>variant="default"</code> or <code>variant="white"</code> (for text type only)</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ote_child_admin_notices');

/**
 * Add custom mobile menu
 */
function ote_mobile_menu_html() {
    ?>
    <div id="mobile-menu-sheet" class="mobile-menu-sheet" role="dialog" aria-modal="true" aria-label="Navigation" data-open="false">
        <div class="mobile-menu-sheet__panel">
            <div class="mobile-menu-sheet__head">
                <div class="brand">
                    <span class="brand__logo" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                            <rect x="4" y="4" width="40" height="40" rx="10" fill="currentColor" opacity="0.1"></rect>
                            <circle cx="24" cy="24" r="12" fill="none" stroke="currentColor" stroke-width="3"></circle>
                            <path d="M18 24h12" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                    </span>
                    <span class="brand__name"><?php bloginfo('name'); ?></span>
                </div>
                <button class="icon-btn" aria-label="Close menu" data-mm-close>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="mobile-menu-sheet__nav" aria-label="Primary">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => false,
                        'depth' => 1,
                        'fallback_cb' => false,
                        'walker' => new OTE_Mobile_Menu_Sheet_Walker()
                    ));
                } else {
                    // Fallback menu if no menu is assigned
                    $pages = get_pages(array('sort_column' => 'menu_order', 'number' => 5));
                    foreach ($pages as $page) {
                        echo '<a href="' . get_page_link($page->ID) . '" class="mm-link">' . $page->post_title . '</a>';
                    }
                }
                ?>
            </nav>
            <div class="mobile-menu-sheet__actions">
                <button type="button" class="theme-toggle mobile-menu-theme-toggle" aria-label="Toggle dark/light theme">
                    <span class="theme-toggle__icon theme-toggle__icon--sun">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                        </svg>
                    </span>
                    <span class="theme-toggle__icon theme-toggle__icon--moon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"/>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'ote_mobile_menu_html');

/**
 * Custom Walker for Mobile Menu Sheet
 */
class OTE_Mobile_Menu_Sheet_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        // No nested lists for this design
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $is_current = in_array('current-menu-item', $classes) || in_array('current_page_item', $classes);
        
        $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ' class="mm-link' . ($is_current ? ' current-menu-item' : '') . '"';
        
        $output .= '<a' . $attributes . '>';
        $output .= apply_filters('the_title', $item->title, $item->ID);
        $output .= '</a>';
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        // No closing tags needed for this structure
    }
}

/**
 * Override WordPress navigation block mobile behavior
 */
function ote_override_navigation_block_mobile() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide WordPress mobile menu overlay when our custom menu is present
        const wpNavContainer = document.querySelector('.wp-block-navigation__responsive-container');
        const customMenu = document.getElementById('ote-mobile-menu');
        
        if (wpNavContainer && customMenu) {
            // Prevent WordPress mobile menu from opening
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const target = mutation.target;
                        if (target.classList.contains('is-menu-open')) {
                            target.classList.remove('is-menu-open');
                            target.classList.remove('has-modal-open');
                        }
                    }
                });
            });
            
            observer.observe(wpNavContainer, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'ote_override_navigation_block_mobile');

/**
 * Add admin tools for cleaning broken blocks
 */
function ote_child_admin_tools() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if we need to show cleanup notice
    if (isset($_GET['ote_cleanup']) && $_GET['ote_cleanup'] === 'blocks') {
        ?>
        <div class="notice notice-info">
            <p><strong>OTE Theme:</strong> To remove broken blocks from posts, you can:</p>
            <ol>
                <li>Edit the post/page in the block editor</li>
                <li>Click on the broken block</li>
                <li>Choose "Remove Block" or convert it to HTML</li>
                <li>Replace with the working "Dark Mode Toggle (Simple)" block</li>
            </ol>
            <p>Or use this quick fix: <a href="?ote_fix_blocks=1&_wpnonce=<?php echo wp_create_nonce('ote_fix_blocks'); ?>" class="button">Auto-fix broken dark mode blocks</a></p>
        </div>
        <?php
    }
    
    // Handle auto-fix
    if (isset($_GET['ote_fix_blocks']) && wp_verify_nonce($_GET['_wpnonce'], 'ote_fix_blocks')) {
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'any',
            'numberposts' => -1,
            's' => 'ote/dark-mode-toggle'
        ));
        
        $fixed = 0;
        foreach ($posts as $post) {
            $content = $post->post_content;
            $original = $content;
            
            // Replace broken dark mode toggle blocks with simple ones
            $content = preg_replace(
                '/<!-- wp:ote\/dark-mode-toggle.*?-->(.*?)<!-- \/wp:ote\/dark-mode-toggle -->/s',
                '<!-- wp:ote/dark-mode-toggle-simple --><!-- /wp:ote/dark-mode-toggle-simple -->',
                $content
            );
            
            if ($content !== $original) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_content' => $content
                ));
                $fixed++;
            }
        }
        
        ?>
        <div class="notice notice-success">
            <p><strong>OTE Theme:</strong> Fixed <?php echo $fixed; ?> posts with broken dark mode blocks.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ote_child_admin_tools');

/**
 * Enqueue app-bar navigation assets
 */
function ote_enqueue_app_bar_assets() {
    wp_enqueue_style(
        'ote-app-bar-nav',
        get_stylesheet_directory_uri() . '/assets/css/app-bar-nav.css',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/css/app-bar-nav.css')
    );
    
    wp_enqueue_script(
        'ote-app-bar-nav',
        get_stylesheet_directory_uri() . '/assets/js/app-bar-nav.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/app-bar-nav.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'ote_enqueue_app_bar_assets');

/**
 * Add script to update mobile menu logo with shortcode
 */
function ote_mobile_menu_logo_script() {
    $logo_html = do_shortcode('[ote_logo type="full" size="150"]');
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update existing mobile menu logo
        const brandSection = document.querySelector('.mobile-menu-sheet__head .brand .brand__logo');
        if (brandSection) {
            brandSection.innerHTML = <?php echo json_encode($logo_html); ?>;
        }
        
        // Remove the brand name text completely
        const brandName = document.querySelector('.mobile-menu-sheet__head .brand .brand__name');
        if (brandName) {
            brandName.style.display = 'none';
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'ote_mobile_menu_logo_script');

