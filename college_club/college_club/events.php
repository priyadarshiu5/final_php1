<?php
$page_title = "Events";
require_once 'includes/header.php';
require_once 'includes/config.php';
?>

<!-- Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4">Our Events</h1>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/college_club">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Events</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title m-0">Upcoming Events</h2>
            <a href="#past-events" class="btn btn-outline-primary">View Past Events</a>
        </div>
        
        <div class="row">
            <?php
            // Fetch upcoming events
            $sql = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $event_date = new DateTime($row['event_date']);
                    $formatted_date = $event_date->format('M d, Y');
                    $formatted_time = $event_date->format('h:i A');
                    $day = $event_date->format('d');
                    $month = $event_date->format('M');
                    ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 event-card">
                            <div class="row g-0">
                                <div class="col-md-4 position-relative">
                                    <div class="event-date-overlay">
                                        <span class="event-day"><?php echo $day; ?></span>
                                        <span class="event-month"><?php echo $month; ?></span>
                                    </div>
                                    <img src="./uploads/events/<?php echo htmlspecialchars($row['image']); ?>" 
                                         class="img-fluid rounded-start h-100" alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         style="object-fit: cover; min-height: 200px;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body h-100 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                                            <span class="badge bg-primary">Upcoming</span>
                                        </div>
                                        <p class="card-text text-muted mb-3">
                                            <i class="far fa-calendar-alt me-2"></i> <?php echo $formatted_date; ?><br>
                                            <i class="far fa-clock me-2"></i> <?php echo $formatted_time; ?><br>
                                            <i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($row['venue']); ?>
                                        </p>
                                        <p class="card-text flex-grow-1">
                                            <?php 
                                            $description = htmlspecialchars($row['description']);
                                            echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                                            ?>
                                        </p>
                                        <div class="mt-auto">
                                            <a href="event-details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm me-2">
                                                View Details
                                            </a>
                                            <?php if (!empty($row['registration_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($row['registration_link']); ?>" 
                                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                                    Register Now
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No upcoming events scheduled at the moment. Please check back later!
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Past Events -->
<section id="past-events" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title mb-4">Past Events</h2>
        
        <div class="row">
            <?php
            // Fetch past events
            $sql = "SELECT * FROM events WHERE event_date < NOW() ORDER BY event_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $event_date = new DateTime($row['event_date']);
                    $formatted_date = $event_date->format('M d, Y');
                    $formatted_time = $event_date->format('h:i A');
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="./uploads/events/<?php echo htmlspecialchars($row['image']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>"
                                     style="height: 180px; object-fit: cover;">
                                <div class="event-date-overlay-sm">
                                    <span class="event-day"><?php echo $event_date->format('d'); ?></span>
                                    <span class="event-month"><?php echo $event_date->format('M'); ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo $formatted_date; ?>
                                    <span class="mx-2">•</span>
                                    <i class="far fa-clock me-1"></i> <?php echo $formatted_time; ?>
                                </p>
                                <p class="card-text">
                                    <?php 
                                    $description = htmlspecialchars($row['description']);
                                    echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="event-details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    View Details <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No past events available.
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Event Categories -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Event Categories</h2>
            <p class="lead">Explore our events by category</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <a href="events.php?category=workshops" class="text-decoration-none">
                    <div class="card category-card h-100 border-0 text-center p-4 hover-shadow">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-laptop-code fa-2x"></i>
                        </div>
                        <h5>Workshops</h5>
                        <p class="text-muted small mb-0">Hands-on learning experiences</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="events.php?category=seminars" class="text-decoration-none">
                    <div class="card category-card h-100 border-0 text-center p-4 hover-shadow">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <h5>Seminars</h5>
                        <p class="text-muted small mb-0">Expert talks and discussions</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="events.php?category=competitions" class="text-decoration-none">
                    <div class="card category-card h-100 border-0 text-center p-4 hover-shadow">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                        <h5>Competitions</h5>
                        <p class="text-muted small mb-0">Test your skills and win prizes</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="events.php?category=social" class="text-decoration-none">
                    <div class="card category-card h-100 border-0 text-center p-4 hover-shadow">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5>Social Events</h5>
                        <p class="text-muted small mb-0">Networking and fun activities</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Event Calendar -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Event Calendar</h3>
                        <div id="event-calendar">
                            <!-- Calendar will be initialized via JavaScript -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Loading calendar...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="mb-4">Have an event idea?</h2>
        <p class="lead mb-4">We're always looking for new and exciting event ideas from our members.</p>
        <a href="contact.php" class="btn btn-light btn-lg me-3">Suggest an Event</a>
        <a href="join.php" class="btn btn-outline-light btn-lg">Become a Member</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<!-- Initialize FullCalendar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // This would be replaced with actual API call to fetch events
    // For demo purposes, we'll use a placeholder
    setTimeout(function() {
        document.getElementById('event-calendar').innerHTML = `
            <div class="text-center p-4">
                <img src="/college_club/assets/images/calendar-placeholder.png" alt="Event Calendar" class="img-fluid rounded">
                <p class="mt-3">Interactive calendar showing upcoming events. In a full implementation, this would be powered by a calendar library like FullCalendar.</p>
                <a href="#" class="btn btn-primary">View Full Calendar</a>
            </div>
        `;
    }, 1500);
});
</script>
