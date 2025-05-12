<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$blog = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch blog post: " . $e->getMessage();
}

if (!$blog) {
    $error = "Blog post not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $content = $_POST['content']; // TinyMCE content, no sanitization to preserve HTML
    $tags = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);
    $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

    $featured_image = $blog['featured_image'];
    if ($_FILES['featured_image']['name']) {
        $target_dir = "../../assets/uploads/";
        $featured_image = time() . '-' . basename($_FILES['featured_image']['name']);
        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_dir . $featured_image)) {
            $error = "Failed to upload image.";
        }
    }

    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, featured_image = ?, tags = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $content, $featured_image, $tags, $status, $id]);
            $success = "Blog post updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update blog post: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post - BePros Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code'
        });
    </script>
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
        <h2>Edit Blog Post</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($blog): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="featured_image" class="form-label">Featured Image (Leave blank to keep current)</label>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                    <?php if ($blog['featured_image']): ?>
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($blog['featured_image']); ?>" width="100" class="mt-2" alt="Current Image">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="tags" class="form-label">Tags (comma-separated)</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars($blog['tags']); ?>">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="draft" <?php echo $blog['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $blog['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Blog Post</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>