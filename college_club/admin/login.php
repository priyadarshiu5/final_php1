<?php
session_start();

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit();
}

$page_title = 'Admin Login';
require_once '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/config.php';
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Prepare a select statement
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);  // ✅ Fixed: Use $email, not $username

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($id, $name, $email_db, $hashed_password, $role);

                    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
                        // Login successful
                        $_SESSION['user_id'] = $id;
                        $_SESSION['name'] = $name;
                        $_SESSION['user_role'] = $role;

                        header('Location: dashboard.php');
                        exit();
                    } else {
                        $error = 'Incorrect password.';
                    }
                } else {
                    $error = 'No admin account found with that email.';
                }
            } else {
                $error = 'Something went wrong. Please try again later.';
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Admin Login</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="../index.php">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
