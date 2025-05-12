<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && $password && in_array($role, ['admin', 'super_admin'])) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hashed_password, $role]);
            $success = "Admin added successfully!";
        } catch (PDOException $e) {
            $error = "Failed to add admin: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    if ($id != $_SESSION['admin_id']) { // Prevent self-deletion
        try {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Admin deleted successfully!";
        } catch (PDOException $e) {
            $error = "Failed to delete admin: " . $e->getMessage();
        }
    } else {
        $error = "Cannot delete your own account.";
    }
}

// Fetch admins (with search)
$search = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : '';
try {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email LIKE ? ORDER BY email");
        $stmt->execute(["%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM admins ORDER BY email");
    }
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch admins: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - BePros Admin</title>
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
        <h2>Manage Admins</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add Admin Form -->
        <form method="POST" class="mb-5">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Admin</button>
        </form>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Admin List -->
        <h3>Admin List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                    <tr><td colspan="3" class="text-center">No admins found.</td></tr>
                <?php else: ?>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['role']); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $admin['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?php echo $admin['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>