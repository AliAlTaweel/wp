<?php
/* Template Name: Front Page */
get_header(); 
?>

<div class="homepage-content">
  <!-- Vertical Year List -->
  <div class="year-list" style="float: left; margin-right: 20px;">
    <h3>Years</h3>
    <ul style="list-style-type: none; padding: 0;">
      <?php
      for ($year = 2008; $year <= 2024; $year++) {
          echo '<li><a href="#' . esc_attr($year) . '">' . esc_html($year) . '</a></li>';
      }
      ?>
    </ul>
  </div>

  <!-- Display the content of the static page set as homepage -->
  <div class="event-content">
    <?php
    if (have_posts()) :
      while (have_posts()) : the_post();
        echo get_the_date();
        echo get_the_category_list(', ');
        the_content();
      endwhile;
    endif;
    ?>
  </div>
</div>

<?php
// Custom Query for 'custom_info'
$args = array(
    'post_type' => 'custom_info',
    'posts_per_page' => 9,
);
$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        
        // Get Year from Date
        $year = get_the_date('Y'); // Get the year from the post date
        
        // Add an ID to each event based on the year
        echo '<div id="' . esc_attr($year) . '">';
        echo '<h2>' . get_the_title() . '</h2>';
        echo '<div>' . get_the_content() . '</div>';
        
        // Display the published date
        echo '<p>Time: ' . get_the_date() . '</p>';
        
        // Get Start Date and End Date
        $start_date = get_field('start_date'); // ACF function to get the start date
        $end_date = get_field('end_date');     // ACF function to get the end date

        if ($start_date) {
            echo '<p>Start Date: ' . esc_html($start_date) . '</p>';
        } else {
            echo '<p>No Start Date set.</p>';
        }

        if ($end_date) {
            echo '<p>End Date: ' . esc_html($end_date) . '</p>';
        } else {
            echo '<p>No End Date set.</p>';
        }

        echo '</div>'; // Close event div
    }
} else {
    echo 'No Custom Info found.';
}
wp_reset_postdata();

get_footer(); 
?>  */