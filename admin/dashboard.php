<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $services_count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $projects_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $blogs_count = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    $testimonials_count = $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    $messages_count = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
    $admins_count = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    $about_count = $pdo->query("SELECT COUNT(*) FROM about_section")->fetchColumn();
    $team_count = $pdo->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
} catch (PDOException $e) {
    $error = "Failed to fetch data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BePros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">BePros Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="services/manage.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="projects/manage.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog/manage.php">Blogs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="testimonials/manage.php">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages/manage.php">Messages</a>
                    </li>
                    <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admins/manage.php">Admins</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="about/manage.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team/manage.php">Team</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Services</h5>
                        <p class="card-text"><?php echo $services_count; ?> Total</p>
                        <a href="services/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Projects</h5>
                        <p class="card-text"><?php echo $projects_count; ?> Total</p>
                        <a href="projects/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Blogs</h5>
                        <p class="card-text"><?php echo $blogs_count; ?> Total</p>
                        <a href="blog/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Testimonials</h5>
                        <p class="card-text"><?php echo $testimonials_count; ?> Total</p>
                        <a href="testimonials/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Messages</h5>
                        <p class="card-text"><?php echo $messages_count; ?> Unread</p>
                        <a href="messages/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Admins</h5>
                            <p class="card-text"><?php echo $admins_count; ?> Total</p>
                            <a href="admins/manage.php" class="btn btn-primary">Manage</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">About</h5>
                        <p class="card-text"><?php echo $about_count; ?> Total</p>
                        <a href="about/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Team</h5>
                        <p class="card-text"><?php echo $team_count; ?> Total</p>
                        <a href="team/manage.php" class="btn btn-primary">Manage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>