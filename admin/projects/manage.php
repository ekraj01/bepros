<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = $error = '';

try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch projects: " . $e->getMessage();
}

// Handle add project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $tech_stack = filter_var(trim($_POST['tech_stack']), FILTER_SANITIZE_STRING);
    $image = '';
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    if ($title && $description) {
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
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, tech_stack, image, is_visible, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $description, $tech_stack, $image, $is_visible]);
            $success = "Project added successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add project: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Handle delete project
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Project deleted successfully!";
        header("Location: manage.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to delete project: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - BePros Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="manage.php">Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="../blog/manage.php">Blogs</a></li>
                    <li class="nav-item"><a class="nav-link" href="../testimonials/manage.php">Testimonials</a></li>
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
        <h2>Manage Projects</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <h3>Add New Project</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="tech_stack" class="form-label">Tech Stack</label>
                <input type="text" class="form-control" id="tech_stack" name="tech_stack">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_visible" name="is_visible" checked>
                <label class="form-check-label" for="is_visible">Visible</label>
            </div>
            <button type="submit" name="add_project" class="btn btn-primary">Add Project</button>
        </form>

        <h3 class="mt-5">Projects</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Tech Stack</th>
                    <th>Image</th>
                    <th>Visible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No projects found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                            <td><?php echo htmlspecialchars(substr($project['description'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($project['tech_stack'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($project['image'] ?: 'No Image'); ?></td>
                            <td><?php echo $project['is_visible'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="manage.php?delete=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
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