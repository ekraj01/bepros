<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$project = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch project: " . $e->getMessage();
}

if (!$project) {
    $error = "Project not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $tech_stack = filter_var($_POST['tech_stack'], FILTER_SANITIZE_STRING);
    $client = filter_var($_POST['client'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $image = $project['image'];
    if ($_FILES['image']['name']) {
        $target_dir = "../../assets/uploads/";
        $image = time() . '-' . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image)) {
            $error = "Failed to upload image.";
        }
    }

    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, image = ?, tech_stack = ?, client = ?, category = ?, is_published = ? WHERE id = ?");
            $stmt->execute([$title, $description, $image, $tech_stack, $client, $category, $is_published, $id]);
            $success = "Project updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update project: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - BePros Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">BePros Admin</a>
            <div class="ms-auto">
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Edit Project</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($project): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image (Leave blank to keep current)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <?php if ($project['image']): ?>
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($project['image']); ?>" width="100" class="mt-2" alt="Current Image">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="tech_stack" class="form-label">Tech Stack</label>
                    <input type="text" class="form-control" id="tech_stack" name="tech_stack" value="<?php echo htmlspecialchars($project['tech_stack']); ?>">
                </div>
                <div class="mb-3">
                    <label for="client" class="form-label">Client</label>
                    <input type="text" class="form-control" id="client" name="client" value="<?php echo htmlspecialchars($project['client']); ?>">
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($project['category']); ?>">
                </div>
                <div class="mb-3">
                    <label for="is_published" class="form-label">Published</label>
                    <input type="checkbox" id="is_published" name="is_published" <?php echo $project['is_published'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>