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

<div id="place-list">
    <!-- Places will be loaded here via AJAX -->
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

            // Send AJAX request to load events and places for the selected year
            fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "filter_events",
                    year: year
                })
            })
            .then(response => response.json())
            .then(data => {
                // Update event list and place list
                document.getElementById("event-list").innerHTML = data.events;
                document.getElementById("place-list").innerHTML = data.places;
                attachPlaceClickEvents(); // Re-attach click events for places
            })
            .catch(error => console.error("Error:", error));
        });
    });

    // Function to attach click events to places
    function attachPlaceClickEvents() {
        const placeLinks = document.querySelectorAll(".place-link");
        placeLinks.forEach(link => {
            link.addEventListener("click", function(event) {
                event.preventDefault(); // Prevent default link behavior

                const placeId = this.getAttribute("data-place");

                // Get the currently selected year
                const year = document.querySelector(".year-link.active")?.getAttribute("data-year");

                // Send AJAX request for events by place
                fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        action: "filter_events_by_place",
                        place_id: placeId,
                        year: year // Include the selected year in the request
                    })
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById("event-list").innerHTML = data;
                })
                .catch(error => console.error("Error:", error));
            });
        });
    }

    // Automatically load events for the first year (2008) by default

});
</script>

<?php get_footer(); ?>
