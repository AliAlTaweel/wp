<?php
 // ========= greate Events plugin ===============
 function create_custom_post_type() {
     register_post_type('custom_info',
     array(
         'labels'      => array(
             'name'          => __('Events'),
             'singular_name' => __('Event'),
             'add_new_item'  => __('Add New Event'),
             'Add New Post'  => __('Add New Event'),
             'edit_item'     => __('Edit Info'),
             'all_items'     => __('All Events'),
            ),
            'public'      => true,
            'has_archive' => true,
            
            'supports'    => array('title', 'editor', 'custom-fields'), // Include fields you want
            'taxonomies'  => array('category'), // This allows standard categories
            'rewrite'     => array('slug' => 'custom-info'),
            'Start'     => array('Event Start at' => 'Date,Time'),
            'End'     => array('Event End At ' => 'Date,Time'),
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



// ========= handle JQUERY =================
// Add AJAX action for logged-in and guest users
add_action('wp_ajax_filter_events', 'filter_events');
add_action('wp_ajax_nopriv_filter_events', 'filter_events');

function filter_events() {
    // Get the selected year from the AJAX request
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;

    // Prepare query arguments
    $args = array(
        'post_type' => 'custom_info',
        'posts_per_page' => 9,
    );

    // If a year is selected, modify the query to filter by start_date
    if ($selected_year) {
        $args['meta_query'] = array(
            array(
                'key' => 'start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            ),
        );
    }

    // Execute the query
    $query = new WP_Query($args);

    // Start output buffer
    ob_start();

    // Display the events
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<div>' . get_the_content() . '</div>';
            echo '<p>Date: ' . get_the_date() . '</p>';
        }
    } else {
        echo 'No events found for this year.';
    }

    wp_reset_postdata();

    // Get the buffered output and end output buffering
    $output = ob_get_clean();
    
    // Return the response
    echo $output;
    wp_die(); // This is required to terminate immediately and return a proper response
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
