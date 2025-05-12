<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$service = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch service: " . $e->getMessage();
}

if (!$service) {
    $error = "Service not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    $icon = $service['icon'];
    if ($_FILES['icon']['name']) {
        $target_dir = "../../assets/uploads/";
        $icon = time() . '-' . basename($_FILES['icon']['name']);
        if (!move_uploaded_file($_FILES['icon']['tmp_name'], $target_dir . $icon)) {
            $error = "Failed to upload icon.";
        }
    }

    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, is_visible = ? WHERE id = ?");
            $stmt->execute([$title, $description, $icon, $is_visible, $id]);
            $success = "Service updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update service: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - BePros Admin</title>
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
        <h2>Edit Service</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($service): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="icon" class="form-label">Icon (Leave blank to keep current)</label>
                    <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                    <?php if ($service['icon']): ?>
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($service['icon']); ?>" width="100" class="mt-2" alt="Current Icon">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="is_visible" class="form-label">Visible</label>
                    <input type="checkbox" id="is_visible" name="is_visible" <?php echo $service['is_visible'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Update Service</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>