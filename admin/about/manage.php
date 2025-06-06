<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch current about section data
try {
    $stmt = $pdo->query("SELECT * FROM about_section LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$about) {
        $stmt = $pdo->prepare("INSERT INTO about_section (title, content, image) VALUES (?, ?, ?)");
        $stmt->execute(['About BePros Nepal', 'BePros Nepal is a leading provider of innovative business solutions...', 'team.jpg']);
        $about = ['id' => 1, 'title' => 'About BePros Nepal', 'content' => 'BePros Nepal is a leading provider of innovative business solutions...', 'image' => 'team.jpg'];
    }
} catch (PDOException $e) {
    $error = "Failed to fetch about section: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
    $image = $about['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/uploads/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_name;
        } else {
            $error = "Failed to upload image.";
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE about_section SET title = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$title, $content, $image, $about['id']]);
        $success = "About section updated successfully!";
        $about = ['id' => $about['id'], 'title' => $title, 'content' => $content, 'image' => $image];
    } catch (PDOException $e) {
        $error = "Failed to update about section: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About - BePros Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">BePros Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../services/manage.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="../projects/manage.php">Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="../blog/manage.php">Blogs</a></li>
                    <li class="nav-item"><a class="nav-link" href="../testimonials/manage.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="../messages/manage.php">Messages</a></li>
                    <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="../admins/manage.php">Admins</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Manage About Section</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($about['content']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image (current: <?php echo htmlspecialchars($about['image']); ?>)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Update About Section</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>