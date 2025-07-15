<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Club - <?php echo $page_title ?? 'Welcome'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/college_club/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/college_club">
                <img src="/college_club/assets/images/logo.png" alt="Logo" height="40" class="d-inline-block align-text-top me-2">
                College Club
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/college_club">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/college_club/about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/college_club/events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/college_club/blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/college_club/contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/college_club/join.php" class="btn btn-outline-light me-2">Join Us</a>
                    <a href="/college_club/admin/" class="btn btn-light">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="container my-4">
