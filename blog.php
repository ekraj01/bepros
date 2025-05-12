<?php
require_once 'config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY created_at DESC");
    $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch blogs: " . $e->getMessage();
    $blog_posts = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BePros Nepal - Blog</title>
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
        .blog-section {
            padding: 60px 0;
        }
        .blog-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 2rem;
            position: relative;
        }
        .blog-section h2::after {
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
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
            .blog-section h2 {
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
        <section class="blog-section">
            <div class="container">
                <h2 class="text-center mb-4">Our Blog</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="row">
                    <?php if (empty($blog_posts)): ?>
                        <div class="col-12">
                            <p class="text-center">No blog posts available at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($blog_posts as $post): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <?php if ($post['image']): ?>
                                        <img src="assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    <?php else: ?>
                                        <img src="assets/uploads/placeholder-blog.jpg" class="card-img-top" alt="Blog Placeholder">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></p>
                                        <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
    <script src="assets/js/script.js"></script>
</body>
</html>