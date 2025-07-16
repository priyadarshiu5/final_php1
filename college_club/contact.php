<?php
$page_title = "Contact Us";
require_once 'includes/header.php';
require_once 'includes/config.php';

// Handle form submission
$success_message = $error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (empty($errors)) {
        // In a real application, you would:
        // 1. Save to database
        // 2. Send email notification
        // 3. Handle any errors that might occur
        
        // For this example, we'll just show a success message
        $success_message = 'Thank you for your message! We will get back to you soon.';
        
        // Clear form fields
        $_POST = [];
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<!-- Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4">Contact Us</h1>
                <p class="lead">We'd love to hear from you</p>
                <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/college_club">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contact</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h5>Our Location</h5>
                        <p class="mb-0">123 College Avenue<br>Tech City, TC 12345<br>United States</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h5>Email Us</h5>
                        <p class="mb-1">General Inquiries:</p>
                        <p class="mb-1"><a href="mailto:info@collegeclub.edu">info@collegeclub.edu</a></p>
                        <p class="mb-0 mt-3">Support:</p>
                        <p class="mb-0"><a href="mailto:support@collegeclub.edu">support@collegeclub.edu</a></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-phone-alt fa-2x"></i>
                        </div>
                        <h5>Call Us</h5>
                        <p class="mb-1">Phone: <a href="tel:+11234567890">9685364856</a></p>
                        <p class="mb-1">Fax: +1 (123) 456-7891</p>
                        <p class="mb-0 mt-3">Hours: Mon-Fri, 9:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Send Us a Message</h2>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form id="contactForm" method="POST" action="contact.php" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your name.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a subject.
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label">Your Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">
                                    Please enter your message.
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">Send Message</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="ratio ratio-21x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDQyJzQ3LjMiTiA3NMKwMjAnMzIuMyJX!5e0!3m2!1sen!2sus!4v1529288032799" 
                                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="lead">Find answers to common questions about our club</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How can I join the club?
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Joining our club is easy! Simply visit our <a href="join.php">Join Us</a> page and fill out the membership form. 
                                Once submitted, our team will review your application and get back to you within 2-3 business days with 
                                further instructions.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Are there any membership fees?
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Basic membership is completely free for all students. However, some special events or workshops may have 
                                a small fee to cover materials and resources. Any fees will be clearly communicated in advance, and we 
                                always strive to keep costs as low as possible.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                When and where do you meet?
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We typically meet every Tuesday and Thursday from 6:00 PM to 8:00 PM in the Student Union Building, Room 203. 
                                However, during special events or holidays, the schedule may change. We recommend checking our 
                                <a href="events.php">events calendar</a> for the most up-to-date information.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm">
                        <h3 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How can I get involved in organizing events?
                            </button>
                        </h3>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We're always looking for enthusiastic members to help organize events! You can join one of our 
                                committees (Events, Marketing, Outreach, etc.) by indicating your interest in the membership form 
                                or by reaching out to our leadership team at <a href="mailto:leadership@collegeclub.edu">leadership@collegeclub.edu</a>.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="mb-3">Still have questions? We're here to help!</p>
                    <a href="#contactForm" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Stay Connected</h2>
                <p class="lead mb-4">Follow us on social media for the latest updates and announcements.</p>
                <div class="d-flex justify-content-center gap-3 mb-4">
                    <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="#" class="btn btn-light">Join Our Community</a>
                    <a href="#" class="btn btn-outline-light">Upcoming Events</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<script>
// Form validation
(function () {
    'use strict'
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            } else {
                // Show loading state on submit
                const submitButton = form.querySelector('button[type="submit"]');
                const buttonText = submitButton.querySelector('.btn-text');
                const spinner = submitButton.querySelector('.spinner-border');
                
                submitButton.disabled = true;
                buttonText.textContent = 'Sending...';
                spinner.classList.remove('d-none');
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
