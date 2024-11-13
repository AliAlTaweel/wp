<?php

// ========= great Places  ===============
function register_taxonomies() {
    register_taxonomy(
        'Place',
        'event_listing',
        array(
            'label' => __('Place'),
            'rewrite' => array('slug' => 'custom-category'),
            'hierarchical' => true,
            'show_ui' => false, // Show in the WordPress admin UI
            'show_in_menu' => false, // Show in admin menu
            'show_in_rest' => true, // Enable for block editor if using
        )
    );

    // Add predefined categories if they don't exist
    $categories = array(
        'Valtakunnallinen perusohjelma',
        'Muut valtakunnalliset', 
        'Paikkalliset Uusimaa',
        'Paikkallliset Päijät-Häme', 
        'Paikalliset Pirkanmaa',
        'Paikalliset Pohjanmaa', 
        'Paikalliset Lounais-Suomi'
    );
    
    foreach ($categories as $category) {
        if (!term_exists($category, 'Place')) {
            wp_insert_term($category, 'Place');
        }
    }
}
add_action('init', 'register_taxonomies');


function save_event_place($post_id) {
    // Check if this is an 'event_listing' post type
    if (get_post_type($post_id) !== 'event_listing') {
        return;
    }

    // Check if the user has the necessary permissions to save the post
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the place is being set in the form (assuming you have a custom field or a select box for place)
    if (isset($_POST['event_place'])) {
        $place_id = intval($_POST['event_place']);
        
        // Use wp_set_object_terms to associate the event with the selected place
        wp_set_object_terms($post_id, $place_id, 'Place');
    }
}
add_action('save_post', 'save_event_place');

function add_event_place_meta_box() {
    add_meta_box(
        'event_place', 
        __('Place'), 
        'event_place_meta_box_callback', 
        'event_listing', 
        'side', 
        'default'
    );
}
add_action('add_meta_boxes', 'add_event_place_meta_box');

function event_place_meta_box_callback($post) {
    // Get all the available places
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'orderby' => 'name',
        'hide_empty' => false,
    ));
    
    // Get the current place assigned to this event
    $selected_place = wp_get_object_terms($post->ID, 'Place');
    $selected_place_id = !empty($selected_place) ? $selected_place[0]->term_id : '';

    // Display the select box
    echo '<select name="event_place" id="event_place">';
    echo '<option value="">Select Place</option>';
    foreach ($places as $place) {
        echo '<option value="' . esc_attr($place->term_id) . '" ' . selected($selected_place_id, $place->term_id, false) . '>';
        echo esc_html($place->name);
        echo '</option>';
    }
    echo '</select>';
}

// ================ Taxonomies Ends ==================

// ================ Add Menus ==================

function theme_register_menus() {
    register_nav_menu('top-menu', __('Top Menu'));
}
add_action('init', 'theme_register_menus');

// ========= handle AJAX =================
// Add AJAX action for logged-in and guest users


// ========= To force wordpress to take last version of style.css ==========
function my_theme_enqueue_styles() {
    wp_enqueue_style('main-styles', get_stylesheet_uri(), array(), time());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
// ============================    ==========================



//===========================
// Modify the filter_events_by_place function to handle events by place
function filter_events_by_place() {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;

    // Fetch all places
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'hide_empty' => false,
    ));

    ob_start();

    // Loop through each place and get events for each one
    foreach ($places as $place) {
        // Display the place name as a heading with a unique ID
        echo '<h2 id="place-' . esc_attr($place->term_id) . '">' . esc_html($place->name) . '</h2>';

        // Set up the query arguments for events under this place and year
        $args = array(
            'post_type' => 'event_listing',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'Place',
                    'field' => 'term_id',
                    'terms' => $place->term_id,
                ),
            ),
            'meta_query' => array(),
            'orderby' => 'meta_value_num',
            'meta_key' => '_event_start_date',
            'order' => 'ASC',
        );

        // Add year filter if selected
        if ($selected_year) {
            $args['meta_query'][] = array(
                'key' => '_event_start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            );
        }

        $query = new WP_Query($args);

        // Display events table for each place
        echo '<table class="event-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Tapahtumat</th>';
        echo '<th>Kuvaus</th>';
        echo '<th>Alku Päivä</th>';
        echo '<th>Loppu Päivä</th>';
        echo '<th>Vastuulliset</th>';
        echo '<th>Mentor</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $start_date = get_post_meta(get_the_ID(),  '_event_start_date', true);
                $end_date = get_post_meta(get_the_ID(),  '_event_end_date', true);
                $formatted_start_date = date('d.m.Y', strtotime($start_date));
                $formatted_end_date = date('d.m.Y', strtotime($end_date));

                $responsible = get_post_meta(get_the_ID(), '_responsible_name', true);
                $organizer = get_post_meta(get_the_ID(), '_organizer_name', true);
                $details = get_the_content();

                echo '<tr>';
                echo '<td>' . esc_html(get_the_title()) . '</td>';
                echo '<td>' . wp_trim_words(esc_html($details), 20, '...') . '</td>';
                echo '<td>' . esc_html($formatted_start_date) . '</td>';
                echo '<td>' . esc_html($formatted_end_date) . '</td>';
                echo '<td>' . esc_html($responsible) . '</td>';
                echo '<td>' . esc_html($organizer) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">No events found for this place.</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
        wp_reset_postdata();
    }

    echo ob_get_clean();
    wp_die();
}

// =================  ==================

function filter_events(): void {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;
    $selected_place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : null;

    $args = array(
        'post_type' => 'event_listing',  // Correct post type
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num',
        'meta_key' => '_event_start_date',
        'order' => 'ASC',
    );

    // Filter by year if a year is specified
    if ($selected_year) {
        $args['meta_query'] = array(
            array(
                'key' => '_event_start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            )
        );
    }

    // Filter by place if a specific place is selected
    if ($selected_place_id) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'Place',
                'field' => 'term_id',
                'terms' => $selected_place_id,
            ),
        );
    }

    $query = new WP_Query($args);
    $events_by_place = [];

    // Group events by their associated places
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $place_terms = get_the_terms(get_the_ID(), 'Place');

            if ($place_terms && !is_wp_error($place_terms)) {
                foreach ($place_terms as $place) {
                    $events_by_place[$place->name][] = array(
                        'title' => get_the_title(),
                        'details' => wp_trim_words(get_the_content(), 20, '...'),
                        'start_date' => date('d.m.Y', strtotime(get_post_meta(get_the_ID(), '_event_start_date', true))),
                        'end_date' => date('d.m.Y', strtotime(get_post_meta(get_the_ID(), '_event_end_date', true))),
                        'responsible' => get_post_meta(get_the_ID(), '_responsible_name', true),
                        'organizer' => get_post_meta(get_the_ID(), '_organizer_name', true)
                    );
                }
            }
        }
    }
    wp_reset_postdata();

    ob_start();

    // Output events grouped by place in a table format
    if (!empty($events_by_place)) {
        foreach ($events_by_place as $place_name => $events) {
            echo '<h2>' . esc_html($place_name) . '</h2>';
            echo '<table class="event-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Tapahtuma</th>';
            echo '<th>Kuvaus</th>';
            echo '<th>Alku Päivä</th>';
            echo '<th>Loppu Päivä</th>';
            echo '<th>Vastuulliset</th>';
            echo '<th>Mentor</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($events as $event) {
                echo '<tr>';
                echo '<td>' . esc_html($event['title']) . '</td>';
                echo '<td>' . esc_html($event['details']) . '</td>';
                echo '<td>' . esc_html($event['start_date']) . '</td>';
                echo '<td>' . esc_html($event['end_date']) . '</td>';
                echo '<td>' . esc_html($event['responsible']) . '</td>';
                echo '<td>' . esc_html($event['organizer']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        }
    } else {
        echo '<p>No events found for this selection.</p>';
    }

    // Generate the list of places for the sidebar
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'hide_empty' => false,
    ));

    $places_output = '<h3>Paikkat:</h3><ul>';
    foreach ($places as $place) {
        $places_output .= '<li><a href="#" class="place-link" data-place="'
            . esc_attr($place->term_id) . '">' . esc_html($place->name) . '</a></li>';
    }
    $places_output .= '</ul>';

    // Send the output as JSON
    wp_send_json(array(
        'events' => ob_get_clean(),
        'places' => $places_output,
    ));

    wp_die();
}

add_action('wp_ajax_filter_events', 'filter_events');
add_action('wp_ajax_nopriv_filter_events', 'filter_events');

add_action('wp_ajax_filter_events_by_place', 'filter_events_by_place');
add_action('wp_ajax_nopriv_filter_events_by_place', 'filter_events_by_place');




