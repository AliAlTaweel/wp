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
            'show_ui' => true, // Show in the WordPress admin UI
            'show_in_menu' => true, // Show in admin menu
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

    // Check if the places are being set in the form (assuming you have checkboxes or clickable items)
    if (isset($_POST['event_place'])) {
        // Save the selected places as an array
        $places = array_map('intval', $_POST['event_place']);
        
        // Use wp_set_object_terms to associate the event with the selected places
        wp_set_object_terms($post_id, $places, 'Place');
    }
}
add_action('save_post', 'save_event_place');



function event_place_meta_box_callback($post) {
    // Get all the available places
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'orderby' => 'name',
        'hide_empty' => false,
    ));
    
    // Get the current places assigned to this event
    $selected_places = wp_get_object_terms($post->ID, 'Place');
    $selected_place_ids = !empty($selected_places) ? wp_list_pluck($selected_places, 'term_id') : [];

    // Display the clickable list (checkboxes)
    echo '<ul>';
    foreach ($places as $place) {
        echo '<li>';
        echo '<label>';
        echo '<input type="checkbox" name="event_place[]" value="' . esc_attr($place->term_id) . '" ' . (in_array($place->term_id, $selected_place_ids) ? 'checked' : '') . '>';
        echo esc_html($place->name);
        echo '</label>';
        echo '</li>';
    }
    echo '</ul>';
}
function change_add_new_category_text($translated_text, $text, $domain) {
    if ($domain === 'default' && $text === 'Add new category') {
        $translated_text = 'Add New Place'; // Change to your desired text
    }
    return $translated_text;
}
add_filter('gettext', 'change_add_new_category_text', 10, 3);



// ================ Taxonomies Ends ==================

// ================ Add Menus ==================

function theme_register_menus() {
    register_nav_menu('top-menu', __('Top Menu'));
}
add_action('init', 'theme_register_menus');

// =============================================



// ========= To force wordpress to take last version of style.css ==========
function my_theme_enqueue_styles() {
    wp_enqueue_style('main-styles', get_stylesheet_uri(), array(), time());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
// ======================================================



//===========================================================================
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

        // Use the separate function to generate the table
        echo generate_event_table($query);

        wp_reset_postdata();
    }

    echo ob_get_clean();
    wp_die();
}

function filter_events(): void {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;
    $selected_place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : null;

    $args = array(
        'post_type' => 'event_listing',
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num',
        'meta_key' => '_event_start_date',
        'order' => 'ASC',
    );
    
    if ($selected_year) {
        $args['meta_query'] = array(
            array(
                'key' => '_event_start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE',
            )
        );
    }
    
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

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $place_terms = get_the_terms(get_the_ID(), 'Place');

            if (!$place_terms || is_wp_error($place_terms)) {
                $events_by_place['Ei paikkaa'][] = get_the_ID();
            } else {
                foreach ($place_terms as $place) {
                    $events_by_place[$place->name][] = get_the_ID();
                }
            }
        }
    }
    wp_reset_postdata();

    ob_start();

    if (!empty($events_by_place)) {
        foreach ($events_by_place as $place_name => $event_ids) {
            echo '<h2>' . esc_html($place_name) . '</h2>';

            $place_query_args = array(
                'post_type' => 'event_listing',
                'post__in' => $event_ids,
                'orderby' => 'meta_value_num',
                'meta_key' => '_event_start_date',
                'order' => 'ASC',
            );

            $place_query = new WP_Query($place_query_args);
            echo generate_event_table($place_query);
            wp_reset_postdata();
        }
    } else {
        echo '<p>Tälle vuodelle ei löytynyt tapahtumia.</p>';
    }
    
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'hide_empty' => false,
    ));

    $places_output = '<ul>';
    foreach ($places as $place) {
        $places_output .= '<li><a href="#" class="place-link" data-place="'
            . esc_attr($place->term_id) . '">' . esc_html($place->name) . '</a></li>';
    }
    $places_output .= '</ul>';

    wp_send_json(array(
        'events' => ob_get_clean(),
        'places' => $places_output,
    ));
    
    wp_die();
}

function generate_event_table($query) {
    ob_start();
    
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
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $start_date = get_post_meta(get_the_ID(), '_event_start_date', true);
            $end_date = get_post_meta(get_the_ID(), '_event_end_date', true);

            $formatted_start_date = ($start_date && strtotime($start_date))
                ? date('d.m.Y', strtotime($start_date))
                : '-';
            $formatted_end_date = ($end_date && strtotime($end_date))
                ? date('d.m.Y', strtotime($end_date))
                : '-';

            $responsible = get_post_meta(get_the_ID(), '_responsible_name', true);
            $organizer = get_post_meta(get_the_ID(), '_organizer_name', true);
            $details = wp_trim_words(get_the_content(), 20, '...');

            echo '<tr>';
            echo '<td><a href="' . esc_url(get_permalink()) . '" target="_blank">' . esc_html(get_the_title()) . '</a></td>';
            echo '<td>' . esc_html($details) . '</td>';
            echo '<td>' . esc_html($formatted_start_date) . '</td>';
            echo '<td>' . esc_html($formatted_end_date) . '</td>';
            echo '<td>' . esc_html($responsible) . '</td>';
            echo '<td>' . esc_html($organizer) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">Tapahtumia ei löytynyt.</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    return ob_get_clean();
}

add_action('wp_ajax_filter_events', 'filter_events');
add_action('wp_ajax_nopriv_filter_events', 'filter_events');

add_action('wp_ajax_filter_events_by_place', 'filter_events_by_place');
add_action('wp_ajax_nopriv_filter_events_by_place', 'filter_events_by_place');

function add_historia_body_class($classes) {
    if (is_page('historia-page')) { // Use the slug of the page or its ID (e.g., is_page(123))
        $classes[] = 'historia-page';
    }
    return $classes;
}
add_filter('body_class', 'add_historia_body_class');
// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = 
