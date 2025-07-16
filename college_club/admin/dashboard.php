<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit();
}
$page_title = 'Admin Dashboard';
require_once '../includes/header.php';
?>
<div class="container py-5">
    <h1 class="mb-4">Welcome, Admin!</h1>
    <p>This is the admin dashboard placeholder. From here, you can manage events, blog posts, members, and more.</p>
    <ul>
        <li><a href="../index.php">Go to Website Home</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>
<?php require_once '../includes/footer.php'; ?>
