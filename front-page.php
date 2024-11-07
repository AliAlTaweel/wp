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

            // Remove active class from all links and add to the clicked one
            yearLinks.forEach(l => l.classList.remove("active"));
            this.classList.add("active");

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
   

    // Automatically load events by default all years
    fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            action: "filter_events",
          
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("event-list").innerHTML = data.events;
        document.getElementById("place-list").innerHTML = data.places;
        attachPlaceClickEvents();
    })
    .catch(error => console.error("Error:", error));
});

function attachPlaceClickEvents() {
    const placeLinks = document.querySelectorAll(".place-link");
    placeLinks.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default link behavior

            // Toggle the active class for the clicked place link
            if (this.classList.contains("active")) {
                // If the link is already active, remove the active class
                this.classList.remove("active");
            } else {
                // If it's not active, first remove active from any other link, then add it to the clicked one
                placeLinks.forEach(l => l.classList.remove("active"));
                this.classList.add("active");
            }

            // Optional: Additional functionality when a place is selected
            const placeId = this.getAttribute("data-place");

            // Get the currently selected year, if any
            const year = document.querySelector(".year-link.active")?.getAttribute("data-year");

            // Send AJAX request for events by place if needed (only if a place is selected)
            if (this.classList.contains("active")) {
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
            } else {
                // Clear the event list if the place is deselected
                document.getElementById("event-list").innerHTML = "";
            }
        });
    });
}
document.querySelectorAll('.place-link').forEach(function(icon) {
    icon.addEventListener('click', function() {
        // Get the table by its ID
        const table = document.getElementByClassName('.event-table');
        
        // Scroll smoothly to the table
        table.scrollIntoView({
            behavior: 'smooth', // Smooth scrolling effect
            block: 'start' // Scroll to the top of the table
        });
    });
});

</script>
<?php get_footer(); ?>
