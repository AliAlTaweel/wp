<?php
/* Template Name: Front Page */
get_header(); 
?>

<div class="year-filter">
    <h3>Select Year:</h3>
    <ul>
        <?php for ($year = 2008; $year <= 2024; $year++): ?>
            <li>
                <a href="#" class="year-link" data-year="<?php echo esc_attr($year); ?>">
                    <?php echo esc_html($year); ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</div>

<div id="event-list">
    <!-- Events will be loaded here via AJAX -->
</div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    const yearLinks = document.querySelectorAll(".year-link");

    yearLinks.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent the default link behavior

            const year = this.getAttribute("data-year");

            // Send AJAX request
            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "filter_events",
                    year: year
                })
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("event-list").innerHTML = data;
            })
            .catch(error => console.error("Error:", error));
        });
    });
});
</script>

<?php get_footer(); ?>