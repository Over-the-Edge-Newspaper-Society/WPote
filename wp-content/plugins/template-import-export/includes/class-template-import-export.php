<?php
/**
 * Main plugin class for Template Import/Export
 */

class Template_Import_Export {
    
    /**
     * Initialize the plugin
     */
    public function run() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_tie_export_templates', array($this, 'ajax_export_templates'));
        add_action('wp_ajax_tie_get_template_list', array($this, 'ajax_get_template_list'));
        
        // Register templates with Site Editor
        add_filter('theme_templates', array($this, 'register_plugin_templates'), 10, 4);
        add_action('init', array($this, 'ensure_template_taxonomy_terms'), 20);
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Any initialization tasks
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Template Import/Export', 'template-import-export'),
            __('Template I/E', 'template-import-export'),
            'manage_options',
            'template-import-export',
            array($this, 'render_admin_page'),
            'dashicons-migrate',
            30
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_template-import-export') {
            return;
        }
        
        wp_enqueue_script(
            'tie-admin',
            TIE_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            TIE_PLUGIN_VERSION,
            true
        );
        
        wp_localize_script('tie-admin', 'tie_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tie_nonce')
        ));
        
        wp_enqueue_style(
            'tie-admin',
            TIE_PLUGIN_URL . 'assets/admin.css',
            array(),
            TIE_PLUGIN_VERSION
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Handle import if form was submitted
        if (isset($_POST['tie_import']) && isset($_FILES['import_file'])) {
            $this->handle_template_import();
        }
        
        include TIE_PLUGIN_PATH . 'includes/admin-page.php';
    }
    
    /**
     * Get available templates with usage information
     */
    public function get_templates_with_usage() {
        $templates = array();
        
        // Get theme templates
        $theme_templates = wp_get_theme()->get_page_templates();
        foreach ($theme_templates as $template_file => $template_name) {
            $templates[$template_file] = array(
                'name' => $template_name,
                'type' => 'theme',
                'usage_count' => 0,
                'used_by' => array()
            );
        }
        
        // Get block templates
        if (function_exists('get_block_templates')) {
            $all_templates = get_block_templates(array(), 'wp_template');
            
            foreach ($all_templates as $template) {
                $key = 'wp-custom-template-' . $template->slug;
                $templates[$key] = array(
                    'name' => $template->title,
                    'type' => 'block',
                    'usage_count' => 0,
                    'used_by' => array(),
                    'slug' => $template->slug,
                    'template_object' => $template
                );
            }
        }
        
        // Also check database for custom templates
        $custom_templates_db = get_posts(array(
            'post_type' => 'wp_template',
            'post_status' => 'any',
            'numberposts' => -1
        ));
        
        foreach ($custom_templates_db as $custom_template) {
            $key = 'wp-custom-template-' . $custom_template->post_name;
            if (!isset($templates[$key])) {
                $templates[$key] = array(
                    'name' => $custom_template->post_title,
                    'type' => 'custom',
                    'usage_count' => 0,
                    'used_by' => array(),
                    'slug' => $custom_template->post_name,
                    'post_id' => $custom_template->ID
                );
            }
        }
        
        // Add default template option
        $templates['default'] = array(
            'name' => 'Default template',
            'type' => 'default',
            'usage_count' => 0,
            'used_by' => array()
        );
        
        // Get usage information for all post types
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            $posts = get_posts(array(
                'post_type' => $post_type,
                'post_status' => 'any',
                'numberposts' => -1,
                'fields' => 'ids'
            ));
            
            foreach ($posts as $post_id) {
                $template = get_post_meta($post_id, '_wp_page_template', true);
                if (empty($template)) {
                    $template = 'default';
                }
                
                if (isset($templates[$template])) {
                    $templates[$template]['usage_count']++;
                    $templates[$template]['used_by'][] = array(
                        'id' => $post_id,
                        'title' => get_the_title($post_id),
                        'post_type' => $post_type
                    );
                }
            }
        }
        
        return $templates;
    }
    
    /**
     * AJAX handler to get template list
     */
    public function ajax_get_template_list() {
        if (!wp_verify_nonce($_POST['nonce'], 'tie_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $templates = $this->get_templates_with_usage();
        wp_send_json_success($templates);
    }
    
    /**
     * AJAX handler for template export
     */
    public function ajax_export_templates() {
        if (!wp_verify_nonce($_POST['nonce'], 'tie_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $selected_templates = isset($_POST['selected_templates']) ? array_map('sanitize_text_field', $_POST['selected_templates']) : array();
        $export_default = isset($_POST['export_default']) && $_POST['export_default'] == '1';
        $export_block_templates = isset($_POST['export_block_templates']) && $_POST['export_block_templates'] == '1';
        $post_types = isset($_POST['post_types']) ? array_map('sanitize_text_field', $_POST['post_types']) : array('organization');
        
        $template_data = array(
            'export_date' => current_time('mysql'),
            'export_site' => get_site_url(),
            'plugin_version' => TIE_PLUGIN_VERSION,
            'exported_post_types' => $post_types
        );
        
        // Export default template setting if applicable
        if ($export_default) {
            // Check for various possible default template options
            $default_options = array(
                'campus_manager_default_org_template',
                'default_page_template',
                'default_post_template'
            );
            
            $template_data['default_templates'] = array();
            foreach ($default_options as $option) {
                $value = get_option($option);
                if ($value !== false) {
                    $template_data['default_templates'][$option] = $value;
                }
            }
        }
        
        // Export post template assignments
        if (!empty($selected_templates)) {
            $template_data['post_templates'] = array();
            
            foreach ($post_types as $post_type) {
                $all_posts = get_posts(array(
                    'post_type' => $post_type,
                    'post_status' => 'any',
                    'numberposts' => -1
                ));
                
                foreach ($all_posts as $post) {
                    $post_template = get_post_meta($post->ID, '_wp_page_template', true);
                    if (empty($post_template)) {
                        $post_template = 'default';
                    }
                    
                    // Only include posts that use selected templates
                    if (in_array($post_template, $selected_templates)) {
                        $post_data = array(
                            'post_title' => $post->post_title,
                            'post_slug' => $post->post_name,
                            'post_type' => $post->post_type,
                            'content' => $post->post_content,
                            'template' => $post_template
                        );
                        
                        $template_data['post_templates'][] = $post_data;
                    }
                }
            }
        }
        
        // Export block templates
        if ($export_block_templates) {
            $template_data['block_templates'] = array();
            
            // Get all block templates
            if (function_exists('get_block_templates')) {
                $all_templates = get_block_templates(array(), 'wp_template');
                
                foreach ($all_templates as $template) {
                    // Export all templates or just selected ones
                    $template_key = 'wp-custom-template-' . $template->slug;
                    if (empty($selected_templates) || in_array($template_key, $selected_templates)) {
                        $template_data['block_templates'][] = array(
                            'slug' => $template->slug,
                            'title' => $template->title,
                            'content' => $template->content,
                            'theme' => $template->theme,
                            'type' => $template->type,
                            'source' => $template->source,
                            'description' => isset($template->description) ? $template->description : '',
                            'status' => isset($template->status) ? $template->status : 'publish',
                            'has_theme_file' => isset($template->has_theme_file) ? $template->has_theme_file : false,
                            'is_custom' => isset($template->is_custom) ? $template->is_custom : false,
                            'author' => isset($template->author) ? $template->author : null,
                            'area' => isset($template->area) ? $template->area : '',
                        );
                    }
                }
            }
            
            // Also get custom templates from database
            $custom_templates = get_posts(array(
                'post_type' => 'wp_template',
                'post_status' => 'any',
                'numberposts' => -1
            ));
            
            foreach ($custom_templates as $custom_template) {
                $template_key = 'wp-custom-template-' . $custom_template->post_name;
                if (empty($selected_templates) || in_array($template_key, $selected_templates)) {
                    // Check if not already exported
                    $already_exported = false;
                    foreach ($template_data['block_templates'] as $exported) {
                        if ($exported['slug'] === $custom_template->post_name) {
                            $already_exported = true;
                            break;
                        }
                    }
                    
                    if (!$already_exported) {
                        $template_data['block_templates'][] = array(
                            'slug' => $custom_template->post_name,
                            'title' => $custom_template->post_title,
                            'content' => $custom_template->post_content,
                            'theme' => get_post_meta($custom_template->ID, 'theme', true) ?: get_stylesheet(),
                            'type' => 'wp_template',
                            'source' => 'custom',
                            'description' => $custom_template->post_excerpt,
                            'status' => $custom_template->post_status,
                            'has_theme_file' => false,
                            'is_custom' => true,
                            'author' => $custom_template->post_author,
                            'area' => get_post_meta($custom_template->ID, 'area', true) ?: '',
                            'post_id' => $custom_template->ID
                        );
                    }
                }
            }
        }
        
        $post_count = isset($template_data['post_templates']) ? count($template_data['post_templates']) : 0;
        $template_count = isset($template_data['block_templates']) ? count($template_data['block_templates']) : 0;
        
        wp_send_json_success(array(
            'content' => $template_data,
            'post_count' => $post_count,
            'template_count' => $template_count
        ));
    }
    
    /**
     * Handle template import
     */
    private function handle_template_import() {
        if (!current_user_can('manage_options')) {
            echo '<div class="notice notice-error"><p>Insufficient permissions.</p></div>';
            return;
        }
        
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            echo '<div class="notice notice-error"><p>Error uploading file. Please try again.</p></div>';
            return;
        }
        
        $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $template_data = json_decode($file_content, true);
        
        if (!$template_data) {
            echo '<div class="notice notice-error"><p>Invalid import file format.</p></div>';
            return;
        }
        
        $overwrite_existing = isset($_POST['overwrite_existing']) && $_POST['overwrite_existing'] === '1';
        $imported_count = 0;
        $skipped_count = 0;
        $templates_imported = 0;
        
        // Import default template settings
        if (!empty($template_data['default_templates'])) {
            foreach ($template_data['default_templates'] as $option_name => $value) {
                update_option($option_name, sanitize_text_field($value));
            }
        }
        
        // Import post template assignments
        if (!empty($template_data['post_templates'])) {
            foreach ($template_data['post_templates'] as $post_data) {
                if (empty($post_data['post_slug']) || empty($post_data['template'])) {
                    continue;
                }
                
                // Try to find post by slug and type
                $post = get_page_by_path($post_data['post_slug'], OBJECT, $post_data['post_type']);
                if (!$post && !empty($post_data['post_title'])) {
                    $posts = get_posts(array(
                        'post_type' => $post_data['post_type'],
                        'title' => $post_data['post_title'],
                        'numberposts' => 1,
                        'post_status' => 'any'
                    ));
                    $post = !empty($posts) ? $posts[0] : null;
                }
                
                if ($post) {
                    $existing_template = get_post_meta($post->ID, '_wp_page_template', true);
                    
                    if ($overwrite_existing || empty($existing_template)) {
                        update_post_meta($post->ID, '_wp_page_template', sanitize_text_field($post_data['template']));
                        
                        // Import content if available and requested
                        if (!empty($post_data['content']) && ($overwrite_existing || empty($post->post_content))) {
                            wp_update_post(array(
                                'ID' => $post->ID,
                                'post_content' => wp_kses_post($post_data['content'])
                            ));
                        }
                        
                        $imported_count++;
                    } else {
                        $skipped_count++;
                    }
                }
            }
        }
        
        // Import block templates
        if (!empty($template_data['block_templates'])) {
            foreach ($template_data['block_templates'] as $block_template) {
                if (empty($block_template['slug']) || empty($block_template['content'])) {
                    continue;
                }
                
                // Check if template already exists
                $existing_post = get_posts(array(
                    'post_type' => 'wp_template',
                    'post_name' => $block_template['slug'],
                    'post_status' => 'any',
                    'numberposts' => 1
                ));
                
                if ($overwrite_existing || empty($existing_post)) {
                    $template_post_data = array(
                        'post_type' => 'wp_template',
                        'post_status' => isset($block_template['status']) ? $block_template['status'] : 'publish',
                        'post_title' => $block_template['title'],
                        'post_name' => $block_template['slug'],
                        'post_content' => $block_template['content'],
                        'post_excerpt' => isset($block_template['description']) ? $block_template['description'] : '',
                        'post_author' => get_current_user_id(),
                    );
                    
                    if (!empty($existing_post)) {
                        $template_post_data['ID'] = $existing_post[0]->ID;
                        $post_id = wp_update_post($template_post_data);
                    } else {
                        $post_id = wp_insert_post($template_post_data);
                    }
                    
                    if ($post_id && !is_wp_error($post_id)) {
                        // Add template metadata
                        update_post_meta($post_id, 'theme', get_stylesheet());
                        
                        if (isset($block_template['area'])) {
                            update_post_meta($post_id, 'area', $block_template['area']);
                        }
                        
                        if (isset($block_template['is_custom'])) {
                            update_post_meta($post_id, 'is_custom', $block_template['is_custom']);
                        }
                        
                        // Set wp_theme taxonomy term
                        if (taxonomy_exists('wp_theme')) {
                            wp_set_object_terms($post_id, get_stylesheet(), 'wp_theme', false);
                        }
                        
                        $templates_imported++;
                    }
                }
            }
        }
        
        $message = sprintf(
            'Import completed! %d post template assignments imported, %d block templates imported%s%s',
            $imported_count,
            $templates_imported,
            $skipped_count > 0 ? ", {$skipped_count} skipped" : '',
            !empty($template_data['export_site']) ? ' from ' . esc_html($template_data['export_site']) : ''
        );
        
        echo '<div class="notice notice-success"><p>' . $message . '</p></div>';
    }
    
    /**
     * Register templates with WordPress
     */
    public function register_plugin_templates($templates, $theme, $post, $post_type) {
        // Get all available templates
        $all_templates = $this->get_templates_with_usage();
        
        // Remove usage information and return just template list
        $template_list = array();
        foreach ($all_templates as $key => $template_info) {
            if ($key !== 'default') {
                $template_list[$key] = $template_info['name'];
            }
        }
        
        return array_merge($templates, $template_list);
    }
    
    /**
     * Ensure templates have proper taxonomy terms
     */
    public function ensure_template_taxonomy_terms() {
        // Get all wp_template posts
        $template_posts = get_posts(array(
            'post_type' => 'wp_template',
            'post_status' => 'any',
            'numberposts' => -1
        ));
        
        if (empty($template_posts) || !taxonomy_exists('wp_theme')) {
            return;
        }
        
        $theme_slug = get_stylesheet();
        
        foreach ($template_posts as $template_post) {
            // Set the wp_theme taxonomy term
            wp_set_object_terms($template_post->ID, $theme_slug, 'wp_theme', false);
        }
    }
}