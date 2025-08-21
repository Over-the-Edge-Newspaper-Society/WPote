<?php
/**
 * Search Sheet Functionality
 * 
 * Handles search sheet HTML and functionality for both mobile and desktop
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Search Sheet HTML (works for both mobile and desktop)
 */
function ote_search_sheet_html() {
    ?>
    <div id="search-sheet" class="search-sheet" role="dialog" aria-modal="true" aria-label="Search" data-open="false">
        <div class="search-sheet__panel">
            <div class="search-sheet__head">
                <h2 class="search-sheet__title">Search</h2>
                <button class="search-sheet__close" aria-label="Close search" data-search-close>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="search-sheet__content">
                <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <span class="search-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                        <input 
                            type="search" 
                            name="s" 
                            class="search-input" 
                            placeholder="Search posts, pages, and more..." 
                            value="<?php echo get_search_query(); ?>"
                            aria-label="Search"
                            autocomplete="off"
                        />
                        <button type="button" class="search-clear" aria-label="Clear search">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <button type="submit" class="search-submit">Search</button>
                </form>
                
                <div class="search-results">
                    <div class="search-empty">
                        <div class="search-empty-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </div>
                        <p>Start typing to search across posts, pages, and more</p>
                    </div>
                    
                    <div class="search-results-content" style="display: none;">
                        <div class="search-section" id="search-posts">
                            <h3 class="search-section__title">Posts</h3>
                            <div class="search-section__items"></div>
                        </div>
                        
                        <div class="search-section" id="search-pages">
                            <h3 class="search-section__title">Pages</h3>
                            <div class="search-section__items"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'ote_search_sheet_html');

/**
 * Enqueue search styles and scripts
 */
function ote_search_assets() {
    // Enqueue CSS
    wp_enqueue_style(
        'ote-search',
        get_stylesheet_directory_uri() . '/assets/css/search.css',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/css/search.css')
    );
    
    // Enqueue JavaScript
    wp_enqueue_script(
        'ote-search',
        get_stylesheet_directory_uri() . '/assets/js/search.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/search.js'),
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('ote-search', 'searchAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ote_search_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'ote_search_assets');

/**
 * AJAX handler for search
 */
function ote_search_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ote_search_nonce')) {
        wp_die('Security check failed');
    }
    
    $search_query = sanitize_text_field($_POST['query']);
    
    if (empty($search_query) || strlen($search_query) < 2) {
        wp_send_json_error('Query too short');
    }
    
    $results = array(
        'posts' => array(),
        'pages' => array()
    );
    
    // Search posts
    $posts = get_posts(array(
        's' => $search_query,
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => 5
    ));
    
    foreach ($posts as $post) {
        $results['posts'][] = array(
            'title' => get_the_title($post),
            'url' => get_permalink($post),
            'excerpt' => wp_trim_words(get_the_excerpt($post), 15, '...')
        );
    }
    
    // Search pages
    $pages = get_posts(array(
        's' => $search_query,
        'post_type' => 'page',
        'post_status' => 'publish',
        'numberposts' => 3
    ));
    
    foreach ($pages as $page) {
        $results['pages'][] = array(
            'title' => get_the_title($page),
            'url' => get_permalink($page),
            'excerpt' => wp_trim_words(get_the_excerpt($page), 15, '...')
        );
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_ote_search', 'ote_search_ajax');
add_action('wp_ajax_nopriv_ote_search', 'ote_search_ajax');