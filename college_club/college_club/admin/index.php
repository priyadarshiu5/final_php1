<?php
session_start();

// Check if user is already logged in as admin
if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
