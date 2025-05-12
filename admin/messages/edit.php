<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$message = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch message: " . $e->getMessage();
}

if (!$message) {
    $error = "Message not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message_text = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    $is_read = isset($_POST['is_read']) ? 1 : 0;

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message_text) {
        try {
            $stmt = $pdo->prepare("UPDATE contacts SET name = ?, email = ?, message = ?, is_read = ? WHERE id = ?");
            $stmt->execute([$name, $email, $message_text, $is_read, $id]);
            $success = "Message updated successfully!";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update message: " . $e->getMessage();
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
    <title>Edit Message - BePros Admin</title>
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
        <h2>Edit Message</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($message['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($message['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message['message']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="is_read" class="form-label">Read</label>
                    <input type="checkbox" id="is_read" name="is_read" <?php echo $message['is_read'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Update Message</button>
                <a href="manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>