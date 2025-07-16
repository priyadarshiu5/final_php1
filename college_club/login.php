<?php
session_start();
$page_title = "Login";
require_once 'includes/header.php';
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Prepare SQL to prevent SQL injection
        $sql = "SELECT id, name, email, password, role, status FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Check if user exists
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $name, $email, $hashed_password, $role, $status);
                    if ($stmt->fetch()) {
                        // Verify password
                        if (password_verify($password, $hashed_password)) {
                            // Check if account is active
                            if ($status != 'active') {
                                $error = 'Your account is not active. Please contact support.';
                            } else {
                                // Password is correct, start a new session
                                session_regenerate_id();
                                
                                // Store data in session variables
                                $_SESSION['user_id'] = $id;
                                $_SESSION['user_name'] = $name;
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_role'] = $role;
                                
                                // Remember me functionality
                                if ($remember) {
                                    $token = bin2hex(random_bytes(32));
                                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                                    
                                    // Store token in database
                                    $sql = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
                                    if ($stmt = $conn->prepare($sql)) {
                                        $stmt->bind_param("iss", $id, $token, $expires);
                                        $stmt->execute();
                                        
                                        // Set cookie
                                        setcookie('remember_token', $token, [
                                            'expires' => strtotime('+30 days'),
                                            'path' => '/',
                                            'domain' => '',
                                            'secure' => isset($_SERVER['HTTPS']),
                                            'httponly' => true,
                                            'samesite' => 'Lax'
                                        ]);
                                    }
                                }
                                
                                // Redirect based on user role
                                if ($role == 'admin') {
                                    header('Location: admin/dashboard.php');
                                } else {
                                    // Check for redirect URL
                                    $redirect = $_SESSION['redirect_url'] ?? 'index.php';
                                    unset($_SESSION['redirect_url']);
                                    header("Location: $redirect");
                                }
                                exit();
                            }
                        } else {
                            $error = 'Invalid email or password.';
                        }
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Oops! Something went wrong. Please try again later.';
            }
            $stmt->close();
        } else {
            $error = 'Database connection error. Please try again later.';
        }
    }
    $conn->close();
}
?>

<!-- Login Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Sign in to access your account</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['registered'])): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i> Registration successful! Please log in.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['logout'])): ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i> You have been successfully logged out.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['session_expired'])): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> Your session has expired. Please log in again.
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           placeholder="Enter your email" required>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <a href="forgot-password.php" class="small text-decoration-none">Forgot password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Enter your password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">Sign In</span>
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account? 
                                <a href="join.php" class="text-primary text-decoration-none fw-semibold">Sign up</a>
                            </p>
                        </div>
                        
                        <div class="position-relative my-4">
                            <hr>
                            <div class="divider-text">or continue with</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fab fa-google me-2"></i> Sign in with Google
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f me-2"></i> Sign in with Facebook
                            </a>
                            <a href="#" class="btn btn-dark">
                                <i class="fab fa-github me-2"></i> Sign in with GitHub
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="small text-muted">By signing in, you agree to our 
                        <a href="terms.php" class="text-decoration-none">Terms of Service</a> and 
                        <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Password Reset Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Enter your email address and we'll send you a link to reset your password.</p>
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="resetEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="resetEmail" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

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
                buttonText.textContent = 'Signing in...';
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

// Forgot password form
const forgotPasswordForm = document.getElementById('forgotPasswordForm');
if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add your password reset logic here
        const email = document.getElementById('resetEmail').value;
        
        // Simulate API call
        setTimeout(() => {
            // Show success message
            const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
            modal.hide();
            
            // Show toast notification
            const toast = new bootstrap.Toast(document.getElementById('passwordResetToast'));
            toast.show();
        }, 1000);
    });
}
</script>

<!-- Toast Notification for Password Reset -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="passwordResetToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Password Reset</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <i class="fas fa-check-circle text-success me-2"></i> Password reset link has been sent to your email.
        </div>
    </div>
</div>
