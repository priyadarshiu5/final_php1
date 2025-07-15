<?php
$page_title = "Event Details";
require_once 'includes/header.php';
require_once 'includes/config.php';

// Check if event ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: events.php');
    exit();
}

$event_id = (int)$_GET['id'];

// Fetch event details from database
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No event found with this ID
    header('Location: events.php?error=event_not_found');
    exit();
}

$event = $result->fetch_assoc();
$page_title = $event['title'] . ' | ' . SITE_NAME;

// Format date and time
$event_date = new DateTime($event['event_date']);
$formatted_date = $event_date->format('l, F j, Y');
$formatted_time = $event_date->format('g:i A');
?>

<!-- Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4"><?php echo htmlspecialchars($event['title']); ?></h1>
                <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                    <div class="text-muted">
                        <i class="far fa-calendar-alt me-2"></i> <?php echo $formatted_date; ?>
                    </div>
                    <div class="text-muted">
                        <i class="far fa-clock me-2"></i> <?php echo $formatted_time; ?>
                    </div>
                    <?php if (!empty($event['location'])): ?>
                    <div class="text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($event['location']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center mt-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/college_club">Home</a></li>
                        <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['title']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Event Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <?php if (!empty($event['image'])): ?>
                    <img src="/college_club/uploads/events/<?php echo htmlspecialchars($event['image']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <?php if (!empty($event['category'])): ?>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($event['category']); ?></span>
                            <?php endif; ?>
                            <span class="badge bg-secondary"><?php echo $formatted_date; ?></span>
                        </div>
                        
                        <div class="event-content mb-4">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        
                        <?php if (!empty($event['registration_link'])): ?>
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="<?php echo htmlspecialchars($event['registration_link']); ?>" 
                               class="btn btn-primary btn-lg px-4 me-md-2" target="_blank">
                                <i class="fas fa-user-plus me-2"></i> Register Now
                            </a>
                            <button class="btn btn-outline-secondary btn-lg px-4" onclick="addToCalendar()">
                                <i class="far fa-calendar-plus me-2"></i> Add to Calendar
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Event Location -->
                <?php if (!empty($event['location'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i> Event Location
                        </h5>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Venue:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="mb-0"><strong>Date:</strong> <?php echo $formatted_date; ?></p>
                            <p class="mb-0"><strong>Time:</strong> <?php echo $formatted_time; ?></p>
                        </div>
                        <div class="ratio ratio-16x9">
                            <?php
                            // Encode the location for the Google Maps URL
                            $encoded_location = urlencode($event['location']);
                            // Default coordinates (can be set to your college's default location)
                            $default_coords = '40.7128,-74.0060'; // Default to New York
                            ?>
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDQyJzQ3LjMiTiA3NMKwMjAnMzIuMyJX!5e0!3m2!1sen!2sus!4v1529288032799&q=<?php echo $encoded_location; ?>"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Share Event -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-share-alt text-primary me-2"></i> Share This Event
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" 
                               class="btn btn-outline-primary rounded-circle" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&text=<?php echo urlencode($event['title']); ?>" 
                               class="btn btn-outline-info rounded-circle" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&title=<?php echo urlencode($event['title']); ?>" 
                               class="btn btn-outline-primary rounded-circle" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode($event['title']); ?>&body=<?php echo urlencode("Check out this event: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" 
                               class="btn btn-outline-secondary rounded-circle">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <button class="btn btn-outline-dark rounded-circle" onclick="copyToClipboard()" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-info-circle text-primary me-2"></i> Event Details
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Date</h6>
                                        <p class="mb-0"><?php echo $formatted_date; ?></p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Time</h6>
                                        <p class="mb-0"><?php echo $formatted_time; ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php if (!empty($event['location'])): ?>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Location</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($event['location']); ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($event['category'])): ?>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Category</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($event['category']); ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <?php if (!empty($event['registration_link'])): ?>
                        <div class="d-grid mt-4">
                            <a href="<?php echo htmlspecialchars($event['registration_link']); ?>" 
                               class="btn btn-primary" target="_blank">
                                <i class="fas fa-user-plus me-2"></i> Register Now
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Upcoming Events -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">
                            <i class="far fa-calendar-check text-primary me-2"></i> Upcoming Events
                        </h5>
                        <?php
                        // Fetch upcoming events (excluding current event)
                        $current_date = date('Y-m-d H:i:s');
                        $upcoming_sql = "SELECT id, title, event_date, location FROM events 
                                        WHERE event_date > ? AND id != ? 
                                        ORDER BY event_date ASC LIMIT 3";
                        $stmt = $conn->prepare($upcoming_sql);
                        $stmt->bind_param("si", $current_date, $event_id);
                        $stmt->execute();
                        $upcoming_events = $stmt->get_result();
                        
                        if ($upcoming_events->num_rows > 0):
                            while($upcoming = $upcoming_events->fetch_assoc()):
                                $upcoming_date = new DateTime($upcoming['event_date']);
                                $upcoming_formatted_date = $upcoming_date->format('M j, Y');
                        ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 bg-light text-center p-2 me-3" style="width: 80px;">
                                <div class="fw-bold text-primary"><?php echo $upcoming_date->format('M'); ?></div>
                                <div class="h4 mb-0"><?php echo $upcoming_date->format('d'); ?></div>
                                <div class="small"><?php echo $upcoming_date->format('D'); ?></div>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    <a href="event-details.php?id=<?php echo $upcoming['id']; ?>">
                                        <?php echo htmlspecialchars($upcoming['title']); ?>
                                    </a>
                                </h6>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-clock me-1"></i> <?php echo $upcoming_date->format('g:i A'); ?>
                                    <?php if (!empty($upcoming['location'])): ?>
                                    <span class="ms-2">
                                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($upcoming['location']); ?>
                                    </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <p class="text-muted">No upcoming events at the moment.</p>
                        <?php endif; ?>
                        
                        <div class="mt-3 text-end">
                            <a href="events.php" class="btn btn-sm btn-outline-primary">View All Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Events -->
<?php
// Fetch related events (same category)
if (!empty($event['category'])) {
    $related_sql = "SELECT id, title, event_date, location, image FROM events 
                    WHERE category = ? AND id != ? AND event_date > ? 
                    ORDER BY event_date ASC LIMIT 6";
    $stmt = $conn->prepare($related_sql);
    $stmt->bind_param("sis", $event['category'], $event_id, $current_date);
    $stmt->execute();
    $related_events = $stmt->get_result();
    
    if ($related_events->num_rows > 0):
?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">You Might Also Like</h3>
            <a href="events.php?category=<?php echo urlencode($event['category']); ?>" class="btn btn-outline-primary">
                View All in <?php echo htmlspecialchars($event['category']); ?>
            </a>
        </div>
        
        <div class="row g-4">
            <?php while($related = $related_events->fetch_assoc()): 
                $related_date = new DateTime($related['event_date']);
                $related_formatted_date = $related_date->format('M j, Y');
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if (!empty($related['image'])): ?>
                    <img src="/college_club/uploads/events/<?php echo htmlspecialchars($related['image']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($related['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($event['category']); ?></span>
                            <small class="text-muted"><?php echo $related_formatted_date; ?></small>
                        </div>
                        <h5 class="card-title">
                            <a href="event-details.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h5>
                        <?php if (!empty($related['location'])): ?>
                        <p class="card-text text-muted small">
                            <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($related['location']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="event-details.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                        <?php if (!empty($related['registration_link'])): ?>
                        <a href="<?php echo htmlspecialchars($related['registration_link']); ?>" 
                           class="btn btn-sm btn-primary float-end" target="_blank">
                            Register
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php 
    endif;
}
?>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Don't Miss Out on Our Events</h2>
                <p class="lead mb-4">Subscribe to our newsletter to stay updated on upcoming events, workshops, and activities.</p>
                <form class="row g-2 justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                            <button class="btn btn-light btn-lg" type="submit">Subscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
// Add to Calendar functionality
function addToCalendar() {
    // Format the event details for Google Calendar
    const eventTitle = '<?php echo addslashes($event['title']); ?>';
    const eventDate = '<?php echo $event_date->format('Ymd\THi00'); ?>';
    const duration = 120; // 2 hours in minutes
    
    // Calculate end time
    const endDate = new Date('<?php echo $event_date->format('Y-m-d\TH:i:s'); ?>');
    endDate.setMinutes(endDate.getMinutes() + duration);
    const eventEndDate = endDate.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
    
    const eventLocation = '<?php echo addslashes($event['location']); ?>';
    const eventDetails = '<?php echo addslashes(strip_tags($event['description'])); ?>';
    
    // Create Google Calendar URL
    const googleCalendarUrl = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(eventTitle)}&dates=${eventDate}/${eventEndDate}&details=${encodeURIComponent(eventDetails)}&location=${encodeURIComponent(eventLocation)}&sf=true&output=xml`;
    
    // Open the calendar in a new tab
    window.open(googleCalendarUrl, '_blank');
    
    // Show a success message
    const toast = new bootstrap.Toast(document.getElementById('calendarToast'));
    toast.show();
}

// Copy to clipboard functionality
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        // Show tooltip
        const tooltip = new bootstrap.Tooltip(document.querySelector('[data-bs-toggle="tooltip"]'), {
            title: 'Link copied!',
            trigger: 'manual'
        });
        tooltip.show();
        
        // Hide tooltip after 2 seconds
        setTimeout(() => {
            tooltip.hide();
        }, 2000);
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="calendarToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Event Added</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <i class="fas fa-check-circle text-success me-2"></i> Event has been added to your calendar!
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
