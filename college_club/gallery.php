<?php
$page_title = "Gallery";
require_once 'includes/header.php';
require_once 'includes/config.php';
?>

<!-- Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4">Photo Gallery</h1>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/college_club">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Filters -->
<section class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <input type="radio" class="btn-check" name="gallery-filter" id="filter-all" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="filter-all">All Photos</label>
                        
                        <input type="radio" class="btn-check" name="gallery-filter" id="filter-workshops" autocomplete="off">
                        <label class="btn btn-outline-primary" for="filter-workshops">Workshops</label>
                        
                        <input type="radio" class="btn-check" name="gallery-filter" id="filter-events" autocomplete="off">
                        <label class="btn btn-outline-primary" for="filter-events">Events</label>
                        
                        <input type="radio" class="btn-check" name="gallery-filter" id="filter-meetups" autocomplete="off">
                        <label class="btn btn-outline-primary" for="filter-meetups">Meetups</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Gallery -->
<section class="py-5">
    <div class="container">
        <div class="row g-4" id="gallery-container">
            <?php
            // Sample gallery items - in a real app, these would come from a database
            $gallery_items = [
                ['id' => 1, 'title' => 'Annual Tech Fest 2023', 'category' => 'events', 'image' => 'college_club/uploads/images/annual tech fest.jpg', 'date' => '2023-10-15'],
                ['id' => 2, 'title' => 'Web Development Workshop', 'category' => 'workshops', 'image' => 'college_club/uploads/images/web development workshop.jpg', 'date' => '2023-09-22'],
                ['id' => 3, 'title' => 'Hackathon Winners', 'category' => 'events', 'image' => 'college_club/uploads/images/hackathon winners.jpg', 'date' => '2023-08-05'],
                ['id' => 4, 'title' => 'Coding Bootcamp', 'category' => 'workshops', 'image' => 'college_club/uploads/images/coding bootcamp.jpg', 'date' => '2023-07-18'],
                ['id' => 5, 'title' => 'Alumni Meetup', 'category' => 'meetups', 'image' => 'college_club/uploads/images/ai and ml.jpg', 'date' => '2023-06-30'],
                ['id' => 6, 'title' => 'Robotics Workshop', 'category' => 'workshops', 'image' => 'college_club/uploads/images/robotics.jpg', 'date' => '2023-05-12'],
                ['id' => 7, 'title' => 'Cultural Fest', 'category' => 'events', 'image' => 'college_club/uploads/images/cultural fest.jpg', 'date' => '2023-04-22'],
                ['id' => 8, 'title' => 'Startup Networking', 'category' => 'meetups', 'image' => 'college_club/uploads/images/startup.jpg', 'date' => '2023-03-15'],
                ['id' => 9, 'title' => 'AI & ML Seminar', 'category' => 'workshops', 'image' => 'college_club/uploads/images/ai and ml.jpg', 'date' => '2023-02-08'],
                ['id' => 10, 'title' => 'Sports Day', 'category' => 'events', 'image' => 'college_club/uploads/images/sports day.jpg', 'date' => '2023-01-20'],
                ['id' => 11, 'title' => 'Coding Competition', 'category' => 'events', 'image' => 'college_club/uploads/images/coding competition.jpg', 'date' => '2022-12-05'],
                ['id' => 12, 'title' => 'Industry Meetup', 'category' => 'meetups', 'image' => 'college_club/uploads/images/industry meetup.jpg', 'date' => '2022-11-18'],
            ];

            foreach ($gallery_items as $item) {
                $formatted_date = date('M d, Y', strtotime($item['date']));
                ?>
                <div class="col-lg-4 col-md-6 gallery-item" data-category="<?php echo $item['category']; ?>">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="gallery-image-container">
                            <img src="/college_club/uploads/images/<?php echo $item['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                 style="height: 220px; object-fit: cover; cursor: pointer;"
                                 onclick="openLightbox('<?php echo $item['id']; ?>')">
                            <div class="gallery-overlay">
                                <div class="gallery-overlay-content">
                                    <h5 class="text-white mb-1"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <p class="text-white-50 small mb-0"><?php echo $formatted_date; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <!-- Load More Button -->
        <div class="text-center mt-5">
            <button id="load-more" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span class="btn-text">Load More</span>
            </button>
        </div>
    </div>
</section>

<!-- Photo of the Month -->
<section class="py-5">
    <div class="container">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="row g-0">
                <div class="col-lg-6">
                    <img src="/college_club/assets/images/featured-photo.jpg" 
                         class="img-fluid h-100" 
                         alt="Photo of the Month"
                         style="min-height: 400px; object-fit: cover;">
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="p-4 p-lg-5">
                        <span class="badge bg-primary mb-3">Photo of the Month</span>
                        <h2>Celebrating Innovation</h2>
                        <p class="lead">Our students showcasing their projects at the Annual Tech Exhibition 2023.</p>
                        <p>This year's exhibition saw over 50 innovative projects from various departments, demonstrating the creativity and technical skills of our students. The event was graced by industry leaders and academicians who provided valuable feedback to the participants.</p>
                        <a href="#" class="btn btn-primary mt-3">View Event Gallery</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Upload (for members) -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Share Your Photos</h2>
                <p class="lead mb-4">Are you a club member? Share your photos from our events!</p>
                <a href="#" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i> Upload Photos
                </a>
                <a href="login.php" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i> Member Login
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Photos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="galleryUploadForm">
                    <div class="mb-3">
                        <label for="photoTitle" class="form-label">Photo Title</label>
                        <input type="text" class="form-control" id="photoTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="photoCategory" class="form-label">Category</label>
                        <select class="form-select" id="photoCategory" required>
                            <option value="">Select a category</option>
                            <option value="workshops">Workshops</option>
                            <option value="events">Events</option>
                            <option value="meetups">Meetups</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="photoUpload" class="form-label">Select Photos</label>
                        <input class="form-control" type="file" id="photoUpload" multiple required>
                        <div class="form-text">You can select multiple photos (JPEG, PNG, max 5MB each)</div>
                    </div>
                    <div class="mb-3">
                        <label for="photoDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="photoDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="galleryUploadForm" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Upload Photos</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="lightboxImage" src="" class="img-fluid" alt="">
                <div class="lightbox-caption text-white mt-3">
                    <h4 id="lightboxTitle" class="mb-1"></h4>
                    <p id="lightboxDate" class="text-white-50 mb-0"></p>
                </div>
            </div>
            <div class="modal-footer justify-content-between border-0">
                <button type="button" class="btn btn-outline-light" id="prevPhoto">
                    <i class="fas fa-chevron-left me-2"></i> Previous
                </button>
                <div>
                    <button type="button" class="btn btn-light me-2">
                        <i class="far fa-heart"></i>
                    </button>
                    <button type="button" class="btn btn-light me-2">
                        <i class="fas fa-download"></i>
                    </button>
                    <button type="button" class="btn btn-light">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-outline-light" id="nextPhoto">
                    Next <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
// Gallery Filtering
const filterButtons = document.querySelectorAll('input[name="gallery-filter"]');
const galleryItems = document.querySelectorAll('.gallery-item');

filterButtons.forEach(button => {
    button.addEventListener('change', function() {
        const filter = this.id.replace('filter-', '');
        
        galleryItems.forEach(item => {
            if (filter === 'all' || item.dataset.category === filter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Lightbox functionality
let currentImageIndex = 0;
const galleryImages = <?php echo json_encode($gallery_items); ?>;

function openLightbox(id) {
    currentImageIndex = galleryItems.findIndex(item => item.id === id);
    updateLightbox();
    
    const lightboxModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    lightboxModal.show();
}

function updateLightbox() {
    const currentImage = galleryImages[currentImageIndex];
    document.getElementById('lightboxImage').src = `/college_club/assets/images/gallery/${currentImage.image}`;
    document.getElementById('lightboxTitle').textContent = currentImage.title;
    document.getElementById('lightboxDate').textContent = new Date(currentImage.date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

document.getElementById('prevPhoto').addEventListener('click', () => {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightbox();
});

document.getElementById('nextPhoto').addEventListener('click', () => {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateLightbox();
});

// Load more functionality
let isLoading = false;
document.getElementById('load-more').addEventListener('click', function() {
    if (isLoading) return;
    
    const button = this;
    const spinner = button.querySelector('.spinner-border');
    const buttonText = button.querySelector('.btn-text');
    
    // Show loading state
    isLoading = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Loading...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // In a real app, you would fetch more items from the server here
        console.log('Loading more gallery items...');
        
        // Reset button state
        spinner.classList.add('d-none');
        buttonText.textContent = 'No more items';
        button.disabled = true;
        isLoading = false;
    }, 1500);
});

// Handle form submission
document.getElementById('galleryUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = document.querySelector('#uploadModal .btn-primary');
    const spinner = submitButton.querySelector('.spinner-border');
    const buttonText = submitButton.querySelector('.btn-text');
    
    // Show loading state
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Uploading...';
    submitButton.disabled = true;
    
    // Simulate upload
    setTimeout(() => {
        // In a real app, you would handle the file upload here
        console.log('Files would be uploaded here');
        
        // Reset form and button
        this.reset();
        spinner.classList.add('d-none');
        buttonText.textContent = 'Upload Photos';
        submitButton.disabled = false;
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
        modal.hide();
        
        // Show success message
        alert('Your photos have been uploaded successfully and will be reviewed by our team.');
    }, 2000);
});
</script>
