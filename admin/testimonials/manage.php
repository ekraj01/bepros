<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = $error = '';

try {
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch testimonials: " . $e->getMessage();
}

// Handle add testimonial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $company = filter_var(trim($_POST['company']), FILTER_SANITIZE_STRING);
    $quote = filter_var(trim($_POST['quote']), FILTER_SANITIZE_STRING);
    $photo = '';
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    if ($name && $company && $quote) {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../assets/uploads/';
            $photo_name = basename($_FILES['photo']['name']);
            $photo_path = $upload_dir . $photo_name;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                $photo = $photo_name;
            } else {
                $error = "Failed to upload photo.";
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, company, quote, photo, is_visible, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $company, $quote, $photo, $is_visible]);
            $success = "Testimonial added successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add testimonial: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Handle delete testimonial
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Testimonial deleted successfully!";
        header("Location: manage.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to delete testimonial: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - BePros Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">BePros Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../services/manage.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="../projects/manage.php">Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="../blog/manage.php">Blogs</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="../messages/manage.php">Messages</a></li>
                    <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="../admins/manage.php">Admins</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="../about/manage.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="../team/manage.php">Team</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Manage Testimonials</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <h3>Add New Testimonial</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="company" class="form-label">Company</label>
                <input type="text" class="form-control" id="company" name="company" required>
            </div>
            <div class="mb-3">
                <label for="quote" class="form-label">Quote</label>
                <textarea class="form-control" id="quote" name="quote" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" checked>
                <label class="form-check-label" for="is_visible">Visible</label>
            </div>
            <button type="submit" name="add_testimonial" class="btn btn-primary">Add Testimonial</button>
        </form>

        <h3 class="mt-5">Testimonials</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Quote</th>
                    <th>Photo</th>
                    <th>Visible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($testimonials)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No testimonials found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($testimonial['name']); ?></td>
                            <td><?php echo htmlspecialchars($testimonial['company']); ?></td>
                            <td><?php echo htmlspecialchars(substr($testimonial['quote'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($testimonial['photo'] ?: 'No Photo'); ?></td>
                            <td><?php echo $testimonial['is_visible'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $testimonial['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="manage.php?delete=<?php echo $testimonial['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this testimonial?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>