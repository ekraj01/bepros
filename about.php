<?php
require_once 'config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM about_section LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$about) {
        $about = ['title' => 'About BePros Nepal', 'content' => 'BePros Nepal is a leading provider of innovative business solutions...', 'image' => 'team.jpg'];
    }
} catch (PDOException $e) {
    $about = ['title' => 'About BePros Nepal', 'content' => 'BePros Nepal is a leading provider of innovative business solutions...', 'image' => 'team.jpg'];
}

try {
    $stmt = $pdo->query("SELECT * FROM team_members ORDER BY created_at");
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $team_members = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BePros Nepal - About Us</title>
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
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .about-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 60px 0;
        }
        .about-section h2, .team-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 2rem;
            position: relative;
        }
        .about-section h2::after, .team-section h2::after {
            content: '';
            width: 50px;
            height: 4px;
            background: #007bff;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .about-section h3 {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .about-section img {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .about-section img:hover {
            transform: scale(1.05);
        }
        .team-section {
            padding: 60px 0;
        }
        .team-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-5px);
        }
        .team-card img {
            height: 200px;
            object-fit: cover;
        }
        .team-card .card-body {
            background: #fff;
            padding: 1.5rem;
        }
        .team-card .card-title {
            font-size: 1.25rem;
            color: #333;
        }
        .team-card .card-text {
            color: #666;
        }
        footer {
            background: #1a1a1a;
            color: #fff;
            padding: 20px 0;
            margin-top: auto;
        }
        footer a {
            color: #fff;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: #007bff;
        }
        @media (max-width: 768px) {
            .about-section h2, .team-section h2 {
                font-size: 2rem;
            }
            .about-section h3 {
                font-size: 1.25rem;
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
                    <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="about-section">
        <div class="container">
            <h2 class="text-center"><?php echo htmlspecialchars($about['title']); ?></h2>
            <div class="row align-items-center mt-5">
                <div class="col-md-6">
                    <h3>Our Story</h3>
                    <p><?php echo htmlspecialchars($about['content']); ?></p>
                </div>
                <div class="col-md-6">
                    <img src="assets/uploads/<?php echo htmlspecialchars($about['image']); ?>" class="img-fluid" alt="BePros Nepal Team">
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-6">
                    <h3>Our Mission</h3>
                    <p>To deliver cutting-edge services that drive growth and success for our clients, while fostering creativity and innovation.</p>
                </div>
                <div class="col-md-6">
                    <h3>Our Vision</h3>
                    <p>To be Nepal's leading multi-service provider, recognized globally for quality and reliability.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h2 class="text-center">Meet Our Team</h2>
            <div class="row">
                <?php if (empty($team_members)): ?>
                    <div class="col-12">
                        <p class="text-center">No team members available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($team_members as $member): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card team-card">
                                <img src="assets/uploads/<?php echo htmlspecialchars($member['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($member['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($member['role']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

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