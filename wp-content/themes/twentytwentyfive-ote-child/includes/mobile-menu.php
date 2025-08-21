<?php
/**
 * Mobile Menu Functionality
 * 
 * Handles mobile menu HTML, walker class, and overrides
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mobile Menu HTML
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
                <button class="ote-theme-toggle" id="mobileThemeToggle" aria-live="polite" aria-label="Theme dark">
                    <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                        </svg>
                    </span>
                    <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
 * Custom Walker for Mobile Menu
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
 * Override WordPress Navigation Block Mobile Behavior
 * Fixed: Changed selector from #ote-mobile-menu to #mobile-menu-sheet
 */
function ote_override_navigation_block_mobile() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide WordPress mobile menu overlay when our custom menu is present
        const wpNavContainer = document.querySelector('.wp-block-navigation__responsive-container');
        const customMenu = document.getElementById('mobile-menu-sheet'); // Fixed selector
        
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
 * Mobile Menu Logo Script
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