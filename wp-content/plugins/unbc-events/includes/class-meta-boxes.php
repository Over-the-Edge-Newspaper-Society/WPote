<?php
class UNBC_Events_Meta_Boxes {
    private $user_roles_handler;
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('wp_loaded', array($this, 'init_user_roles_handler'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function init_user_roles_handler() {
        if (class_exists('UNBC_Events_User_Roles')) {
            $this->user_roles_handler = new UNBC_Events_User_Roles();
        }
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        
        global $post;
        if ($post && $post->post_type === 'organization') {
            wp_enqueue_script('unbc-org-restrictions', plugin_dir_url(dirname(__FILE__)) . 'js/organization-restrictions.js', array('jquery'), '1.0.0', true);
        }
    }

    public function add_meta_boxes() {
        add_meta_box(
            'event_details',
            'Event Details',
            array($this, 'event_details_callback'),
            'unbc_event',
            'normal',
            'high'
        );
        
        // Organization meta boxes
        add_meta_box(
            'org_basic_info',
            'Organization Information',
            array($this, 'org_basic_info_callback'),
            'organization',
            'normal',
            'high'
        );
        
        add_meta_box(
            'org_contact_info',
            'Contact Information',
            array($this, 'org_contact_info_callback'),
            'organization',
            'normal',
            'default'
        );
        
        add_meta_box(
            'org_social_media',
            'Social Media Links',
            array($this, 'org_social_media_callback'),
            'organization',
            'normal',
            'default'
        );
        
        add_meta_box(
            'org_additional_info',
            'Additional Information',
            array($this, 'org_additional_info_callback'),
            'organization',
            'normal',
            'default'
        );
    }

    public function event_details_callback($post) {
        wp_nonce_field('event_details_nonce', 'event_details_nonce');
        
        $event_date = get_post_meta($post->ID, 'event_date', true);
        $start_time = get_post_meta($post->ID, 'start_time', true);
        $end_time = get_post_meta($post->ID, 'end_time', true);
        $location = get_post_meta($post->ID, 'location', true);
        $building = get_post_meta($post->ID, 'building', true);
        $room = get_post_meta($post->ID, 'room', true);
        $cost = get_post_meta($post->ID, 'cost', true);
        $organization_id = get_post_meta($post->ID, 'organization_id', true);
        $registration_required = get_post_meta($post->ID, 'registration_required', true);
        $registration_link = get_post_meta($post->ID, 'registration_link', true);
        $contact_email = get_post_meta($post->ID, 'contact_email', true);
        $is_virtual = get_post_meta($post->ID, 'is_virtual', true);
        $virtual_link = get_post_meta($post->ID, 'virtual_link', true);
        $capacity = get_post_meta($post->ID, 'capacity', true);
        $featured = get_post_meta($post->ID, 'featured', true);
        $flyer_id = get_post_meta($post->ID, 'flyer_id', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="event_date">Event Date</label></th>
                <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" /></td>
            </tr>
            <tr>
                <th><label for="start_time">Start Time</label></th>
                <td><input type="time" id="start_time" name="start_time" value="<?php echo esc_attr($start_time); ?>" /></td>
            </tr>
            <tr>
                <th><label for="end_time">End Time</label></th>
                <td><input type="time" id="end_time" name="end_time" value="<?php echo esc_attr($end_time); ?>" /></td>
            </tr>
            <tr>
                <th><label for="location">Location</label></th>
                <td><input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" /></td>
            </tr>
            <tr>
                <th><label for="building">Building</label></th>
                <td><input type="text" id="building" name="building" value="<?php echo esc_attr($building); ?>" /></td>
            </tr>
            <tr>
                <th><label for="room">Room</label></th>
                <td><input type="text" id="room" name="room" value="<?php echo esc_attr($room); ?>" /></td>
            </tr>
            <tr>
                <th><label for="cost">Cost</label></th>
                <td><input type="text" id="cost" name="cost" value="<?php echo esc_attr($cost); ?>" placeholder="Free" /></td>
            </tr>
            <tr>
                <th><label for="organization_id">Organization</label></th>
                <td>
                    <?php
                    $current_user = wp_get_current_user();
                    $is_org_manager = in_array('organization_manager', $current_user->roles);
                    $assigned_org_id = $is_org_manager ? get_user_meta($current_user->ID, 'assigned_organization', true) : null;
                    
                    if ($is_org_manager && $assigned_org_id) {
                        // For organization managers, only show their assigned organization
                        $assigned_org = get_post($assigned_org_id);
                        if ($assigned_org) {
                            echo '<input type="hidden" id="organization_id" name="organization_id" value="' . $assigned_org_id . '" />';
                            echo '<p><strong>' . esc_html($assigned_org->post_title) . '</strong></p>';
                            echo '<p class="description">Events will be automatically associated with your organization.</p>';
                        } else {
                            echo '<p class="error">No organization assigned to your account. Please contact an administrator.</p>';
                        }
                    } else {
                        // For administrators and other users, show all organizations
                        $organizations = get_posts(array('post_type' => 'organization', 'numberposts' => -1));
                        echo '<select id="organization_id" name="organization_id">';
                        echo '<option value="">Select Organization</option>';
                        foreach ($organizations as $org) {
                            $selected = ($organization_id == $org->ID) ? 'selected' : '';
                            echo '<option value="' . $org->ID . '" ' . $selected . '>' . $org->post_title . '</option>';
                        }
                        echo '</select>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="registration_required">Registration Required</label></th>
                <td><input type="checkbox" id="registration_required" name="registration_required" value="1" <?php checked($registration_required, '1'); ?> /></td>
            </tr>
            <tr>
                <th><label for="registration_link">Registration Link</label></th>
                <td><input type="url" id="registration_link" name="registration_link" value="<?php echo esc_attr($registration_link); ?>" /></td>
            </tr>
            <tr>
                <th><label for="contact_email">Contact Email</label></th>
                <td><input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr($contact_email); ?>" /></td>
            </tr>
            <tr>
                <th><label for="is_virtual">Virtual Event</label></th>
                <td><input type="checkbox" id="is_virtual" name="is_virtual" value="1" <?php checked($is_virtual, '1'); ?> /></td>
            </tr>
            <tr>
                <th><label for="virtual_link">Virtual Link</label></th>
                <td><input type="url" id="virtual_link" name="virtual_link" value="<?php echo esc_attr($virtual_link); ?>" /></td>
            </tr>
            <tr>
                <th><label for="capacity">Capacity</label></th>
                <td><input type="number" id="capacity" name="capacity" value="<?php echo esc_attr($capacity); ?>" /></td>
            </tr>
            <tr>
                <th><label for="featured">Featured Event</label></th>
                <td><input type="checkbox" id="featured" name="featured" value="1" <?php checked($featured, '1'); ?> /></td>
            </tr>
            <tr>
                <th><label for="flyer_id">Event Flyer</label></th>
                <td>
                    <div class="flyer-upload">
                        <?php if ($flyer_id): 
                            $flyer_url = wp_get_attachment_image_src($flyer_id, 'thumbnail')[0];
                        ?>
                            <img src="<?php echo esc_url($flyer_url); ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />
                        <?php endif; ?>
                        <input type="hidden" id="flyer_id" name="flyer_id" value="<?php echo esc_attr($flyer_id); ?>" />
                        <button type="button" class="button" id="upload_flyer_button">
                            <?php echo $flyer_id ? 'Change Flyer' : 'Upload Flyer'; ?>
                        </button>
                        <?php if ($flyer_id): ?>
                            <button type="button" class="button" id="remove_flyer_button" style="margin-left: 10px;">Remove Flyer</button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload_flyer_button').click(function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose Event Flyer',
                    button: {
                        text: 'Choose Flyer'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#flyer_id').val(attachment.id);
                    $('.flyer-upload img').remove();
                    $('.flyer-upload').prepend('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;" />');
                    $('#upload_flyer_button').text('Change Flyer');
                    if (!$('#remove_flyer_button').length) {
                        $('#upload_flyer_button').after('<button type="button" class="button" id="remove_flyer_button" style="margin-left: 10px;">Remove Flyer</button>');
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '#remove_flyer_button', function(e) {
                e.preventDefault();
                $('#flyer_id').val('');
                $('.flyer-upload img').remove();
                $('#upload_flyer_button').text('Upload Flyer');
                $(this).remove();
            });
        });
        </script>
        <?php
    }

    // Organization meta box callbacks
    public function org_basic_info_callback($post) {
        wp_nonce_field('org_details_nonce', 'org_details_nonce');
        
        $email = get_post_meta($post->ID, 'org_email', true);
        $size = get_post_meta($post->ID, 'org_size', true);
        $founded_year = get_post_meta($post->ID, 'org_founded_year', true);
        $short_description = get_post_meta($post->ID, 'org_short_description', true);
        $membership_requirements = get_post_meta($post->ID, 'org_membership_requirements', true);
        $meeting_schedule = get_post_meta($post->ID, 'org_meeting_schedule', true);
        $original_image_path = get_post_meta($post->ID, 'org_original_image_path', true);
        
        $current_user = wp_get_current_user();
        $is_org_manager = in_array('organization_manager', $current_user->roles);
        
        ?>
        <p><strong>Note:</strong> Use the "Club/Organization Logo" box on the right to upload your organization's logo/image.</p>
        <?php if ($is_org_manager): ?>
        <p><strong>Organization Manager:</strong> You can edit most fields, but some administrative fields are restricted.</p>
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><label for="org_email">Email</label></th>
                <td><input type="email" id="org_email" name="org_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_size">Organization Size</label></th>
                <td>
                    <select id="org_size" name="org_size">
                        <option value="">Select size</option>
                        <option value="Small (1-10)" <?php selected($size, 'Small (1-10)'); ?>>Small (1-10)</option>
                        <option value="Medium (11-50)" <?php selected($size, 'Medium (11-50)'); ?>>Medium (11-50)</option>
                        <option value="Large (51+)" <?php selected($size, 'Large (51+)'); ?>>Large (51+)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="org_founded_year">Founded Year</label></th>
                <td><input type="number" id="org_founded_year" name="org_founded_year" value="<?php echo esc_attr($founded_year); ?>" min="1900" max="<?php echo date('Y'); ?>" /></td>
            </tr>
            <tr>
                <th><label for="org_short_description">Short Description</label></th>
                <td><textarea id="org_short_description" name="org_short_description" rows="3" class="large-text"><?php echo esc_textarea($short_description); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="org_membership_requirements">Membership Requirements</label></th>
                <td><textarea id="org_membership_requirements" name="org_membership_requirements" rows="2" class="large-text"><?php echo esc_textarea($membership_requirements); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="org_meeting_schedule">Meeting Schedule</label></th>
                <td><input type="text" id="org_meeting_schedule" name="org_meeting_schedule" value="<?php echo esc_attr($meeting_schedule); ?>" class="regular-text" placeholder="e.g., Every Tuesday at 6 PM" /></td>
            </tr>
            <tr>
                <th><label for="org_original_image_path">Original Image Path</label></th>
                <td>
                    <input type="text" id="org_original_image_path" name="org_original_image_path" value="<?php echo esc_attr($original_image_path); ?>" class="regular-text" placeholder="e.g., club_name/image.jpg" />
                    <p class="description">Legacy field for original image path (if migrated from old system)</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function org_contact_info_callback($post) {
        $president_name = get_post_meta($post->ID, 'org_president_name', true);
        $president_email = get_post_meta($post->ID, 'org_president_email', true);
        $contact_name = get_post_meta($post->ID, 'org_contact_name', true);
        $contact_email = get_post_meta($post->ID, 'org_contact_email', true);
        $office_location = get_post_meta($post->ID, 'org_office_location', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="org_president_name">President Name</label></th>
                <td><input type="text" id="org_president_name" name="org_president_name" value="<?php echo esc_attr($president_name); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_president_email">President Email</label></th>
                <td><input type="email" id="org_president_email" name="org_president_email" value="<?php echo esc_attr($president_email); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_contact_name">Primary Contact Name</label></th>
                <td><input type="text" id="org_contact_name" name="org_contact_name" value="<?php echo esc_attr($contact_name); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_contact_email">Primary Contact Email</label></th>
                <td><input type="email" id="org_contact_email" name="org_contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_office_location">Office Location</label></th>
                <td><input type="text" id="org_office_location" name="org_office_location" value="<?php echo esc_attr($office_location); ?>" class="regular-text" placeholder="e.g., Student Union Building Room 205" /></td>
            </tr>
        </table>
        <?php
    }
    
    public function org_social_media_callback($post) {
        $website = get_post_meta($post->ID, 'org_website', true);
        $facebook = get_post_meta($post->ID, 'org_facebook', true);
        $instagram = get_post_meta($post->ID, 'org_instagram', true);
        $twitter = get_post_meta($post->ID, 'org_twitter', true);
        $discord = get_post_meta($post->ID, 'org_discord', true);
        $linktree = get_post_meta($post->ID, 'org_linktree', true);
        $youtube = get_post_meta($post->ID, 'org_youtube', true);
        $registration_link = get_post_meta($post->ID, 'org_registration_link', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="org_website">Website</label></th>
                <td><input type="url" id="org_website" name="org_website" value="<?php echo esc_attr($website); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="org_facebook">Facebook</label></th>
                <td><input type="url" id="org_facebook" name="org_facebook" value="<?php echo esc_attr($facebook); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" /></td>
            </tr>
            <tr>
                <th><label for="org_instagram">Instagram</label></th>
                <td><input type="url" id="org_instagram" name="org_instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="https://instagram.com/username" /></td>
            </tr>
            <tr>
                <th><label for="org_twitter">Twitter/X</label></th>
                <td><input type="text" id="org_twitter" name="org_twitter" value="<?php echo esc_attr($twitter); ?>" class="regular-text" placeholder="@yourusername" /></td>
            </tr>
            <tr>
                <th><label for="org_discord">Discord Server</label></th>
                <td><input type="url" id="org_discord" name="org_discord" value="<?php echo esc_attr($discord); ?>" class="regular-text" placeholder="https://discord.gg/invite" /></td>
            </tr>
            <tr>
                <th><label for="org_linktree">Linktree</label></th>
                <td><input type="url" id="org_linktree" name="org_linktree" value="<?php echo esc_attr($linktree); ?>" class="regular-text" placeholder="https://linktr.ee/username" /></td>
            </tr>
            <tr>
                <th><label for="org_youtube">YouTube Channel</label></th>
                <td><input type="url" id="org_youtube" name="org_youtube" value="<?php echo esc_attr($youtube); ?>" class="regular-text" placeholder="https://youtube.com/channel/..." /></td>
            </tr>
            <tr>
                <th><label for="org_registration_link">Registration Link</label></th>
                <td><input type="url" id="org_registration_link" name="org_registration_link" value="<?php echo esc_attr($registration_link); ?>" class="regular-text" placeholder="Registration or signup link" /></td>
            </tr>
        </table>
        <?php
    }
    
    public function org_additional_info_callback($post) {
        $status = get_post_meta($post->ID, 'org_status', true);
        $founded_date = get_post_meta($post->ID, 'org_founded_date', true);
        $approval_date = get_post_meta($post->ID, 'org_approval_date', true);
        $registration_date = get_post_meta($post->ID, 'org_registration_date', true);
        $is_department = get_post_meta($post->ID, 'org_is_department', true);
        
        $current_user = wp_get_current_user();
        $is_org_manager = in_array('organization_manager', $current_user->roles);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="org_status">Organization Status</label></th>
                <td>
                    <select id="org_status" name="org_status">
                        <option value="">Select status</option>
                        <option value="Established" <?php selected($status, 'Established'); ?>>Established</option>
                        <option value="New" <?php selected($status, 'New'); ?>>New</option>
                        <option value="Inactive" <?php selected($status, 'Inactive'); ?>>Inactive</option>
                    </select>
                </td>
            </tr>
            <?php if (!$is_org_manager): ?>
            <tr>
                <th><label for="org_is_department">UNBC Department</label></th>
                <td>
                    <label>
                        <input type="checkbox" id="org_is_department" name="org_is_department" value="1" <?php checked($is_department, '1'); ?> />
                        This is a UNBC department (will not show in clubs listing but can be linked to events)
                    </label>
                    <p class="description">Check this for academic departments, administrative offices, etc. that aren't student clubs.</p>
                </td>
            </tr>
            <?php else: ?>
            <tr>
                <th>UNBC Department Status</th>
                <td>
                    <p><?php echo $is_department ? 'Yes - This is a UNBC department' : 'No - This is a student club/organization'; ?></p>
                    <input type="hidden" name="org_is_department" value="<?php echo esc_attr($is_department); ?>" />
                    <p class="description"><em>This field can only be changed by administrators.</em></p>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (!$is_org_manager): ?>
            <tr>
                <th><label for="org_founded_date">Founded Date</label></th>
                <td><input type="date" id="org_founded_date" name="org_founded_date" value="<?php echo esc_attr($founded_date); ?>" /></td>
            </tr>
            <tr>
                <th><label for="org_approval_date">Approval Date</label></th>
                <td><input type="date" id="org_approval_date" name="org_approval_date" value="<?php echo esc_attr($approval_date); ?>" /></td>
            </tr>
            <tr>
                <th><label for="org_registration_date">Registration Date</label></th>
                <td><input type="date" id="org_registration_date" name="org_registration_date" value="<?php echo esc_attr($registration_date); ?>" /></td>
            </tr>
            <?php else: ?>
            <?php if ($founded_date): ?>
            <tr>
                <th>Founded Date</th>
                <td>
                    <p><?php echo esc_html(date('F j, Y', strtotime($founded_date))); ?></p>
                    <input type="hidden" name="org_founded_date" value="<?php echo esc_attr($founded_date); ?>" />
                    <p class="description"><em>This field can only be changed by administrators.</em></p>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($approval_date): ?>
            <tr>
                <th>Approval Date</th>
                <td>
                    <p><?php echo esc_html(date('F j, Y', strtotime($approval_date))); ?></p>
                    <input type="hidden" name="org_approval_date" value="<?php echo esc_attr($approval_date); ?>" />
                    <p class="description"><em>This field can only be changed by administrators.</em></p>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($registration_date): ?>
            <tr>
                <th>Registration Date</th>
                <td>
                    <p><?php echo esc_html(date('F j, Y', strtotime($registration_date))); ?></p>
                    <input type="hidden" name="org_registration_date" value="<?php echo esc_attr($registration_date); ?>" />
                    <p class="description"><em>This field can only be changed by administrators.</em></p>
                </td>
            </tr>
            <?php endif; ?>
            <?php endif; ?>
        </table>
        <?php
    }

    public function save_meta_boxes($post_id) {
        // Skip if not our nonce
        if (!isset($_POST['event_details_nonce']) && !isset($_POST['org_details_nonce'])) {
            return;
        }
        
        // Verify nonce for events
        if (isset($_POST['event_details_nonce']) && !wp_verify_nonce($_POST['event_details_nonce'], 'event_details_nonce')) {
            return;
        }
        
        // Verify nonce for organizations
        if (isset($_POST['org_details_nonce']) && !wp_verify_nonce($_POST['org_details_nonce'], 'org_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save event meta
        if (get_post_type($post_id) === 'unbc_event') {
            $meta_fields = array(
                'event_date', 'start_time', 'end_time', 'location', 'building', 'room',
                'cost', 'organization_id', 'registration_link', 'contact_email',
                'virtual_link', 'capacity', 'flyer_id'
            );

            foreach ($meta_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                }
            }

            // Handle checkboxes
            $checkbox_fields = array('registration_required', 'is_virtual', 'featured');
            foreach ($checkbox_fields as $field) {
                update_post_meta($post_id, $field, isset($_POST[$field]) ? '1' : '0');
            }
        }
        
        // Save organization meta
        if (get_post_type($post_id) === 'organization') {
            $current_user = wp_get_current_user();
            $is_org_manager = in_array('organization_manager', $current_user->roles);
            
            $org_fields = array(
                'org_email', 'org_size', 'org_founded_year', 'org_short_description',
                'org_membership_requirements', 'org_meeting_schedule', 'org_president_name',
                'org_president_email', 'org_contact_name', 'org_contact_email',
                'org_office_location', 'org_website', 'org_facebook', 'org_instagram',
                'org_twitter', 'org_discord', 'org_linktree', 'org_youtube', 
                'org_registration_link', 'org_status', 'org_founded_date', 'org_approval_date', 'org_registration_date',
                'org_original_image_path'
            );

            // Fields that organization managers cannot edit
            $restricted_fields = array('org_founded_date', 'org_approval_date', 'org_registration_date');

            foreach ($org_fields as $field) {
                if (isset($_POST[$field])) {
                    // Skip restricted fields for organization managers
                    if ($is_org_manager && in_array($field, $restricted_fields)) {
                        continue;
                    }
                    
                    // Handle URL fields
                    if (in_array($field, array('org_website', 'org_facebook', 'org_instagram', 'org_discord', 'org_linktree', 'org_youtube', 'org_registration_link'))) {
                        update_post_meta($post_id, $field, esc_url_raw($_POST[$field]));
                    }
                    // Handle email fields
                    elseif (strpos($field, 'email') !== false) {
                        update_post_meta($post_id, $field, sanitize_email($_POST[$field]));
                    }
                    // Handle textarea fields
                    elseif (in_array($field, array('org_short_description', 'org_membership_requirements'))) {
                        update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
                    }
                    // Handle regular text fields
                    else {
                        update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                    }
                }
            }
            
            // Handle organization checkbox - only for non-org managers
            if (!$is_org_manager) {
                update_post_meta($post_id, 'org_is_department', isset($_POST['org_is_department']) ? '1' : '0');
            } else {
                // For org managers, preserve the existing value
                if (isset($_POST['org_is_department'])) {
                    update_post_meta($post_id, 'org_is_department', sanitize_text_field($_POST['org_is_department']));
                }
            }
            
            // Prevent organization managers from changing post title and slug
            if ($is_org_manager) {
                // Don't allow title changes
                remove_action('save_post', 'wp_update_post');
                
                // Get original post data to preserve title and slug
                $original_post = get_post($post_id);
                if ($original_post) {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_title' => $original_post->post_title,
                        'post_name' => $original_post->post_name,
                        'post_status' => $original_post->post_status
                    ));
                }
            }
        }
    }
}