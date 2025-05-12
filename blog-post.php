<?php
require_once 'config/db.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND is_published = 1");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        $error = "Blog post not found.";
    }
} catch (PDOException $e) {
    $error = "Failed to fetch blog post: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BePros Nepal - Blog Post</title>
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
        .blog-post-section {
            padding: 60px 0;
        }
        .blog-post-section h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1.5rem;
        }
        .blog-post-content {
            max-width: 800px;
            margin: 0 auto;
        }
        .blog-post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
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
            .blog-post-section h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="assets/images/logo.png" alt="BePros Nepal Logo" style="max-height: 40px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="blog-post-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($post): ?>
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="blog-post-content">
                        <?php if ($post['image']): ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="mb-3">
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <p class="text-muted">Posted on: <?php echo date('F d, Y', strtotime($post['created_at'])); ?></p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Invalid blog post ID.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>

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
</body>
</html>