<?php
try {
    require_once 'config/db.php';
} catch (Exception $e) {
    die("Failed to load database configuration: " . $e->getMessage());
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$project = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND is_published = 1");
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch project: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BePros Nepal - Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">BePros Nepal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link active" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Project Details Section -->
    <section class="py-5">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php elseif (!$project): ?>
                <h2 class="text-center">Project Not Found</h2>
                <p class="text-center">The project you are looking for does not exist or is not available.</p>
            <?php else: ?>
                <h2><?php echo htmlspecialchars($project['title']); ?></h2>
                <div class="row">
                    <div class="col-md-6">
                        <?php if ($project['image']): ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($project['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <?php else: ?>
                            <img src="assets/uploads/placeholder-project.jpg" class="img-fluid" alt="Project Placeholder">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Project Details</h4>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($project['description']); ?></p>
                        <p><strong>Tech Stack:</strong> <?php echo htmlspecialchars($project['tech_stack']); ?></p>
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($project['category']); ?></p>
                        <a href="portfolio.php" class="btn btn-primary">Back to Portfolio</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>© 2025 BePros Nepal. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html><?php
try {
    require_once 'config/db.php';
} catch (Exception $e) {
    die("Failed to load database configuration: " . $e->getMessage());
}

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$project = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND is_published = 1");
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch project: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BePros Nepal - Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .project-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 5rem 0;
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .img-fluid {
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .img-fluid {
                max-height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <!-- Image: logo.png (assets/images/logo.png) -->
            <a class="navbar-brand" href="index.php"><img src="assets/images/logo.png" alt="BePros Nepal Logo" style="max-height: 40px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link active" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Project Details Section -->
    <section class="project-section fade-in">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger fade-in visible"><?php echo $error; ?></div>
            <?php elseif (!$project): ?>
                <h2 class="text-center fade-in visible">Project Not Found</h2>
                <p class="text-center fade-in visible">The project you are looking for does not exist or is not available.</p>
            <?php else: ?>
                <h2 class="fade-in visible"><?php echo htmlspecialchars($project['title']); ?></h2>
                <div class="row align-items-center">
                    <div class="col-md-6 fade-in">
                        <!-- Dynamic Images: ecommerce.jpg, homescape.jpg, website.jpg, inventory.jpg, travel.jpg, crm.jpg -->
                        <!-- Placeholder: placeholder-project.jpg (assets/uploads/) -->
                        <?php if ($project['image']): ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($project['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <?php else: ?>
                            <img src="assets/uploads/placeholder-project.jpg" class="img-fluid" alt="Project Placeholder">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 fade-in">
                        <h4 class="fade-in visible">Project Details</h4>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($project['description']); ?></p>
                        <p><strong>Tech Stack:</strong> <?php echo htmlspecialchars($project['tech_stack'] ?? 'N/A'); ?></p>
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($project['client'] ?? 'N/A'); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($project['category'] ?? 'Uncategorized'); ?></p>
                        <a href="portfolio.php" class="btn btn-primary fade-in visible">Back to Portfolio</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 fade-in">
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
        // Fade-in animations on scroll
        document.addEventListener('DOMContentLoaded', () => {
            const elements = document.querySelectorAll('.fade-in');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            elements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>