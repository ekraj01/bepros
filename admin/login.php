<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Generate CAPTCHA code
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(strtolower(trim($_POST['email'])), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $captchaInput = trim($_POST['captcha']);

    if (strtoupper($captchaInput) !== strtoupper($_SESSION['captcha_code'])) {
        $error = "CAPTCHA verification failed. Please try again.";
        $_SESSION['captcha_code'] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    } elseif ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, email, password, role FROM admins WHERE LOWER(email) = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
                unset($_SESSION['captcha_code']);
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password.";
                $_SESSION['captcha_code'] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
            }
        } catch (PDOException $e) {
            $error = "Database error occurred. Please try again later.";
            $_SESSION['captcha_code'] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        }
    } else {
        $error = "Please fill in all fields.";
        $_SESSION['captcha_code'] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BePros Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        .login-card img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 12px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .input-group-text {
            cursor: pointer;
            border-radius: 8px;
        }
        .alert {
            border-radius: 8px;
        }
        .captcha-code {
            font-family: monospace;
            font-size: 1.2rem;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 5px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            .login-card {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card p-4">
        <div class="text-center">
            <img src="../assets/images/logo.png" alt="BePros Nepal Logo" class="img-fluid">
            <h3 class="mb-4">BePros Admin Login</h3>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($debug)): ?>
            <div class="alert alert-warning">
                <strong>Debug Info (remove after troubleshooting):</strong>
                <ul>
                    <?php foreach ($debug as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="captcha" class="form-label">CAPTCHA Verification</label>
                <div class="captcha-code mb-2"><?php echo htmlspecialchars($_SESSION['captcha_code']); ?></div>
                <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter the code above" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>