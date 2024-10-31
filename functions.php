<?php
 // ========= greate Events plugin ===============
 function create_custom_post_type() {
     register_post_type('custom_info',
     array(
         'labels'      => array(
             'name'          => __('Tapahtumat'),
             'singular_name' => __('Tapahtuma'),
             'add_new_item'  => __('Add New Event'),
             'Add New Post'  => __('Add New Event'),
             'edit_item'     => __('Edit Info'),
             'all_items'     => __('Kaikki Tapahtumija'),
            ),
            'public'      => true,
            'has_archive' => true,
            
            'supports'    => array('title', 'editor', 'custom-fields'), // Include fields you want
            'taxonomies'  => array('category'), // This allows standard categories
            'rewrite'     => array('slug' => 'custom-info'),
         
            )
        );
    }
    add_action('init', 'create_custom_post_type');
    
// ========= great Years  ===============
// Register custom taxonomy 'Year'
function register_year_taxonomy() {
    register_taxonomy('year', 'custom_info', array(
        'label' => __('Year'),
        'rewrite' => array('slug' => 'year'),
        'hierarchical' => true,
    ));
    
    // Automatically create terms for each year from 2008 to 2024
    for ($i = 2008; $i <= 2024; $i++) {
        if (!term_exists($i, 'year')) {
            wp_insert_term($i, 'year');
        }
    }
}
add_action('init', 'register_year_taxonomy');


// ========= great Date taxonomy  ===============
// Register custom taxonomy 'Event Dates' for 'custom_info' post type
function register_event_dates_taxonomy() {
    register_taxonomy('event_dates', 'custom_info', array(
        'labels' => array(
            'name' => __('Event Dates'),
            'singular_name' => __('Event Date')
        ),
        'public' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'event-dates'),
    ));
}
add_action('init', 'register_event_dates_taxonomy');
// ========= great Place taxonomy  ===============
function create_custom_taxonomy() {
    register_taxonomy(
        'Place',
        'custom_info',
        array(
            'label' => __('Place'),
            'rewrite' => array('slug' => 'custom-category'),
            'hierarchical' => true, // Set to true to have parent/child relationship
        )
    );

    // Add predefined categories
    $categories = array('Valtakunnallinen perusohjelma', 
    'Muut valtakunnalliset', 'Paikkalliset UUsimaa',
     'Paikkallliset Päijät-Häme', 'Paikalliset Pirkanmaa',
      'Paikalliset Pohjanmaa', 'Paikalliset Lounais-Suomi');
    
    foreach ($categories as $category) {
        if (!term_exists($category, 'Place')) {
            wp_insert_term($category, 'Place');
        }
    }
}
add_action('init', 'create_custom_taxonomy');

function theme_register_menus() {
    register_nav_menu('top-menu', __('Top Menu'));
}
add_action('init', 'theme_register_menus');



// ========= handle AJAX =================
// Add AJAX action for logged-in and guest users
add_action('wp_ajax_filter_events', 'filter_events');
add_action('wp_ajax_nopriv_filter_events', 'filter_events');

// Fetch and display Organizer in the AJAX table
// Fetch and display Organizer and Event Details in the AJAX table

// before change   01 
function filter_events(): void {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;
    $selected_place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : null;

    $args = array(
        'post_type' => 'custom_info',
        'posts_per_page' => 10, // Retrieve all events
    );

    // Add meta query for year filtering only if a year is selected
    if ($selected_year) {
        $args['meta_query'] = array(
            array(
                'key' => 'start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            )
        );
    }

    // Add tax query for place filtering only if a place is selected
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

    ob_start();

    // Start output with table structure
    echo '<table class="event-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Tapahtuma</th>';
    echo '<th>Details</th>';
    echo '<th>Paikka</th>';
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
            
            $start_date = get_post_meta(get_the_ID(), 'start_date', true);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            $formatted_start_date = date('d.m.Y', strtotime($start_date));
            $formatted_end_date = date('d.m.Y', strtotime($end_date));

            $place = get_the_terms(get_the_ID(), 'Place');
            $responsible = get_post_meta(get_the_ID(), '_responsible_name', true);
            $organizer = get_post_meta(get_the_ID(), '_organizer_name', true);
            $details = get_the_content(); // Retrieve the event details

            echo '<tr>';
            echo '<td>' . esc_html(get_the_title()) . '</td>';
            echo '<td>' . wp_trim_words(esc_html($details), 20, '...') . '</td>'; // Show first 20 words of details
            echo '<td>' . ($place ? esc_html($place[0]->name) : 'N/A') . '</td>';
            echo '<td>' . esc_html( $formatted_start_date) . '</td>';
            echo '<td>' . esc_html( $formatted_end_date) . '</td>';
            echo '<td>' . esc_html($responsible) . '</td>';
            echo '<td>' . esc_html($organizer) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No events found for this year.</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Fetch Places
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'hide_empty' => false,
    ));

    $places_output = '<h3>Places:</h3><ul>';
    foreach ($places as $place) {
        $places_output .= '<li><a href="#" class="place-link" data-place="' . esc_attr($place->term_id) . '">' . esc_html($place->name) . '</a></li>';
    }
    $places_output .= '</ul>';

    // Return as JSON
    wp_send_json(array(
        'events' => ob_get_clean(),
        'places' => $places_output,
    ));

    wp_die();
}






function enqueue_ajax_script() {
    // Localize the script with the new AJAX URL
    wp_localize_script('your-script-handle', 'ajax_events', array(
        'url' => get_template_directory_uri() . '/ajax-events-handler.php',
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');


// ========= To force wordpress to take last version of style.css ==========
function my_theme_enqueue_styles() {
    wp_enqueue_style('main-styles', get_stylesheet_uri(), array(), time());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

// ================ add orginaizer names ==============
// ========== Add Organizer Dropdown in Event Post Type ==========

// Define a list of organizer names
function get_organizer_list() {
    return [
        'Sami',
        'Anna',
        'John',
        'Alex',
        'Sara'
    ];
}

// Add metabox for Organizer selection
function add_organizer_metabox() {
    add_meta_box(
        'organizer_metabox',       // ID of the metabox
        __('Mentor Name'),       // Title of the metabox
        'organizer_metabox_callback', // Callback function to display content
        'custom_info',              // Post type
        'side'                      // Location of the metabox
    );
}
add_action('add_meta_boxes', 'add_organizer_metabox');

// Display the organizer dropdown in the metabox
function organizer_metabox_callback($post) {
    // Retrieve the current organizer value if it exists
    $selected_organizer = get_post_meta($post->ID, '_organizer_name', true);
    $organizers = get_organizer_list();
    ?>
    <label for="organizer_name"><?php _e('Select Mentor:', 'text_domain'); ?></label>
    <select name="organizer_name" id="organizer_name" class="postbox">
        <?php foreach ($organizers as $organizer): ?>
            <option value="<?php echo esc_attr($organizer); ?>" <?php selected($selected_organizer, $organizer); ?>>
                <?php echo esc_html($organizer); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Save the selected organizer when the post is saved
function save_organizer_metabox_data($post_id) {
    // Verify nonce and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['organizer_name'])) {
        update_post_meta($post_id, '_organizer_name', sanitize_text_field($_POST['organizer_name']));
    }
}
add_action('save_post', 'save_organizer_metabox_data');

//==============================================================
// ================ add oresponsible names ==============
// ========== Add responsible Dropdown in Event Post Type ==========

// Define a list of responsible names
function get_responsible_list() {
    return [
        'Meri',
        'Anna',
        'Satu',
        'Oksana',
        'Natali'
    ];
}

// Add metabox for responsible selection
function add_responsible_metabox() {
    add_meta_box(
        'responsible_metabox',       // ID of the metabox
        __('Resbonsible Name'),       // Title of the metabox
        'responsible_metabox_callback', // Callback function to display content
        'custom_info',              // Post type
        'side'                      // Location of the metabox
    );
}
add_action('add_meta_boxes', 'add_responsible_metabox');

// Display the oresponsible dropdown in the metabox
function responsible_metabox_callback($post) {
    // Retrieve the current responsible value if it exists
    $selected_responsible = get_post_meta($post->ID, '_responsible_name', true);
    $responsibles = get_responsible_list();
    ?>
    <label for="responsible_name"><?php _e('Select Responsible:', 'text_domain'); ?></label>
    <select name="responsible_name" id="responsible_name" class="postbox">
        <?php foreach ($responsibles as $responsible): ?>
            <option value="<?php echo esc_attr($responsible); ?>" <?php selected($selected_responsible, $responsible); ?>>
                <?php echo esc_html($responsible); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Save the selected organizer when the post is saved
function save_responsible_metabox_data($post_id) {
    // Verify nonce and permissions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['responsible_name'])) {
        update_post_meta($post_id, '_responsible_name', sanitize_text_field($_POST['responsible_name']));
    }
}
add_action('save_post', 'save_organizer_metabox_data');
//==============================================================
// Add AJAX action for loading places
add_action('wp_ajax_load_places', 'load_places');
add_action('wp_ajax_nopriv_load_places', 'load_places');

function load_places() {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;

    // Query places
    $places = get_terms(array(
        'taxonomy' => 'Place',
        'hide_empty' => true,
    ));

    ob_start();
    echo '<h3>Select Place:</h3>';
    echo '<ul>';
    
    foreach ($places as $place) {
        echo '<li>';
        echo '<a href="#" class="place-link" data-place="' . esc_attr($place->slug) . '">';
        echo esc_html($place->name);
        echo '</a>';
        echo '</li>';
    }
    
    echo '</ul>';
    
    echo ob_get_clean();
    wp_die();
}

// Modify the filter_events function to handle events by place
add_action('wp_ajax_filter_events_by_place', 'filter_events_by_place');
add_action('wp_ajax_nopriv_filter_events_by_place', 'filter_events_by_place');

function filter_events_by_place() {
    $selected_place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : null;
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;

    $args = array(
        'post_type' => 'custom_info',
        'posts_per_page' => -1, // Retrieve all events for the selected place
        'meta_query' => array()
    );

    if ($selected_year) {
        $args['meta_query'][] = array(
            'key' => 'start_date',
            'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
            'compare' => 'BETWEEN',
            'type' => 'DATE'
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

    ob_start();

    // Start output with table structure
    echo '<table class="event-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Event</th>';
    echo '<th>Details</th>';
    echo '<th>Place</th>';
    echo '<th>Start Date</th>';
    echo '<th>End Date</th>';
    echo '<th>Vastuulliset</th>';
    echo '<th>Mentor</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $start_date = get_post_meta(get_the_ID(), 'start_date', true);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            $formatted_start_date = date('d.m.Y', strtotime($start_date));
            $formatted_end_date = date('d.m.Y', strtotime($end_date));

            $place = get_the_terms(get_the_ID(), 'Place');
            $responsible = get_post_meta(get_the_ID(), '_responsible_name', true);
            $organizer = get_post_meta(get_the_ID(), '_organizer_name', true);
            $details = get_the_content(); // Retrieve the event details

            echo '<tr>';
            echo '<td>' . esc_html(get_the_title()) . '</td>';
            echo '<td>' . wp_trim_words(esc_html($details), 20, '...') . '</td>'; // Show first 20 words of details
            echo '<td>' . ($place ? esc_html($place[0]->name) : 'N/A') . '</td>';
            echo '<td>' . esc_html( $formatted_start_date) . '</td>';
            echo '<td>' . esc_html( $formatted_end_date) . '</td>';
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

    echo ob_get_clean();
    wp_die();
}
// ================= Add Time plugin ==================
