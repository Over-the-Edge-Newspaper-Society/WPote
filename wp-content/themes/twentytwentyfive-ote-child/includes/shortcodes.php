<?php
/**
 * Shortcodes
 * 
 * All theme shortcodes and their helper functions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dark Mode Toggle Shortcode
 * Usage: [dark_mode_toggle] or [dark_mode_toggle style="icon" show_label="true"]
 */
function ote_dark_mode_toggle_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'icon',  // icon, button, pill, segmented
        'id' => 'themeToggle', // button ID
        'show_label' => 'false' // true, false
    ), $atts);
    
    $show_label = $atts['show_label'] === 'true';
    
    $sun_icon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
        </svg>';
    
    $moon_icon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
        </svg>';
        
    ob_start();
    ?>
    <button type="button" 
            id="<?php echo esc_attr($atts['id']); ?>" 
            class="ote-theme-toggle theme-toggle" 
            aria-label="Toggle theme">
        <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">
          <?php echo $sun_icon; ?>
        </span>
        <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">
          <?php echo $moon_icon; ?>
        </span>
        <?php if ($show_label): ?>
          <span class="theme-toggle__label"><?php _e('Toggle Theme', 'ote-child-theme'); ?></span>
        <?php endif; ?>
    </button>
    <?php
    return ob_get_clean();
}
add_shortcode('dark_mode_toggle', 'ote_dark_mode_toggle_shortcode');

/**
 * Search Toggle Shortcode (works for both mobile and desktop)
 * Usage: [search_toggle] or [search_toggle style="icon" show_label="false"]
 */
function ote_search_toggle_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'icon',  // icon, button
        'id' => 'searchToggle', // button ID
        'show_label' => 'false', // true, false
        'mobile_only' => 'false' // true, false - if true, only shows on mobile
    ), $atts);
    
    $show_label = $atts['show_label'] === 'true';
    $mobile_only = $atts['mobile_only'] === 'true';
    
    $search_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7"/>
            <path d="M21 21l-4.35-4.35"/>
        </svg>';
    
    // Use same classes but let CSS handle visibility
    $css_class = 'search-toggle';
    if ($mobile_only) {
        $css_class .= ' mobile-only';
    }
        
    ob_start();
    ?>
    <button type="button" 
            id="<?php echo esc_attr($atts['id']); ?>" 
            class="<?php echo esc_attr($css_class); ?>" 
            aria-label="Search"
            data-search-open="true">
        <span class="search-toggle__icon" aria-hidden="true">
          <?php echo $search_icon; ?>
        </span>
        <?php if ($show_label): ?>
          <span class="search-toggle__label"><?php _e('Search', 'ote-child-theme'); ?></span>
        <?php endif; ?>
    </button>
    <?php
    return ob_get_clean();
}
add_shortcode('search_toggle', 'ote_search_toggle_shortcode');

/**
 * Mobile Search Toggle Shortcode (backward compatibility)
 * Usage: [mobile_search_toggle] or [mobile_search_toggle style="icon" show_label="false"]
 */
function ote_mobile_search_toggle_shortcode($atts) {
    // Force mobile_only to true for backward compatibility
    $atts['mobile_only'] = 'true';
    return ote_search_toggle_shortcode($atts);
}
add_shortcode('mobile_search_toggle', 'ote_mobile_search_toggle_shortcode');

/**
 * Organization Search Shortcode
 * Usage: [organization_search placeholder="Search..." target=".grid-selector"]
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
 * Icon Shortcode
 * Usage: [icon name="users" size="24" color="currentColor"]
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
 * Logo Shortcode
 * Usage: [ote_logo type="symbol" size="100" link="true"]
 */
function ote_logo_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'symbol',     // symbol, text, full
        'size' => '100',        // Size in pixels (height for symbol, width for text/full)
        'link' => 'false',      // true, false - wrap in home link
        'align' => 'left',      // left, center, right
        'valign' => 'center',   // top, center, bottom - vertical alignment
        'class' => '',          // Additional CSS classes
        'variant' => 'default', // default, white - color variant
    ), $atts);
    
    $wrapper_classes = array(
        'ote-logo-wrapper',
        'ote-logo--' . $atts['type'],
        'ote-logo--align-' . $atts['align'],
        'ote-logo--valign-' . $atts['valign']
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
add_shortcode('ote_logo', 'ote_logo_shortcode');

/**
 * Helper: Get Symbol Logo SVG
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
 * Helper: Get Text Logo SVG
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
 * Helper: Get Full Logo SVG
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

/**
 * Category Filters Shortcode
 * Usage: [category_filters post_type="post" taxonomy="category" style="chips"]
 */
function ote_category_filters_shortcode($atts) {
    // Enqueue the category filters script when shortcode is used
    $stylesheet_dir = get_stylesheet_directory();
    $stylesheet_uri = get_stylesheet_directory_uri();
    $category_filters_js = $stylesheet_dir . '/assets/js/category-filters.js';
    
    if (file_exists($category_filters_js)) {
        wp_enqueue_script(
            'ote-category-filters',
            $stylesheet_uri . '/assets/js/category-filters.js',
            array(),
            filemtime($category_filters_js),
            true
        );
    }
    $atts = shortcode_atts(array(
        'post_type' => 'post',     // Post type to filter
        'taxonomy' => 'category',   // Taxonomy to use for filtering
        'show_all' => 'true',      // Show "All" option
        'all_text' => 'All',       // Text for "All" button
        'style' => 'chips',        // chips, tabs
        'target' => '.wp-block-query', // CSS selector for query loop to filter
        'ajax' => 'false',         // Enable AJAX filtering (requires custom JS)
        'categories' => '',        // Comma-separated category slugs (empty = all)
        'show_count' => 'false',   // Show post count in each filter
        'dynamic_categories' => 'false' // Only show categories that exist in current query
    ), $atts);

    $taxonomy = $atts['taxonomy'];
    $post_type = $atts['post_type'];
    $show_all = ($atts['show_all'] === 'true');
    $style = $atts['style'];
    $target = $atts['target'];
    $ajax_enabled = ($atts['ajax'] === 'true');
    $show_count = ($atts['show_count'] === 'true');
    $dynamic_categories = ($atts['dynamic_categories'] === 'true');

    // Get categories
    $categories = array();
    if ($dynamic_categories) {
        // Get categories that exist in the current query's posts
        $categories = ote_get_dynamic_categories_for_query($taxonomy, $post_type);
    } elseif (!empty($atts['categories'])) {
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
        $debug_info = '';
        if (current_user_can('manage_options')) {
            $debug_info = '<br><small>Debug: dynamic=' . ($dynamic_categories ? 'yes' : 'no') . ', taxonomy=' . $taxonomy . ', post_type=' . $post_type . '</small>';
        }
        return '<p>No categories found.' . $debug_info . '</p>';
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
 * Article Read Time Shortcode
 * Usage: [article_read_time] or [article_read_time post_id="123" words_per_minute="200"]
 */
function ote_article_read_time_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => '',           // Optional post ID, defaults to current post
        'words_per_minute' => 200, // Average reading speed
        'format' => 'short',       // short (4 min read) or long (4 minutes to read)
        'show_icon' => 'false'     // Show clock icon before text
    ), $atts);
    
    // Get the post ID
    $post_id = !empty($atts['post_id']) ? intval($atts['post_id']) : get_the_ID();
    
    if (!$post_id) {
        return '';
    }
    
    // Get post content
    $post = get_post($post_id);
    if (!$post) {
        return '';
    }
    
    // Strip HTML tags and shortcodes, then count words
    $content = strip_shortcodes($post->post_content);
    $content = wp_strip_all_tags($content);
    $word_count = str_word_count($content);
    
    // Calculate reading time in minutes
    $reading_time = ceil($word_count / intval($atts['words_per_minute']));
    
    // Ensure minimum of 1 minute
    $reading_time = max(1, $reading_time);
    
    // Format the output
    $output = '';
    
    if ($atts['show_icon'] === 'true') {
        $clock_icon = '<svg class="ote-read-time-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>';
        $output .= $clock_icon;
    }
    
    if ($atts['format'] === 'long') {
        $output .= sprintf(
            _n('%d minute to read', '%d minutes to read', $reading_time, 'ote-child-theme'),
            $reading_time
        );
    } else {
        $output .= sprintf('%d min read', $reading_time);
    }
    
    return '<span class="ote-article-read-time">' . $output . '</span>';
}
add_shortcode('article_read_time', 'ote_article_read_time_shortcode');

/**
 * Helper function to get categories that exist in recent posts of the specified type
 */
function ote_get_dynamic_categories_for_query($taxonomy = 'category', $post_type = 'post') {
    // Get recent posts of the specified type to determine which categories are in use
    $recent_posts = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => 100, // Get more posts to ensure we capture all relevant categories
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (empty($recent_posts)) {
        // If no posts found, fallback to all categories
        return get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));
    }
    
    // Collect all category IDs from these posts
    $category_ids = array();
    foreach ($recent_posts as $post) {
        $post_categories = wp_get_post_terms($post->ID, $taxonomy, array('fields' => 'ids'));
        if (!is_wp_error($post_categories) && !empty($post_categories)) {
            $category_ids = array_merge($category_ids, $post_categories);
        }
    }
    
    // Remove duplicates
    $category_ids = array_unique($category_ids);
    
    if (empty($category_ids)) {
        // If no categories found on posts, fallback to all categories
        return get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));
    }
    
    // Get the actual category objects
    $categories = get_terms(array(
        'taxonomy' => $taxonomy,
        'include' => $category_ids,
        'hide_empty' => false,
    ));
    
    return is_wp_error($categories) ? array() : $categories;
}