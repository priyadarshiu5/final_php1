<?php
session_start();
$page_title = "Join Us";
require_once 'includes/header.php';
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}

$errors = [];
$success = false;

// Form fields with default values
$form_data = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'university' => '',
    'major' => '',
    'graduation_year' => '',
    'interests' => []
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $form_data['name'] = trim($_POST['name'] ?? '');
    $form_data['email'] = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $form_data['phone'] = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $form_data['university'] = trim($_POST['university'] ?? '');
    $form_data['major'] = trim($_POST['major'] ?? '');
    $form_data['graduation_year'] = $_POST['graduation_year'] ?? '';
    $form_data['interests'] = $_POST['interests'] ?? [];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Validation
    if (empty($form_data['name'])) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($form_data['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $form_data['email']);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['email'] = 'This email is already registered';
        }
        $stmt->close();
    }
    
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (empty($form_data['university'])) {
        $errors['university'] = 'University name is required';
    }
    
    if (empty($form_data['major'])) {
        $errors['major'] = 'Major/Field of study is required';
    }
    
    if (empty($form_data['graduation_year'])) {
        $errors['graduation_year'] = 'Expected graduation year is required';
    }
    
    if (empty($form_data['interests'])) {
        $errors['interests'] = 'Please select at least one area of interest';
    }
    
    if (!$terms) {
        $errors['terms'] = 'You must accept the terms and conditions';
    }
    
    // If no errors, process registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate verification token
        $verification_token = bin2hex(random_bytes(32));
        
        // Insert user
        $sql = "INSERT INTO users (name, email, password, phone, university, major, graduation_year, verification_token, status, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'member')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssss",
            $form_data['name'],
            $form_data['email'],
            $hashed_password,
            $form_data['phone'],
            $form_data['university'],
            $form_data['major'],
            $form_data['graduation_year'],
            $verification_token
        );
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Insert interests
            if (!empty($form_data['interests'])) {
                $sql = "INSERT INTO user_interests (user_id, interest) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                
                foreach ($form_data['interests'] as $interest) {
                    $interest = trim($interest);
                    if (!empty($interest)) {
                        $stmt->bind_param("is", $user_id, $interest);
                        $stmt->execute();
                    }
                }
            }
            
            // Send verification email (in a real application)
            $verification_link = "http://" . $_SERVER['HTTP_HOST'] . "/college_club/verify.php?token=" . $verification_token;
            $to = $form_data['email'];
            $subject = "Verify Your Email Address";
            $message = "Hello " . htmlspecialchars($form_data['name']) . ",\n\n";
            $message .= "Thank you for registering with " . SITE_NAME . ". Please click the link below to verify your email address:\n\n";
            $message .= $verification_link . "\n\n";
            $message .= "Best regards,\n" . SITE_NAME . " Team";
            
            $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "Reply-To: contact@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // In a production environment, you would use a proper mailer library
            // mail($to, $subject, $message, $headers);
            
            // Redirect to success page
            $_SESSION['registration_success'] = true;
            $_SESSION['registered_email'] = $form_data['email'];
            header('Location: registration-success.php');
            exit();
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4">Join Our Community</h1>
                <p class="lead">Become a member and unlock exclusive benefits</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Please fix the following errors:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form id="registrationForm" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" name="name" 
                                           value="<?php echo htmlspecialchars($form_data['name']); ?>" required>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['name'] ?? 'Please enter your name'; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                           id="email" name="email" 
                                           value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email'] ?? 'Please enter a valid email address'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                               id="password" name="password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['password'] ?? 'Please enter a valid password'; ?>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        Must be at least 8 characters long
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                           id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['confirm_password'] ?? 'Passwords do not match'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="university" class="form-label">University/Institution <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['university']) ? 'is-invalid' : ''; ?>" 
                                           id="university" name="university" 
                                           value="<?php echo htmlspecialchars($form_data['university']); ?>" required>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['university'] ?? 'Please enter your university/institution'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="major" class="form-label">Major/Field of Study <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['major']) ? 'is-invalid' : ''; ?>" 
                                           id="major" name="major" 
                                           value="<?php echo htmlspecialchars($form_data['major']); ?>" required>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['major'] ?? 'Please enter your major/field of study'; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="graduation_year" class="form-label">Expected Graduation Year <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($errors['graduation_year']) ? 'is-invalid' : ''; ?>" 
                                            id="graduation_year" name="graduation_year" required>
                                        <option value="" disabled selected>Select Year</option>
                                        <?php 
                                        $current_year = date('Y');
                                        for ($year = $current_year; $year <= $current_year + 5; $year++): 
                                        ?>
                                            <option value="<?php echo $year; ?>" 
                                                <?php echo $form_data['graduation_year'] == $year ? 'selected' : ''; ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['graduation_year'] ?? 'Please select your expected graduation year'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Areas of Interest <span class="text-danger">*</span></label>
                                <div class="row">
                                    <?php 
                                    $interest_options = [
                                        'Web Development', 'Mobile App Development', 'Data Science', 
                                        'Artificial Intelligence', 'Machine Learning', 'Cybersecurity',
                                        'Cloud Computing', 'Game Development', 'UI/UX Design'
                                    ];
                                    
                                    foreach ($interest_options as $interest): 
                                        $is_checked = in_array($interest, $form_data['interests']) ? 'checked' : '';
                                    ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="interests[]" value="<?php echo htmlspecialchars($interest); ?>"
                                                       id="interest_<?php echo preg_replace('/[^a-z0-9]/', '_', strtolower($interest)); ?>"
                                                       <?php echo $is_checked; ?>>
                                                <label class="form-check-label" for="interest_<?php echo preg_replace('/[^a-z0-9]/', '_', strtolower($interest)); ?>">
                                                    <?php echo htmlspecialchars($interest); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="invalid-feedback" style="display: block;">
                                    <?php echo $errors['interests'] ?? 'Please select at least one area of interest'; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>" 
                                           type="checkbox" id="terms" name="terms" 
                                           <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and 
                                        <a href="privacy.php" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['terms'] ?? 'You must agree to the terms and conditions'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">Create Account</span>
                                </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <p class="mb-0">Already have an account? 
                                    <a href="login.php" class="text-decoration-none">Sign in</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h5>Networking</h5>
                    <p class="mb-0">Connect with like-minded students and professionals in your field.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="fas fa-laptop-code fa-2x"></i>
                    </div>
                    <h5>Learning</h5>
                    <p class="mb-0">Access workshops and resources to enhance your skills.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h5>Career</h5>
                    <p class="mb-0">Get access to job postings and career development resources.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Form validation and submission
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
                buttonText.textContent = 'Creating Account...';
                spinner.classList.remove('d-none');
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})();

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const passwordInput = this.closest('.input-group').querySelector('input');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

// Password strength indicator
const passwordInput = document.getElementById('password');
if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthMeter = document.getElementById('password-strength');
        
        if (password.length === 0) {
            if (strengthMeter) strengthMeter.style.width = '0%';
            return;
        }
        
        // Calculate password strength (simple version)
        let strength = 0;
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/)) strength += 25;
        
        // Update strength meter
        if (strengthMeter) {
            strengthMeter.style.width = strength + '%';
            
            // Update color based on strength
            if (strength < 50) {
                strengthMeter.classList.remove('bg-warning', 'bg-success');
                strengthMeter.classList.add('bg-danger');
            } else if (strength < 75) {
                strengthMeter.classList.remove('bg-danger', 'bg-success');
                strengthMeter.classList.add('bg-warning');
            } else {
                strengthMeter.classList.remove('bg-danger', 'bg-warning');
                strengthMeter.classList.add('bg-success');
            }
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
