<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'super_admin') {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$admin = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch admin: " . $e->getMessage();
}

if (!$admin) {
    $error = "Admin not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && in_array($role, ['admin', 'super_admin'])) {
        try {
            if ($password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET email = ?, password = ?, role = ? WHERE id = ?");
                $stmt->execute([$email, $hashed_password, $role, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET email = ?, role = ? WHERE id = ?");
                $stmt->execute([$email, $role, $id]);
            }
            $success = "Admin updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update admin: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin - BePros Admin</title>
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
        <h2>Edit Admin</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($admin): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="admin" <?php echo $admin['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="super_admin" <?php echo $admin['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Admin</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>