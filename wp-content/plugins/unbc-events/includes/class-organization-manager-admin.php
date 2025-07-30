<?php
class UNBC_Organization_Manager_Admin {
    
    public function __construct() {
        add_action('show_user_profile', array($this, 'add_organization_assignment_field'));
        add_action('edit_user_profile', array($this, 'add_organization_assignment_field'));
        add_action('personal_options_update', array($this, 'save_organization_assignment_field'));
        add_action('edit_user_profile_update', array($this, 'save_organization_assignment_field'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function add_organization_assignment_field($user) {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $assigned_org = get_user_meta($user->ID, 'assigned_organization', true);
        $organizations = get_posts(array(
            'post_type' => 'organization',
            'numberposts' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <h3>Organization Manager Settings</h3>
        <table class="form-table">
            <tr>
                <th><label for="assigned_organization">Assigned Organization</label></th>
                <td>
                    <select name="assigned_organization" id="assigned_organization">
                        <option value="">Select Organization (None)</option>
                        <?php foreach ($organizations as $org): ?>
                            <option value="<?php echo $org->ID; ?>" <?php selected($assigned_org, $org->ID); ?>>
                                <?php echo esc_html($org->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        If this user has the "Organization Manager" role, they will only be able to edit this organization and create events under it.
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function save_organization_assignment_field($user_id) {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['assigned_organization'])) {
            update_user_meta($user_id, 'assigned_organization', sanitize_text_field($_POST['assigned_organization']));
        }
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=organization',
            'Organization Managers',
            'Managers',
            'manage_options',
            'organization-managers',
            array($this, 'organization_managers_page')
        );
    }
    
    public function organization_managers_page() {
        if (isset($_POST['create_manager']) && wp_verify_nonce($_POST['org_manager_nonce'], 'create_org_manager')) {
            $this->handle_create_manager();
        }
        
        if (isset($_POST['assign_existing_user']) && wp_verify_nonce($_POST['assign_user_nonce'], 'assign_existing_user')) {
            $this->handle_assign_existing_user();
        }
        
        if (isset($_POST['remove_manager']) && wp_verify_nonce($_POST['remove_manager_nonce'], 'remove_org_manager')) {
            $this->handle_remove_manager();
        }
        
        // Get all organization managers
        $org_managers = get_users(array(
            'role' => 'organization_manager'
        ));
        
        // Get all users (excluding organization managers for the assignment dropdown)
        $all_users = get_users(array(
            'role__not_in' => array('organization_manager')
        ));
        
        // Get all organizations
        $organizations = get_posts(array(
            'post_type' => 'organization',
            'numberposts' => -1,
            'post_status' => 'publish'
        ));
        
        ?>
        <div class="wrap">
            <h1>Organization Managers</h1>
            
            <?php if (empty($organizations)): ?>
            <div class="notice notice-warning">
                <p><strong>No organizations found!</strong> You need to create organizations first before you can assign managers. 
                <a href="<?php echo admin_url('post-new.php?post_type=organization'); ?>">Create an organization</a> or 
                <a href="<?php echo admin_url('edit.php?post_type=organization'); ?>">view existing organizations</a>.</p>
            </div>
            <?php endif; ?>
            
            <style>
            .org-manager-forms {
                display: flex;
                gap: 20px;
                margin-bottom: 30px;
            }
            .org-manager-forms .card {
                flex: 1;
                margin: 0;
            }
            @media (max-width: 782px) {
                .org-manager-forms {
                    flex-direction: column;
                }
            }
            </style>
            
            <div class="org-manager-forms">
                <div class="card">
                    <h2>Assign Existing User as Organization Manager</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('assign_existing_user', 'assign_user_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="existing_user">Select User</label></th>
                            <td>
                                <select name="existing_user" id="existing_user" required>
                                    <option value="">Select a user</option>
                                    <?php foreach ($all_users as $user): ?>
                                        <option value="<?php echo $user->ID; ?>">
                                            <?php echo esc_html($user->display_name . ' (' . $user->user_login . ') - ' . $user->user_email); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">Select an existing user to convert to Organization Manager role.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="assign_organization">Assigned Organization</label></th>
                            <td>
                                <select name="assign_organization" id="assign_organization" required>
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?php echo $org->ID; ?>"><?php echo esc_html($org->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Assign as Organization Manager', 'primary', 'assign_existing_user'); ?>
                </form>
                </div>
                
                <div class="card">
                    <h2>Create New Organization Manager</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('create_org_manager', 'org_manager_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="username">Username</label></th>
                            <td><input type="text" name="username" id="username" class="regular-text" required /></td>
                        </tr>
                        <tr>
                            <th><label for="email">Email</label></th>
                            <td><input type="email" name="email" id="email" class="regular-text" required /></td>
                        </tr>
                        <tr>
                            <th><label for="first_name">First Name</label></th>
                            <td><input type="text" name="first_name" id="first_name" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="last_name">Last Name</label></th>
                            <td><input type="text" name="last_name" id="last_name" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="organization">Assigned Organization</label></th>
                            <td>
                                <select name="organization" id="organization" required>
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?php echo $org->ID; ?>"><?php echo esc_html($org->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Create Organization Manager', 'primary', 'create_manager'); ?>
                </form>
                </div>
            </div>
            
            <h2>Existing Organization Managers</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Assigned Organization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($org_managers)): ?>
                        <tr>
                            <td colspan="4">No organization managers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($org_managers as $manager): ?>
                            <?php
                            $assigned_org_id = get_user_meta($manager->ID, 'assigned_organization', true);
                            $assigned_org = $assigned_org_id ? get_post($assigned_org_id) : null;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($manager->display_name); ?></strong><br>
                                    <small><?php echo esc_html($manager->user_login); ?></small>
                                </td>
                                <td><?php echo esc_html($manager->user_email); ?></td>
                                <td>
                                    <?php if ($assigned_org): ?>
                                        <a href="<?php echo get_edit_post_link($assigned_org->ID); ?>">
                                            <?php echo esc_html($assigned_org->post_title); ?>
                                        </a>
                                    <?php else: ?>
                                        <em>No organization assigned</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo get_edit_user_link($manager->ID); ?>" class="button button-small">Edit User</a>
                                    <form method="post" style="display:inline-block; margin-left:10px;" onsubmit="return confirm('Are you sure you want to remove organization manager role from this user?');">
                                        <?php wp_nonce_field('remove_org_manager', 'remove_manager_nonce'); ?>
                                        <input type="hidden" name="remove_manager_id" value="<?php echo $manager->ID; ?>" />
                                        <input type="submit" name="remove_manager" value="Remove Role" class="button button-small button-link-delete" />
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    private function handle_create_manager() {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $organization_id = intval($_POST['organization']);
        
        // Ensure the organization_manager role exists before creating user
        $this->ensure_organization_manager_role_exists();
        
        // Generate random password
        $password = wp_generate_password(12, false);
        
        // Create user with subscriber role first (to avoid role issues)
        $user_data = array(
            'user_login' => $username,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_pass' => $password,
            'role' => 'subscriber'  // Create as subscriber first
        );
        
        $user_id = wp_insert_user($user_data);
        
        if (is_wp_error($user_id)) {
            echo '<div class="notice notice-error"><p>Error creating user: ' . $user_id->get_error_message() . '</p></div>';
        } else {
            // Now change to organization_manager role
            $user = new WP_User($user_id);
            $user->set_role('organization_manager');
            
            // Assign organization
            update_user_meta($user_id, 'assigned_organization', $organization_id);
            
            // Send password to user
            wp_new_user_notification($user_id, null, 'both');
            
            // Verify the role was set correctly
            $user_roles = $user->roles;
            $role_check = in_array('organization_manager', $user_roles) ? 'Organization Manager' : 'Failed to assign role';
            
            echo '<div class="notice notice-success"><p>Organization manager created successfully! Role: ' . $role_check . '. Login credentials have been sent to ' . esc_html($email) . '</p></div>';
        }
    }
    
    private function handle_assign_existing_user() {
        $user_id = intval($_POST['existing_user']);
        $organization_id = intval($_POST['assign_organization']);
        
        if (!$user_id || !$organization_id) {
            echo '<div class="notice notice-error"><p>Please select both a user and an organization.</p></div>';
            return;
        }
        
        $user = get_user_by('id', $user_id);
        $organization = get_post($organization_id);
        
        if (!$user || !$organization) {
            echo '<div class="notice notice-error"><p>Invalid user or organization selected.</p></div>';
            return;
        }
        
        // Ensure the organization_manager role exists
        $this->ensure_organization_manager_role_exists();
        
        // Change user role to organization manager
        $user->set_role('organization_manager');
        
        // Assign organization
        update_user_meta($user_id, 'assigned_organization', $organization_id);
        
        // Verify the role was set correctly
        $user = get_user_by('id', $user_id); // Refresh user data
        $user_roles = $user->roles;
        $role_check = in_array('organization_manager', $user_roles) ? 'Organization Manager' : 'Failed to assign role';
        
        echo '<div class="notice notice-success"><p>' . esc_html($user->display_name) . ' has been assigned as manager for ' . esc_html($organization->post_title) . '. Role: ' . $role_check . '</p></div>';
    }
    
    private function handle_remove_manager() {
        $user_id = intval($_POST['remove_manager_id']);
        
        if (!$user_id) {
            echo '<div class="notice notice-error"><p>Invalid user ID.</p></div>';
            return;
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            echo '<div class="notice notice-error"><p>User not found.</p></div>';
            return;
        }
        
        // Change role back to subscriber (default)
        $user->set_role('subscriber');
        
        // Remove organization assignment
        delete_user_meta($user_id, 'assigned_organization');
        
        echo '<div class="notice notice-success"><p>Organization manager role removed from ' . esc_html($user->display_name) . '</p></div>';
    }
    
    private function ensure_organization_manager_role_exists() {
        // Check if the role exists, if not create it
        if (!get_role('organization_manager')) {
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
                'delete_organizations' => false,
                'edit_others_organizations' => false,
                'delete_published_organizations' => false,
                'delete_others_organizations' => false,
                'manage_organization_terms' => false,
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
                'edit_others_unbc_events' => false,
                'delete_others_unbc_events' => false,
                'manage_unbc_event_terms' => true,
                'edit_unbc_event_terms' => true,
                'delete_unbc_event_terms' => false,
                'assign_unbc_event_terms' => true,
            ));
        }
        
        // Also ensure administrators have the capabilities
        $this->add_admin_capabilities();
    }
    
    private function add_admin_capabilities() {
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
}