<?php
try {
    require_once 'config/db.php';
} catch (Exception $e) {
    die("Failed to load database configuration: " . $e->getMessage());
}

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if (!$name) {
        $error = "Please enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!$message) {
        $error = "Please enter a message.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $message]);
            $success = "Your message has been sent successfully!";
            // Clear form fields
            $name = $email = $message = '';
        } catch (PDOException $e) {
            $error = "Failed to send message: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact BePros Nepal for software development, digital marketing, and Homescape solutions.">
    <meta name="keywords" content="BePros Nepal, contact, software development, digital marketing, Homescape solutions">
    <meta name="author" content="BePros Nepal">
    <title>BePros Nepal - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        main {
            flex: 1 0 auto;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .contact-section {
            padding: 60px 0;
        }
        .contact-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 2rem;
            position: relative;
        }
        .contact-section h2::after {
            content: '';
            width: 50px;
            height: 4px;
            background: #007bff;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .contact-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        footer {
            flex-shrink: 0;
            background: #1a1a1a;
            color: #fff;
            padding: 20px 0;
        }
        footer a {
            color: #fff;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: #007bff;
        }
        @media (max-width: 768px) {
            .contact-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="assets/images/logo.png" alt="BePros Nepal Logo" style="max-height: 40px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <main>
        <section class="contact-section">
            <div class="container">
                <h2 class="text-center mb-4">Contact Us</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h3>Get in Touch</h3>
                        <form method="POST" id="contactForm" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required aria-describedby="nameError">
                                <div id="nameError" class="invalid-feedback">Please enter a valid name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required aria-describedby="emailError">
                                <div id="emailError" class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required aria-describedby="messageError"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                <div id="messageError" class="invalid-feedback">Please enter a message.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h3>Contact Information</h3>
                        <div class="contact-info">
                            <p><strong>Email:</strong> <a href="mailto:bepros.nepal@gmail.com">bepros.nepal@gmail.com</a></p>
                            <p><strong>WhatsApp:</strong> <a href="https://wa.me/9779818685250" target="_blank">+977-9818685250</a></p>
                            <p><strong>Phone:</strong> <a href="tel:+9779746562144">+977-9746562144</a></p>
                            <p><strong>Address:</strong> Newroad, Kathmandu, Nepal</p>
                            <p>Reach out for inquiries about our services, including software development, digital marketing, or BePros Homescape solutions!</p>
                            <div class="mt-3">
                                <a href="https://www.facebook.com/share/1F693fG8Yd/?mibextid=wwXIfr" class="me-3" aria-label="BePros Nepal Facebook Page" target="_blank"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="me-3" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="me-3" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>© 2025 BePros Nepal. All Rights Reserved.</p>
            <div class="mt-2">
                <a href="https://www.facebook.com/share/1F693fG8Yd/?mibextid=wwXIfr" class="me-3" aria-label="BePros Nepal Facebook Page" target="_blank"><i class="bi bi-facebook"></i></a>
                <a href="https://g.page/r/CdCmikQcB-ZtEAI/review" class="me-3" aria-label="BePros Nepal Google Business Profile" target="_blank"><i class="bi bi-google"></i></a>
                
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        // Client-side form validation
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('contactForm');
            form.addEventListener('submit', (e) => {
                let isValid = true;
                const name = form.querySelector('#name');
                const email = form.querySelector('#email');
                const message = form.querySelector('#message');

                // Reset invalid feedback
                [name, email, message].forEach(input => {
                    input.classList.remove('is-invalid');
                    input.nextElementSibling.style.display = 'none';
                });

                // Validate name
                if (!name.value.trim()) {
                    name.classList.add('is-invalid');
                    name.nextElementSibling.style.display = 'block';
                    isValid = false;
                }

                // Validate email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    email.classList.add('is-invalid');
                    email.nextElementSibling.style.display = 'block';
                    isValid = false;
                }

                // Validate message
                if (!message.value.trim()) {
                    message.classList.add('is-invalid');
                    message.nextElementSibling.style.display = 'block';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>