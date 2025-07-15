<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit();
}

$page_title = 'Admin Login';
require_once '../includes/header.php';

// Check for login errors
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/config.php';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Prepare a select statement
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $username);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {                    
                    // Bind result variables
                    $stmt->bind_result($id, $name, $email, $hashed_password, $role);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION['user_id'] = $id;
                            $_SESSION['name'] = $name;                            
                            $_SESSION['user_role'] = $role;
                            
                            // Redirect user to dashboard page
                            header("location: dashboard.php");
                            exit();
                        } else {
                            // Display an error message if password is not valid
                            $error = 'The password you entered was not valid.';
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $error = 'No account found with that username or you do not have admin privileges.';
                }
            } else {
                $error = 'Oops! Something went wrong. Please try again later.';
            }

            // Close statement
            $stmt->close();
        }
        
        // Close connection
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
