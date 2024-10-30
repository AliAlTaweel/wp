<?php
// Ensure this is only accessible via AJAX
if (!defined('DOING_AJAX') || !DOING_AJAX) {
    exit;
}

// Load WordPress functionality
require_once('../../../wp-load.php');

function filter_events_by_year() {
    $selected_year = isset($_POST['year']) ? intval($_POST['year']) : null;

    $args = array(
        'post_type' => 'custom_info',
        'posts_per_page' => 9,
    );

    // Filter by selected year if provided
    if ($selected_year) {
        $args['meta_query'] = array(
            array(
                'key' => 'start_date',
                'value' => array($selected_year . '-01-01', $selected_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE',
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<div>' . get_the_content() . '</div>';
            echo '<p>Date: ' . get_the_date() . '</p>';

            $start_date = get_post_meta(get_the_ID(), 'start_date', true);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            
            echo '<p>Start Date: ' . esc_html($start_date) . '</p>';
            echo '<p>End Date: ' . esc_html($end_date) . '</p>';
        }
    } else {
        echo 'No events found for this year.';
    }

    wp_reset_postdata();
    exit;
}

filter_events_by_year();