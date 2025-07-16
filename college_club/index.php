<?php
$page_title = "Home";
require_once 'includes/header.php';
require_once 'includes/config.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to College Club</h1>
        <p class="lead">Join us in our journey to explore, learn, and grow together!</p>
        <div class="mt-4">
            <a href="join.php" class="btn btn-primary btn-lg me-3">Join Now</a>
            <a href="#upcoming-events" class="btn btn-outline-light btn-lg">View Events</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="section-title">About Our Club</h2>
                <p class="lead">Empowering students through knowledge sharing, events, and community building.</p>
                <p>We are a vibrant community of students passionate about learning, networking, and making a difference. Our club organizes various events, workshops, and activities throughout the year to help students develop new skills and connect with like-minded individuals.</p>
                <a href="about.php" class="btn btn-outline-primary">Learn More</a>
            </div>
            <div class="col-lg-6">
                <img src="./assets/images/about.jpg" alt="About College Club" class="img-fluid rounded-lg shadow">
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section id="upcoming-events" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 section-title">Upcoming Events</h2>
        <div class="row">
            <?php
            // Fetch upcoming events from database
            $sql = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 3";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $event_date = new DateTime($row['event_date']);
                    $formatted_date = $event_date->format('M d, Y');
                    $formatted_time = $event_date->format('h:i A');
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 event-card">
                            <div class="position-relative">
                                <img src="/college_club/uploads/events/<?php echo htmlspecialchars($row['image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <div class="event-date"><?php echo $formatted_date; ?></div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <i class="far fa-clock me-2"></i> <?php echo $formatted_time; ?><br>
                                    <i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($row['venue']); ?>
                                </p>
                                <p class="card-text"><?php echo substr(htmlspecialchars($row['description']), 0, 100); ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="event-details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No upcoming events at the moment. Please check back soon!</p></div>';
            }
            ?>
        </div>
        <div class="text-center mt-4">
            <a href="events.php" class="btn btn-outline-primary">View All Events</a>
        </div>
    </div>
</section>

<!-- Gallery Preview -->


<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 section-title">What Our Members Say</h2>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" 
                             class="rounded-circle mb-3" width="80" alt="Member">
                        <p class="card-text">"Joining this club was the best decision of my college life. I've made great friends and learned so much!"</p>
                        <h5 class="mb-1">Utkarsh</h5>
                        <p class="text-muted small">Computer Science, 2023</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" 
                             class="rounded-circle mb-3" width="80" alt="Member">
                        <p class="card-text">"The workshops and events organized by the club have helped me develop skills that aren't taught in the classroom."</p>
                        <h5 class="mb-1">prince </h5>
                        <p class="text-muted small">Engineering, 2024</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" 
                             class="rounded-circle mb-3" width="80" alt="Member">
                        <p class="card-text">"Being part of this community has given me the confidence to take on leadership roles and grow professionally."</p>
                        <h5 class="mb-1">vishnu </h5>
                        <p class="text-muted small">Business Administration, 2023</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Stay Updated</h2>
                <p class="lead mb-4">Subscribe to our newsletter to receive the latest updates, event invitations, and club news.</p>
                <form id="newsletter-form" class="row g-3 justify-content-center" onsubmit="event.preventDefault(); subscribeNewsletter(this.email.value);">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="email" name="email" class="form-control form-control-lg" 
                                   placeholder="Enter your email" required>
                            <button class="btn btn-light btn-lg" type="submit">Subscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Back to top button -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="fas fa-arrow-up"></i>
</a>

<?php require_once 'includes/footer.php'; ?>
