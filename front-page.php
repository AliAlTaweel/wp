
<?php
/* Template Name: Front Page */
get_header(); 
?>

<div class="homepage-content">
  <?php
  // Display the content of the static page set as homepage
  if (have_posts()) :
    while (have_posts()) : the_post();
      the_content();
    endwhile;
  endif;
  ?>
</div>
<?php
// In your theme's template file (e.g., page.php or a custom template)
$args = array(
    'post_type' => 'custom_info',
    'posts_per_page' => -1,
);
$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        echo '<h2>' . get_the_title() . '</h2>';
        echo '<div>' . get_the_content() . '</div>';
        echo '<p>Time: ' . get_post_meta(get_the_ID(), 'time', true) . '</p>'; // Custom field for time
        echo '<p>Category: ' . get_the_terms(get_the_ID(), 'custom_category')[0]->name . '</p>'; // Custom taxonomy
    }
} else {
    echo 'No Custom Info found.';
}
wp_reset_postdata();

get_footer(); ?>