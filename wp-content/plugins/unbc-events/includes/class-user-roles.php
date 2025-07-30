<?php
class UNBC_Events_User_Roles {
    
    public function __construct() {
        add_action('init', array($this, 'create_organization_manager_role'));
        add_action('wp_loaded', array($this, 'assign_organization_to_user'));
        add_filter('map_meta_cap', array($this, 'map_organization_edit_capability'), 10, 4);
        
        // Ensure role exists when needed
        add_action('wp_loaded', array($this, 'ensure_role_exists'));
    }

    public function create_organization_manager_role() {
        // Remove role if it exists to recreate with fresh capabilities
        remove_role('organization_manager');
        
        // Create the Organization Manager role
        add_role('organization_manager', 'Organization Manager', array(
            'read' => true,
            'upload_files' => true,
            
            // Explicitly deny general post and comment capabilities
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'edit_others_posts' => false,
            'delete_others_posts' => false,
            'edit_published_posts' => false,
            'delete_published_posts' => false,
            'moderate_comments' => false,
            'edit_comment' => false,
            
            // Organization capabilities
            'edit_organizations' => true,
            'edit_published_organizations' => true,
            'publish_organizations' => true,
            'read_organization' => true,
            'delete_organizations' => false, // Cannot delete organizations
            'edit_others_organizations' => false, // Cannot edit other organizations
            'delete_published_organizations' => false, // Cannot delete published organizations
            'delete_others_organizations' => false, // Cannot delete others' organizations
            'manage_organization_terms' => false, // Cannot manage categories/tags
            'edit_organization_terms' => false,
            'delete_organization_terms' => false,
            'assign_organization_terms' => false,
            
            // Event capabilities
            'edit_unbc_events' => true,
            'edit_published_unbc_events' => true,
            'publish_unbc_events' => true,
            'delete_unbc_events' => true,
            'delete_published_unbc_events' => true,
            'read_unbc_event' => true,
            'edit_others_unbc_events' => false, // Cannot edit other organizations' events
            'delete_others_unbc_events' => false, // Cannot delete other organizations' events
            'manage_unbc_event_terms' => true, // Can manage event categories for their events
            'edit_unbc_event_terms' => true,
            'delete_unbc_event_terms' => false,
            'assign_unbc_event_terms' => true,
        ));
    }
    
    public function ensure_role_exists() {
        // Make sure the role exists, create it if it doesn't
        if (!get_role('organization_manager')) {
            $this->create_organization_manager_role();
        }
        
        // Also ensure administrators have the custom post type capabilities
        $this->add_admin_capabilities();
    }
    
    public function add_admin_capabilities() {
        $admin_role = get_role('administrator');
        if ($admin_role) {
            // Event capabilities for administrators
            $admin_role->add_cap('edit_unbc_events');
            $admin_role->add_cap('edit_others_unbc_events');
            $admin_role->add_cap('publish_unbc_events');
            $admin_role->add_cap('read_unbc_event');
            $admin_role->add_cap('delete_unbc_events');
            $admin_role->add_cap('delete_others_unbc_events');
            $admin_role->add_cap('delete_published_unbc_events');
            $admin_role->add_cap('edit_published_unbc_events');
            $admin_role->add_cap('manage_unbc_event_terms');
            $admin_role->add_cap('edit_unbc_event_terms');
            $admin_role->add_cap('delete_unbc_event_terms');
            $admin_role->add_cap('assign_unbc_event_terms');
            
            // Organization capabilities for administrators
            $admin_role->add_cap('edit_organizations');
            $admin_role->add_cap('edit_others_organizations');
            $admin_role->add_cap('publish_organizations');
            $admin_role->add_cap('read_organization');
            $admin_role->add_cap('delete_organizations');
            $admin_role->add_cap('delete_others_organizations');
            $admin_role->add_cap('delete_published_organizations');
            $admin_role->add_cap('edit_published_organizations');
            $admin_role->add_cap('manage_organization_terms');
            $admin_role->add_cap('edit_organization_terms');
            $admin_role->add_cap('delete_organization_terms');
            $admin_role->add_cap('assign_organization_terms');
        }
    }
    
    public function assign_organization_to_user() {
        // This function can be used to assign a specific organization to a user
        // when creating their account or through the admin interface
    }
    
    public function set_user_organization($user_id, $organization_id) {
        update_user_meta($user_id, 'assigned_organization', $organization_id);
    }
    
    public function get_user_organization($user_id) {
        return get_user_meta($user_id, 'assigned_organization', true);
    }
    
    public function map_organization_edit_capability($caps, $cap, $user_id, $args) {
        // Handle organization editing capabilities
        if ($cap === 'edit_post' && isset($args[0])) {
            $post_id = $args[0];
            $post = get_post($post_id);
            
            if ($post && ($post->post_type === 'organization' || $post->post_type === 'unbc_event')) {
                $user = get_user_by('id', $user_id);
                
                if ($user && !empty($user->roles) && in_array('organization_manager', $user->roles)) {
                    $assigned_org = $this->get_user_organization($user_id);
                    
                    if ($post->post_type === 'organization') {
                        // Organization managers can only edit their assigned organization
                        if ($assigned_org && $assigned_org == $post_id) {
                            return array('edit_organizations');
                        } else {
                            return array('do_not_allow');
                        }
                    } elseif ($post->post_type === 'unbc_event') {
                        // For new events (post_id = 0), allow if user has organization assigned
                        if ($post_id == 0 && $assigned_org) {
                            return array('edit_unbc_events');
                        }
                        
                        // For existing events, check if it belongs to their organization
                        $event_org_id = get_post_meta($post_id, 'organization_id', true);
                        if ($assigned_org && ($assigned_org == $event_org_id || empty($event_org_id))) {
                            return array('edit_unbc_events');
                        } else {
                            return array('do_not_allow');
                        }
                    }
                }
            }
        }
        
        // Handle create new post capability
        if ($cap === 'create_posts' || $cap === 'edit_posts') {
            $user = get_user_by('id', $user_id);
            if ($user && !empty($user->roles) && in_array('organization_manager', $user->roles)) {
                // Check if this is for events
                global $pagenow, $typenow;
                if (($pagenow === 'post-new.php' && $typenow === 'unbc_event') || 
                    (isset($_GET['post_type']) && $_GET['post_type'] === 'unbc_event')) {
                    return array('edit_unbc_events');
                }
            }
        }
        
        // Handle event deletion capabilities
        if ($cap === 'delete_post' && isset($args[0])) {
            $post_id = $args[0];
            $post = get_post($post_id);
            
            if ($post && $post->post_type === 'unbc_event') {
                $user = get_user_by('id', $user_id);
                
                if (in_array('organization_manager', $user->roles)) {
                    $assigned_org = $this->get_user_organization($user_id);
                    $event_org_id = get_post_meta($post_id, 'organization_id', true);
                    
                    if ($assigned_org && $assigned_org == $event_org_id) {
                        return array('delete_unbc_events');
                    } else {
                        return array('do_not_allow');
                    }
                }
            }
        }
        
        return $caps;
    }
    
    public function can_user_edit_organization_field($user_id, $field_name) {
        $user = get_user_by('id', $user_id);
        
        if (!$user || !in_array('organization_manager', $user->roles)) {
            return true; // Non-organization managers have full access
        }
        
        // Restricted fields for organization managers
        $restricted_fields = array(
            'org_is_department',
            'org_founded_date', 
            'org_approval_date',
            'org_registration_date',
            'post_name', // Slug/link
            'post_status', // Visibility status
            'post_title' // Organization name/title
        );
        
        return !in_array($field_name, $restricted_fields);
    }
    
    public function get_organization_manager_allowed_fields() {
        return array(
            // Basic Info - Allowed
            'org_email',
            'org_size', 
            'org_short_description',
            'org_membership_requirements',
            'org_meeting_schedule',
            'org_original_image_path',
            
            // Contact Info - Allowed
            'org_president_name',
            'org_president_email', 
            'org_contact_name',
            'org_contact_email',
            'org_office_location',
            
            // Social Media - Allowed
            'org_website',
            'org_facebook',
            'org_instagram', 
            'org_twitter',
            'org_discord',
            'org_linktree',
            'org_youtube',
            'org_registration_link',
            
            // Additional Info - Partially allowed
            'org_status', // Can edit status
            'org_founded_year', // Can edit founded year (but not founded date)
            
            // Post content
            'post_content', // Can edit description
            'post_excerpt', // Can edit excerpt
            '_thumbnail_id' // Can edit featured image
        );
    }
    
    public function get_organization_manager_restricted_fields() {
        return array(
            'org_is_department',
            'org_founded_date',
            'org_approval_date', 
            'org_registration_date',
            'post_name',
            'post_status', 
            'post_title'
        );
    }
}