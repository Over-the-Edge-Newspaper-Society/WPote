<?php
class UNBC_Events_Post_Types {
    public function __construct() {
        // Register immediately since we're called from init already
        $this->register_post_types();
        $this->register_taxonomies();
        
        add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_events'), 10, 2);
        add_filter('post_link', array($this, 'custom_organization_permalink'), 10, 2);
        add_filter('post_type_link', array($this, 'custom_organization_permalink'), 10, 2);
        add_action('add_meta_boxes', array($this, 'add_featured_image_support'));
        add_action('pre_get_posts', array($this, 'restrict_organization_manager_posts'));
        add_action('admin_menu', array($this, 'modify_admin_menu_for_org_managers'));
        add_action('admin_init', array($this, 'block_restricted_pages_for_org_managers'));
        add_action('admin_init', array($this, 'redirect_org_managers_from_dashboard'));
    }

    public function register_post_types() {
        // Check if post types already exist (from mu-plugins or theme)
        $existing_event_type = post_type_exists('unbc_event');
        $existing_org_type = post_type_exists('organization');
        
        // Only register if doesn't exist, or re-register with compatible settings
        if (!$existing_event_type) {
            register_post_type('unbc_event', array(
            'labels' => array(
                'name' => 'Events',
                'singular_name' => 'Event',
                'add_new' => 'Add New Event',
                'add_new_item' => 'Add New Event',
                'edit_item' => 'Edit Event',
                'new_item' => 'New Event',
                'view_item' => 'View Event',
                'search_items' => 'Search Events',
                'not_found' => 'No events found',
                'not_found_in_trash' => 'No events found in Trash',
                'menu_name' => 'Events',
                'all_items' => 'All Events'
            ),
            'public' => true,               // Compatible with existing setup
            'publicly_queryable' => true,   // Allow frontend access
            'show_ui' => true,               // Keep admin UI
            'show_in_menu' => true,          // Show in admin menu
            'has_archive' => true,           // Allow archive page
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail'), // Full support
            'show_in_rest' => true,          // Keep REST API access for React app
            'rest_base' => 'events',
            'menu_icon' => 'dashicons-calendar-alt', // Calendar icon for events
            'capability_type' => array('unbc_event', 'unbc_events'),
            'map_meta_cap' => true
        ));
        }

        // Organization/Clubs post type (using existing 'organization' to preserve data)
        if (!$existing_org_type) {
            register_post_type('organization', array(
            'labels' => array(
                'name' => 'Organizations',
                'singular_name' => 'Organization',
                'add_new' => 'Add New Club/Organization',
                'add_new_item' => 'Add New Organization',
                'edit_item' => 'Edit Organization',
                'new_item' => 'New Organization',
                'view_item' => 'View Organization',
                'search_items' => 'Search Organizations',
                'not_found' => 'No organizations found',
                'not_found_in_trash' => 'No organizations found in Trash',
                'menu_name' => 'Organizations',
                'all_items' => 'All Organizations'
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
            'rest_base' => 'organization',
            'menu_icon' => 'dashicons-groups', // Group of people icon for organizations
            'rewrite' => array('slug' => 'clubs'),
            'publicly_queryable' => true,
            'query_var' => true,
            'capability_type' => array('organization', 'organizations'),
            'map_meta_cap' => true
        ));
        }
    }

    public function register_taxonomies() {
        // Only register taxonomies if they don't exist
        if (!taxonomy_exists('event_category')) {
            register_taxonomy('event_category', 'unbc_event', array(
            'labels' => array(
                'name' => 'Event Categories',
                'singular_name' => 'Event Category'
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
            'public' => true
        ));
        }
        
        // Organization Categories
        if (!taxonomy_exists('org_category')) {
            register_taxonomy('org_category', 'organization', array(
            'labels' => array(
                'name' => 'Organization Categories',
                'singular_name' => 'Category',
                'search_items' => 'Search Categories',
                'all_items' => 'All Categories',
                'parent_item' => 'Parent Category',
                'parent_item_colon' => 'Parent Category:',
                'edit_item' => 'Edit Category',
                'update_item' => 'Update Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => 'New Category Name',
                'menu_name' => 'Categories',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'club-category'),
        ));
        }
        
        // Organization Tags
        if (!taxonomy_exists('org_tag')) {
            register_taxonomy('org_tag', 'organization', array(
            'labels' => array(
                'name' => 'Organization Tags',
                'singular_name' => 'Tag',
                'search_items' => 'Search Tags',
                'popular_items' => 'Popular Tags',
                'all_items' => 'All Tags',
                'edit_item' => 'Edit Tag',
                'update_item' => 'Update Tag',
                'add_new_item' => 'Add New Tag',
                'new_item_name' => 'New Tag Name',
                'menu_name' => 'Tags',
            ),
            'hierarchical' => false,
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'club-tag'),
        ));
        }
    }
    
    public function disable_gutenberg_for_events($use_block_editor, $post_type) {
        if ($post_type === 'unbc_event' || $post_type === 'organization') {
            return false; // Disable Gutenberg for events and organizations
        }
        return $use_block_editor;
    }
    
    public function custom_organization_permalink($permalink, $post) {
        if ($post->post_type !== 'organization') {
            return $permalink;
        }
        
        $is_department = get_post_meta($post->ID, 'org_is_department', true);
        
        if ($is_department === '1') {
            // Departments use /organization/name
            $permalink = home_url('/organization/' . $post->post_name . '/');
        } else {
            // Clubs use /clubs/name (default)
            $permalink = home_url('/clubs/' . $post->post_name . '/');
        }
        
        return $permalink;
    }
    
    public function add_featured_image_support() {
        add_meta_box(
            'postimagediv',
            __('Club/Organization Logo'),
            'post_thumbnail_meta_box',
            'organization',
            'side',
            'high'
        );
    }
    
    public function restrict_organization_manager_posts($query) {
        global $pagenow;
        
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $current_user = wp_get_current_user();
        if (!in_array('organization_manager', $current_user->roles)) {
            return;
        }
        
        // Get user's assigned organization
        $assigned_org = get_user_meta($current_user->ID, 'assigned_organization', true);
        if (!$assigned_org) {
            return;
        }
        
        // Restrict organization posts to only the assigned organization
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'organization') {
            $query->set('post__in', array($assigned_org));
        }
        
        // Restrict event posts to only events associated with their organization
        if (($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'unbc_event') ||
            ($pagenow === 'edit.php' && !isset($_GET['post_type']))) {
            
            // Find events that belong to this organization
            $organization_events = get_posts(array(
                'post_type' => 'unbc_event',
                'numberposts' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'organization_id',
                        'value' => $assigned_org,
                        'compare' => '='
                    )
                ),
                'fields' => 'ids'
            ));
            
            if (!empty($organization_events)) {
                $query->set('post__in', $organization_events);
            } else {
                // If no events found, return empty result
                $query->set('post__in', array(0));
            }
        }
    }
    
    public function modify_admin_menu_for_org_managers() {
        $current_user = wp_get_current_user();
        if (!in_array('organization_manager', $current_user->roles)) {
            return;
        }
        
        // Remove Posts menu
        remove_menu_page('edit.php');
        
        // Remove Pages menu
        remove_menu_page('edit.php?post_type=page');
        
        // Remove Comments menu
        remove_menu_page('edit-comments.php');
        
        // Remove Dashboard menu
        remove_menu_page('index.php');
        
        // Remove Media menu (they can still upload via events/organization forms)
        remove_menu_page('upload.php');
        
        // Remove Tools menu
        remove_menu_page('tools.php');
        
        // Remove ability to add new organizations
        remove_submenu_page('edit.php?post_type=organization', 'post-new.php?post_type=organization');
        
        // Also remove any "Add New" links that might appear elsewhere
        add_action('admin_head', function() {
            echo '<style>
                .page-title-action[href*="post-new.php?post_type=organization"] { display: none !important; }
                .add-new-h2[href*="post-new.php?post_type=organization"] { display: none !important; }
                a[href*="post-new.php?post_type=organization"] { display: none !important; }
            </style>';
        });
        
        // Modify menu labels for clarity
        global $submenu;
        if (isset($submenu['edit.php?post_type=organization'])) {
            foreach ($submenu['edit.php?post_type=organization'] as $key => $item) {
                if ($item[2] === 'edit.php?post_type=organization') {
                    $submenu['edit.php?post_type=organization'][$key][0] = 'My Organization';
                }
            }
        }
        
        if (isset($submenu['edit.php?post_type=unbc_event'])) {
            foreach ($submenu['edit.php?post_type=unbc_event'] as $key => $item) {
                if ($item[2] === 'edit.php?post_type=unbc_event') {
                    $submenu['edit.php?post_type=unbc_event'][$key][0] = 'My Organization Events';
                }
            }
        }
    }
    
    public function block_restricted_pages_for_org_managers() {
        $current_user = wp_get_current_user();
        if (!in_array('organization_manager', $current_user->roles)) {
            return;
        }
        
        global $pagenow;
        
        // List of restricted pages for organization managers
        $restricted_pages = array(
            'index.php',             // Dashboard
            'edit.php',              // Posts listing
            'post-new.php',          // New post
            'post.php',              // Edit post
            'edit-comments.php',     // Comments
            'comment.php',           // Edit comment
            'edit-tags.php',         // Tags
            'term.php',              // Edit term
            'nav-menus.php',         // Menus
            'widgets.php',           // Widgets
            'customize.php',         // Customizer
            'themes.php',            // Themes
            'plugins.php',           // Plugins
            'users.php',             // Users
            'user-new.php',          // Add user
            'profile.php',           // Profile (they can still edit their own)
            'options-general.php',   // Settings
            'options-writing.php',   // Writing settings
            'options-reading.php',   // Reading settings
            'options-discussion.php',// Discussion settings
            'options-media.php',     // Media settings
            'options-permalink.php', // Permalink settings
            'tools.php',             // Tools
            'import.php',            // Import
            'export.php',            // Export
            'upload.php'             // Media library
        );
        
        // Check if current page is restricted
        if (in_array($pagenow, $restricted_pages)) {
            // Dashboard is handled by redirect function, not blocked here
            if ($pagenow === 'index.php') {
                return;
            }
            
            // Allow editing their own profile
            if ($pagenow === 'profile.php' || ($pagenow === 'user-edit.php' && isset($_GET['user_id']) && $_GET['user_id'] == $current_user->ID)) {
                return;
            }
            
            // Allow editing posts if it's their organization's content
            if ($pagenow === 'post.php' && isset($_GET['post'])) {
                $post_id = intval($_GET['post']);
                $post = get_post($post_id);
                
                if ($post && ($post->post_type === 'organization' || $post->post_type === 'unbc_event')) {
                    // Let the capability system handle this
                    return;
                }
            }
            
            // Allow new posts if it's for their allowed post types
            if ($pagenow === 'post-new.php' && isset($_GET['post_type'])) {
                $post_type = $_GET['post_type'];
                if ($post_type === 'unbc_event') {
                    // Let the capability system handle this
                    return;
                }
            }
            
            // Allow edit.php for their allowed post types
            if ($pagenow === 'edit.php' && isset($_GET['post_type'])) {
                $post_type = $_GET['post_type'];
                if ($post_type === 'organization' || $post_type === 'unbc_event') {
                    // Let the capability system handle this
                    return;
                }
            }
            
            // Block access to regular posts (edit.php without post_type)
            if ($pagenow === 'edit.php' && !isset($_GET['post_type'])) {
                wp_die(__('Sorry, you are not allowed to access this page.'), 403);
            }
            
            // Block access to other restricted pages (but not edit.php, post.php, post-new.php as we handle those above)
            if (!in_array($pagenow, array('edit.php', 'post.php', 'post-new.php'))) {
                wp_die(__('Sorry, you are not allowed to access this page.'), 403);
            }
        }
        
        // Also block if they try to access regular posts directly
        if ($pagenow === 'post.php' && isset($_GET['post'])) {
            $post_id = intval($_GET['post']);
            $post = get_post($post_id);
            
            if ($post && $post->post_type === 'post') {
                wp_die(__('Sorry, you are not allowed to edit regular posts.'), 403);
            }
        }
        
        // Block creating regular posts
        if ($pagenow === 'post-new.php' && (!isset($_GET['post_type']) || $_GET['post_type'] === 'post')) {
            wp_die(__('Sorry, you are not allowed to create regular posts.'), 403);
        }
        
        // Block editing pages
        if ($pagenow === 'post.php' && isset($_GET['post'])) {
            $post_id = intval($_GET['post']);
            $post = get_post($post_id);
            
            if ($post && $post->post_type === 'page') {
                wp_die(__('Sorry, you are not allowed to edit pages.'), 403);
            }
        }
        
        // Block creating pages
        if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            wp_die(__('Sorry, you are not allowed to create pages.'), 403);
        }
    }
    
    public function redirect_org_managers_from_dashboard() {
        $current_user = wp_get_current_user();
        if (!in_array('organization_manager', $current_user->roles)) {
            return;
        }
        
        global $pagenow;
        
        // Redirect from dashboard to their organization page
        if ($pagenow === 'index.php' || $pagenow === 'admin.php') {
            $assigned_org = get_user_meta($current_user->ID, 'assigned_organization', true);
            
            if ($assigned_org) {
                // Redirect to edit their organization
                wp_redirect(admin_url('post.php?post=' . $assigned_org . '&action=edit'));
                exit;
            } else {
                // If no organization assigned, redirect to events page
                wp_redirect(admin_url('edit.php?post_type=unbc_event'));
                exit;
            }
        }
    }
}