<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Handle mark as read/unread
if (isset($_GET['mark_read'])) {
    $id = filter_var($_GET['mark_read'], FILTER_SANITIZE_NUMBER_INT);
    $is_read = isset($_GET['status']) && $_GET['status'] == '1' ? 1 : 0;
    try {
        $stmt = $pdo->prepare("UPDATE contacts SET is_read = ? WHERE id = ?");
        $stmt->execute([$is_read, $id]);
        $success = "Message marked as " . ($is_read ? "read" : "unread") . "!";
    } catch (PDOException $e) {
        $error = "Failed to update message: " . $e->getMessage();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Message deleted successfully!";
    } catch (PDOException $e) {
        $error = "Failed to delete message: " . $e->getMessage();
    }
}

// Fetch messages (with search)
$search = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : '';
try {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    }
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch messages: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - BePros Admin</title>
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
        <h2>Manage Messages</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Message List -->
        <h3>Message List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Read</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr><td colspan="6" class="text-center">No messages found.</td></tr>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo substr(htmlspecialchars($message['message']), 0, 100); ?>...</td>
                            <td><?php echo date('Y-m-d H:i', strtotime($message['created_at'])); ?></td>
                            <td><?php echo $message['is_read'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="?mark_read=<?php echo $message['id']; ?>&status=<?php echo $message['is_read'] ? 0 : 1; ?>" class="btn btn-<?php echo $message['is_read'] ? 'warning' : 'success'; ?> btn-sm">
                                    Mark as <?php echo $message['is_read'] ? 'Unread' : 'Read'; ?>
                                </a>
                                <a href="?delete=<?php echo $message['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>