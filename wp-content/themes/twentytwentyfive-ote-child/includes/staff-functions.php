<?php
/**
 * Staff Profile Functions
 * Handles staff-related functionality including article queries
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get articles for a staff member based on their linked author
 * Check if function doesn't already exist (from plugin)
 */
if (!function_exists('ote_get_staff_articles')) {
    function ote_get_staff_articles($staff_id = null) {
        if (!$staff_id) {
            $staff_id = get_the_ID();
        }
        
        // Get the linked author ID from staff meta
        $linked_author_id = get_post_meta($staff_id, '_linked_author_id', true);
        
        if (!$linked_author_id) {
            return array();
        }
        
        // Query posts by the linked author
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'post_status' => 'publish',
            'author' => $linked_author_id,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        return $query->posts;
    }
}

/**
 * Get the article count for a staff member
 * Check if function doesn't already exist (from plugin)
 */
if (!function_exists('ote_get_staff_article_count')) {
    function ote_get_staff_article_count($staff_id = null) {
        if (!$staff_id) {
            $staff_id = get_the_ID();
        }
        
        $linked_author_id = get_post_meta($staff_id, '_linked_author_id', true);
        
        if (!$linked_author_id) {
            return 0;
        }
        
        return count_user_posts($linked_author_id);
    }
}

/**
 * Modify query for staff articles block
 * This hooks into the query loop block when on a staff profile page
 */
function ote_theme_modify_staff_articles_query($query_args, $block, $page) {
    // Only modify on staff_profile single pages
    if (!is_singular('staff_profile')) {
        return $query_args;
    }
    
    // Debug: Check what we're getting
    error_log('Staff Query Debug - Post Type: ' . get_post_type());
    error_log('Staff Query Debug - Block Classes: ' . (isset($block->parsed_block['attrs']['className']) ? $block->parsed_block['attrs']['className'] : 'none'));
    
    // Check if this is the articles query by class name
    $has_staff_class = false;
    if (isset($block->parsed_block['attrs']['className'])) {
        $classes = $block->parsed_block['attrs']['className'];
        if (strpos($classes, 'staff-recent-articles') !== false ||
            strpos($classes, 'ote-news-grid') !== false ||
            strpos($classes, 'staff-articles') !== false) {
            $has_staff_class = true;
        }
    }
    
    // Also check if it's a query block on staff page (fallback)
    if ($has_staff_class || (isset($block->parsed_block['blockName']) && $block->parsed_block['blockName'] === 'core/query')) {
        $staff_id = get_the_ID();
        $linked_author_id = get_post_meta($staff_id, '_linked_author_id', true);
        
        error_log('Staff Query Debug - Staff ID: ' . $staff_id);
        error_log('Staff Query Debug - Linked Author ID: ' . $linked_author_id);
        
        if ($linked_author_id) {
            // Override the query to show posts from the linked author
            $query_args['author'] = intval($linked_author_id);
            $query_args['post_type'] = 'post';
            $query_args['post_status'] = 'publish';
            $query_args['posts_per_page'] = isset($query_args['posts_per_page']) ? $query_args['posts_per_page'] : 10;
            $query_args['orderby'] = 'date';
            $query_args['order'] = 'DESC';
            
            // Remove any inherit settings that might conflict
            unset($query_args['inherit']);
            
            error_log('Staff Query Debug - Modified Query Args: ' . print_r($query_args, true));
        } else {
            // If no linked author, return no posts
            $query_args['post__in'] = array(0); // This will return no posts
        }
    }
    
    return $query_args;
}
add_filter('query_loop_block_query_vars', 'ote_theme_modify_staff_articles_query', 10, 3);

/**
 * Add custom query variable for staff articles
 * This allows the block editor to recognize our custom query
 */
function ote_register_staff_query_vars() {
    // Register custom query var for staff articles
    global $wp;
    $wp->add_query_var('staff_author');
}
add_action('init', 'ote_register_staff_query_vars');

/**
 * Shortcode to display staff articles
 * Usage: [staff_articles count="4"]
 */
function ote_staff_articles_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 4,
        'staff_id' => get_the_ID()
    ), $atts);
    
    $articles = ote_get_staff_articles($atts['staff_id']);
    
    if (empty($articles)) {
        return '<p class="no-articles">No articles found for this staff member.</p>';
    }
    
    ob_start();
    ?>
    <div class="staff-articles-grid">
        <?php foreach (array_slice($articles, 0, $atts['count']) as $article) : ?>
            <article class="staff-article-item">
                <?php if (has_post_thumbnail($article->ID)) : ?>
                    <div class="article-thumbnail">
                        <?php echo get_the_post_thumbnail($article->ID, 'medium'); ?>
                    </div>
                <?php endif; ?>
                <div class="article-content">
                    <h4><a href="<?php echo get_permalink($article->ID); ?>"><?php echo get_the_title($article->ID); ?></a></h4>
                    <div class="article-meta">
                        <time><?php echo get_the_date('', $article->ID); ?></time>
                        <?php 
                        $categories = get_the_category($article->ID);
                        if (!empty($categories)) {
                            echo ' • ' . esc_html($categories[0]->name);
                        }
                        ?>
                    </div>
                    <div class="article-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt($article->ID), 20); ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('staff_articles', 'ote_staff_articles_shortcode');

/**
 * Helper function to check if current staff has articles
 */
if (!function_exists('ote_staff_has_articles')) {
    function ote_staff_has_articles($staff_id = null) {
        if (!$staff_id) {
            $staff_id = get_the_ID();
        }
        
        $linked_author_id = get_post_meta($staff_id, '_linked_author_id', true);
        return !empty($linked_author_id) && count_user_posts($linked_author_id) > 0;
    }
}

/**
 * Get the linked author object for a staff member
 */
if (!function_exists('ote_get_staff_linked_author')) {
    function ote_get_staff_linked_author($staff_id = null) {
        if (!$staff_id) {
            $staff_id = get_the_ID();
        }
        
        $linked_author_id = get_post_meta($staff_id, '_linked_author_id', true);
        
        if ($linked_author_id) {
            return get_user_by('id', $linked_author_id);
        }
        
        return false;
    }
}