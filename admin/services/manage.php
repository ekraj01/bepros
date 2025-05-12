<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    $icon = '';
    if ($_FILES['icon']['name']) {
        $target_dir = "../../assets/uploads/";
        $icon = time() . '-' . basename($_FILES['icon']['name']);
        if (!move_uploaded_file($_FILES['icon']['tmp_name'], $target_dir . $icon)) {
            $error = "Failed to upload icon.";
        }
    }

    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, is_visible) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $icon, $is_visible]);
            $success = "Service added successfully!";
        } catch (PDOException $e) {
            $error = "Failed to add service: " . $e->getMessage();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Service deleted successfully!";
    } catch (PDOException $e) {
        $error = "Failed to delete service: " . $e->getMessage();
    }
}

// Fetch services (with search)
$search = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : '';
try {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE title LIKE ? ORDER BY title");
        $stmt->execute(["%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM services ORDER BY title");
    }
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch services: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - BePros Admin</title>
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
        <h2>Manage Services</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add Service Form -->
        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="icon" class="form-label">Icon</label>
                <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="is_visible" class="form-label">Visible</label>
                <input type="checkbox" id="is_visible" name="is_visible" checked>
            </div>
            <button type="submit" class="btn btn-primary">Add Service</button>
        </form>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by title" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Service List -->
        <h3>Service List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Icon</th>
                    <th>Visible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                    <tr><td colspan="4" class="text-center">No services found.</td></tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['title']); ?></td>
                            <td>
                                <?php if ($service['icon']): ?>
                                    <img src="../../assets/uploads/<?php echo htmlspecialchars($service['icon']); ?>" width="50" alt="Service Icon">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $service['is_visible'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $service['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>