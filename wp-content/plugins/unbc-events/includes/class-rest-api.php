<?php
class UNBC_Events_REST_API {
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('unbc-events/v1', '/events', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_events'),
            'permission_callback' => '__return_true',
            'args' => array(
                'per_page' => array(
                    'default' => 10,
                    'sanitize_callback' => 'absint'
                ),
                'page' => array(
                    'default' => 1,
                    'sanitize_callback' => 'absint'
                ),
                'start_date' => array(
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'end_date' => array(
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'category' => array(
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'organization' => array(
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'featured' => array(
                    'sanitize_callback' => 'rest_sanitize_boolean'
                ),
                'search' => array(
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));
    }

    public function get_events($request) {
        $params = $request->get_params();
        
        $args = array(
            'post_type' => 'unbc_event',
            'post_status' => 'publish',
            'posts_per_page' => $params['per_page'],
            'paged' => $params['page'],
            'meta_query' => array(),
            'tax_query' => array()
        );

        // Date filtering
        if (!empty($params['start_date']) || !empty($params['end_date'])) {
            $date_query = array('relation' => 'AND');
            
            if (!empty($params['start_date'])) {
                $date_query[] = array(
                    'key' => 'event_date',
                    'value' => $params['start_date'],
                    'compare' => '>='
                );
            }
            
            if (!empty($params['end_date'])) {
                $date_query[] = array(
                    'key' => 'event_date',
                    'value' => $params['end_date'],
                    'compare' => '<='
                );
            }
            
            $args['meta_query'][] = $date_query;
        }

        // Category filtering
        if (!empty($params['category'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'event_category',
                'field' => 'slug',
                'terms' => $params['category']
            );
        }

        // Organization filtering
        if (!empty($params['organization'])) {
            $args['meta_query'][] = array(
                'key' => 'organization_id',
                'value' => $params['organization'],
                'compare' => '='
            );
        }

        // Featured filtering
        if (isset($params['featured']) && $params['featured']) {
            $args['meta_query'][] = array(
                'key' => 'featured',
                'value' => '1',
                'compare' => '='
            );
        }

        // Search functionality
        if (!empty($params['search'])) {
            $args['s'] = $params['search'];
        }

        $query = new WP_Query($args);
        $events = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $events[] = $this->format_event_data(get_the_ID());
            }
            wp_reset_postdata();
        }

        return rest_ensure_response(array(
            'events' => $events,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages
        ));
    }

    private function format_event_data($event_id) {
        $event = get_post($event_id);
        
        // Get meta data
        $event_date = get_post_meta($event_id, 'event_date', true);
        $start_time = get_post_meta($event_id, 'start_time', true) ?: '00:00';
        $end_time = get_post_meta($event_id, 'end_time', true) ?: '23:59';
        $location = get_post_meta($event_id, 'location', true);
        $building = get_post_meta($event_id, 'building', true);
        $room = get_post_meta($event_id, 'room', true);
        
        // Build full location string
        $full_location_parts = array_filter(array($location, $building, $room));
        $full_location = !empty($full_location_parts) ? implode(', ', $full_location_parts) : 'TBD';
        
        // Get organization
        $organization_id = get_post_meta($event_id, 'organization_id', true);
        $organization_name = '';
        if ($organization_id) {
            $organization = get_post($organization_id);
            $organization_name = $organization ? $organization->post_title : '';
        }
        
        // Get categories
        $categories = wp_get_post_terms($event_id, 'event_category');
        $category_data = array();
        foreach ($categories as $category) {
            $category_data[] = array(
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug
            );
        }

        return array(
            'id' => $event_id,
            'title' => $event->post_title,
            'description' => $event->post_content,
            'excerpt' => $event->post_excerpt,
            'date' => $event_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'location' => $location,
            'building' => $building,
            'room' => $room,
            'full_location' => $full_location,
            'cost' => get_post_meta($event_id, 'cost', true) ?: 'Free',
            'organization' => $organization_name,
            'categories' => $category_data,
            'featured_image' => get_the_post_thumbnail_url($event_id, 'large'),
            'registration_required' => get_post_meta($event_id, 'registration_required', true) === '1',
            'registration_link' => get_post_meta($event_id, 'registration_link', true),
            'contact_email' => get_post_meta($event_id, 'contact_email', true),
            'is_virtual' => get_post_meta($event_id, 'is_virtual', true) === '1',
            'virtual_link' => get_post_meta($event_id, 'virtual_link', true),
            'capacity' => get_post_meta($event_id, 'capacity', true),
            'featured' => get_post_meta($event_id, 'featured', true) === '1',
            'permalink' => get_permalink($event_id)
        );
    }
}