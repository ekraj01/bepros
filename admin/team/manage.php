<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = $error = '';

try {
    $stmt = $pdo->query("SELECT * FROM team_members ORDER BY created_at");
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch team members: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $role = filter_var(trim($_POST['role']), FILTER_SANITIZE_STRING);

    if ($name && $role) {
        $image = '';
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
            $stmt = $pdo->prepare("INSERT INTO team_members (name, role, image) VALUES (?, ?, ?)");
            $stmt->execute([$name, $role, $image]);
            $success = "Team member added successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add team member: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Team member deleted successfully!";
        header("Location: manage.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to delete team member: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - BePros Admin</title>
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
                    <li class="nav-item"><a class="nav-link" href="../about/manage.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage.php">Team</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Manage Team Members</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <h3>Add New Team Member</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" id="role" name="role" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_member" class="btn btn-primary">Add Team Member</button>
        </form>

        <h3 class="mt-5">Team Members</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($team_members)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No team members found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($team_members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo htmlspecialchars($member['role']); ?></td>
                            <td><?php echo htmlspecialchars($member['image'] ?: 'No Image'); ?></td>
                            <td>
                                <a href="manage.php?delete=<?php echo $member['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this team member?')">Delete</a>
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