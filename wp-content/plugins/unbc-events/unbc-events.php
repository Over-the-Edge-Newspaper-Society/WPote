<?php
/**
 * Plugin Name: UNBC Campus Manager
 * Description: Comprehensive management system for UNBC campus events and organizations
 * Version: 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNBC_Events_Plugin {
    public function __construct() {
        add_action('init', array($this, 'init'));
        
        // Include files immediately
        $this->include_files();
        
        // Initialize REST API immediately after including files
        new UNBC_Events_REST_API();
    }

    public function init() {
        new UNBC_Events_Post_Types();
        new UNBC_Events_Meta_Boxes();
        new UNBC_Events_User_Roles();
        new UNBC_Organization_Manager_Admin();
        
        // Add custom rewrite rules for organizations/departments
        add_action('init', array($this, 'add_rewrite_rules'));
        
        // Force menu icon update
        add_action('admin_menu', array($this, 'update_menu_icons'), 999);
    }
    
    public function update_menu_icons() {
        global $menu;
        
        // Update Events menu icon
        foreach ($menu as $key => $item) {
            if (isset($item[5]) && $item[5] === 'menu-posts-unbc_event') {
                $menu[$key][6] = 'dashicons-calendar-alt';
            }
            if (isset($item[5]) && $item[5] === 'menu-posts-organization') {
                $menu[$key][6] = 'dashicons-groups';
            }
        }
    }
    
    public function add_rewrite_rules() {
        // Add rewrite rule for /organization/name
        add_rewrite_rule(
            '^organization/([^/]+)/?$',
            'index.php?post_type=organization&name=$matches[1]',
            'top'
        );
    }


    private function include_files() {
        $files_to_include = array(
            'includes/class-post-types.php',
            'includes/class-rest-api.php',
            'includes/class-meta-boxes.php',
            'includes/class-user-roles.php',
            'includes/class-organization-manager-admin.php'
        );
        
        foreach ($files_to_include as $file) {
            $file_path = plugin_dir_path(__FILE__) . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            } else {
                // Log error if file doesn't exist
                error_log('UNBC Events Plugin: Missing file - ' . $file_path);
            }
        }
    }
}

// Initialize plugin
$unbc_events_plugin = new UNBC_Events_Plugin();

// Plugin activation hook
register_activation_hook(__FILE__, 'unbc_events_plugin_activate');

function unbc_events_plugin_activate() {
    // Ensure our post types are registered
    require_once plugin_dir_path(__FILE__) . 'includes/class-post-types.php';
    $post_types = new UNBC_Events_Post_Types();
    $post_types->register_post_types();
    $post_types->register_taxonomies();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}