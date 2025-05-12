<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$testimonial = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch testimonial: " . $e->getMessage();
}

if (!$testimonial) {
    $error = "Testimonial not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    $quote = filter_var($_POST['quote'], FILTER_SANITIZE_STRING);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    $photo = $testimonial['photo'];
    if ($_FILES['photo']['name']) {
        $target_dir = "../../assets/uploads/";
        $photo = time() . '-' . basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo)) {
            $error = "Failed to upload photo.";
        }
    }

    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, company = ?, quote = ?, photo = ?, is_visible = ? WHERE id = ?");
            $stmt->execute([$name, $company, $quote, $photo, $is_visible, $id]);
            $success = "Testimonial updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update testimonial: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Testimonial - BePros Admin</title>
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
        <h2>Edit Testimonial</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($testimonial): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($testimonial['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="company" class="form-label">Company</label>
                    <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($testimonial['company']); ?>">
                </div>
                <div class="mb-3">
                    <label for="quote" class="form-label">Quote</label>
                    <textarea class="form-control" id="quote" name="quote" rows="5" required><?php echo htmlspecialchars($testimonial['quote']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo (Leave blank to keep current)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <?php if ($testimonial['photo']): ?>
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($testimonial['photo']); ?>" width="100" class="mt-2" alt="Current Photo">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="is_visible" class="form-label">Visible</label>
                    <input type="checkbox" id="is_visible" name="is_visible" <?php echo $testimonial['is_visible'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Update Testimonial</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>