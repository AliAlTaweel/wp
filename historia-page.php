<?php
/* Template Name: Histoiria Page */
get_header(); 
?>


<div class="year-filter">
    <h3>Valitse Vuosi:</h3>
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
            event.preventDefault();

            // Toggle the active class for the clicked place link
            if (this.classList.contains("active")) {
                this.classList.remove("active");
            } else {
                placeLinks.forEach(l => l.classList.remove("active"));
                this.classList.add("active");
            }

            const placeId = this.getAttribute("data-place");
            const year = document.querySelector(".year-link.active")?.getAttribute("data-year");

            if (this.classList.contains("active")) {
                fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        action: "filter_events_by_place",
                        place_id: placeId,
                        year: year
                    })
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById("event-list").innerHTML = data;

                    // Scroll to the place heading after the events load
                    const placeHeading = document.getElementById(`place-${placeId}`);
                    if (placeHeading) {
                        placeHeading.scrollIntoView({ behavior: "smooth" });
                    }
                })
                .catch(error => console.error("Error:", error));
            } else {
                document.getElementById("event-list").innerHTML = "";
            }
        });
    });
}

   

</script>
<?php get_footer(); ?>