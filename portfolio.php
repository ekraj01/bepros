<?php
try {
    require_once 'config/db.php';
} catch (Exception $e) {
    die("Failed to load database configuration: " . $e->getMessage());
}

// Fetch categories for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM projects WHERE category IS NOT NULL AND is_published = 1 ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $categories = [];
}

// Pagination
$projects_per_page = 6;
$page = isset($_GET['page']) ? max(1, filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT)) : 1;
$offset = ($page - 1) * $projects_per_page;

// Fetch projects (with optional category filter)
$category = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_SANITIZE_STRING) : null;
try {
    if ($category) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE is_published = 1 AND category = ?");
        $stmt->execute([$category]);
        $total_projects = $stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE is_published = 1 AND category = ? ORDER BY title LIMIT ? OFFSET ?");
        $stmt->execute([$category, $projects_per_page, $offset]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE is_published = 1");
        $total_projects = $stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE is_published = 1 ORDER BY title LIMIT ? OFFSET ?");
        $stmt->execute([$projects_per_page, $offset]);
    }
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_pages = ceil($total_projects / $projects_per_page);
} catch (PDOException $e) {
    $projects = [];
    $total_projects = 0;
    $total_pages = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BePros Nepal Portfolio - Explore our innovative projects in web development, mobile apps, and Homescape solutions.">
    <meta name="keywords" content="BePros Nepal, portfolio, web development, mobile apps, Homescape solutions, IT projects">
    <meta name="author" content="BePros Nepal">
    <title>BePros Nepal - Portfolio</title>
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
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/hero-bg.jpg') no-repeat center/cover;
            color: white;
            min-height: 400px;
            display: flex;
            align-items: center;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            animation: fadeInUp 1s ease-in-out;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 2rem;
            position: relative;
        }
        .section-title::after {
            content: '';
            width: 50px;
            height: 4px;
            background: #007bff;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            color: #333;
        }
        .card-text {
            color: #666;
        }
        .full-description {
            display: none;
        }
        .read-more-btn {
            cursor: pointer;
        }
        .btn-filter {
            border-radius: 25px;
            margin: 5px;
            padding: 8px 20px;
            font-size: 0.9rem;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-filter.active, .btn-filter:hover {
            background-color: #007bff;
            color: white;
            transform: translateY(-2px);
        }
        .portfolio-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .btn-filter {
                padding: 6px 15px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
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
                    <li class="nav-item"><a class="nav-link active" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Showcasing Our Success</h1>
            <p class="lead mt-3">Explore BePros Nepal’s innovative projects in web development, mobile apps, and Homescape solutions.</p>
            <div class="mt-4">
                <a href="contact.php" class="btn btn-primary btn-lg me-3">Contact Us</a>
                <a href="services.php" class="btn btn-outline-light btn-lg">Explore Services</a>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="portfolio-section">
        <div class="container">
            <h2 class="section-title text-center fade-in">Our Portfolio</h2>

            <!-- Category Filter -->
            <div class="mb-4 text-center fade-in">
                <a href="portfolio.php?page=1" class="btn btn-outline-primary btn-filter <?php echo !$category ? 'active' : ''; ?>" role="button" aria-pressed="<?php echo !$category ? 'true' : 'false'; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="portfolio.php?category=<?php echo urlencode($cat); ?>&page=1" class="btn btn-outline-primary btn-filter <?php echo $category === $cat ? 'active' : ''; ?>" role="button" aria-pressed="<?php echo $category === $cat ? 'true' : 'false'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Projects Grid -->
            <div class="row">
                <?php if (empty($projects)): ?>
                    <div class="col-12 text-center fade-in">
                        <p>No projects available in this category.</p>
                        <a href="contact.php" class="btn btn-primary mt-3">Contact Us to Start a Project</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-4 mb-4 fade-in">
                            <div class="card h-100">
                                <?php if ($project['image']): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($project['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <?php else: ?>
                                    <img src="assets/uploads/placeholder-project.jpg" class="card-img-top" alt="Project Placeholder">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text short-description"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                                    <p class="card-text full-description" id="desc-<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['description']); ?></p>
                                    <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($project['category'] ?? 'Uncategorized'); ?></small></p>
                                    <button class="btn btn-primary read-more-btn" aria-expanded="false" aria-controls="desc-<?php echo $project['id']; ?>">Read More</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Portfolio pagination" class="mt-4 fade-in">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="portfolio.php?<?php echo $category ? 'category=' . urlencode($category) . '&' : ''; ?>page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="portfolio.php?<?php echo $category ? 'category=' . urlencode($category) . '&' : ''; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="portfolio.php?<?php echo $category ? 'category=' . urlencode($category) . '&' : ''; ?>page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>

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

        // Read More toggle
        document.addEventListener('DOMContentLoaded', () => {
            const readMoreButtons = document.querySelectorAll('.read-more-btn');
            readMoreButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const cardBody = button.closest('.card-body');
                    const shortDesc = cardBody.querySelector('.short-description');
                    const fullDesc = cardBody.querySelector('.full-description');
                    const isExpanded = button.getAttribute('aria-expanded') === 'true';

                    if (!isExpanded) {
                        shortDesc.style.display = 'none';
                        fullDesc.style.display = 'block';
                        button.textContent = 'Read Less';
                        button.setAttribute('aria-expanded', 'true');
                    } else {
                        shortDesc.style.display = 'block';
                        fullDesc.style.display = 'none';
                        button.textContent = 'Read More';
                        button.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });
    </script>
</body>
</html>