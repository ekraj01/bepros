<?php
try {
    require_once 'config/db.php';
} catch (Exception $e) {
    die("Failed to load database configuration: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT * FROM services WHERE is_visible = 1 ORDER BY title LIMIT 4");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
}

try {
    $stmt = $pdo->query("SELECT * FROM projects WHERE is_visible = 1 ORDER BY created_at DESC LIMIT 4");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
}

try {
    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3");
    $blog_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Number of blog posts fetched: " . count($blog_posts));
} catch (PDOException $e) {
    $blog_posts = [];
    error_log("Error fetching blog posts: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY created_at DESC LIMIT 3");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $testimonials = [];
}

$contact_error = '';
$contact_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    if ($name && $email && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $message]);
            $contact_success = "Thank you for your message! We'll get back to you soon.";
        } catch (PDOException $e) {
            $contact_error = "Failed to send message. Please try again later.";
        }
    } else {
        $contact_error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BePros Nepal - Empowering businesses with innovative software development, Homescape solutions, and more. Explore our services, portfolio, and testimonials.">
    <meta name="keywords" content="BePros Nepal, software development, Homescape solutions, web development, IT services, Nepal business">
    <meta name="author" content="BePros Nepal">
    <title>BePros Nepal - Innovative Business Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/hero-bg.jpg') no-repeat center/cover;
            color: white;
            min-height: 600px;
            display: flex;
            align-items: center;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: bold;
            animation: fadeInUp 1s ease-in-out;
        }
        .section-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .testimonial-carousel .carousel-item {
            padding: 2rem;
            text-align: center;
        }
        .contact-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .form-control, .btn {
            border-radius: 8px;
        }
        .btn-primary {
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .section-title {
                font-size: 2rem;
            }
            footer .col-md-3:first-child h5,
footer .col-md-3:first-child p {
    color: #fff !important;
}
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="assets/images/logo.png" alt="BePros Nepal Logo" style="max-height: 40px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1>Empower Your Business with BePros Nepal</h1>
            <p class="lead mt-3">Innovative software development, Homescape solutions, and more to elevate your success.</p>
            <div class="mt-4">
                <a href="contact.php" class="btn btn-primary btn-lg me-3">Get in Touch</a>
                <a href="services.php" class="btn btn-outline-light btn-lg">Explore Services</a>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">About BePros Nepal</h2>
            <div class="row align-items-center">
                <div class="col-md-6 fade-in">
                    <p>BePros Nepal is a leading provider of innovative business solutions, specializing in software development, web services, and Homescape solutions. Our mission is to empower businesses in Nepal and beyond with cutting-edge technology and exceptional service.</p>
                    <p>Founded in 2020, our team of experts is dedicated to delivering results that drive growth and success. <a href="about.php" class="text-primary">Learn more about our story</a>.</p>
                </div>
                <div class="col-md-6 fade-in">
                    <img src="assets\images\images.jpeg" alt="Service" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">Our Services</h2>
            <div class="row">
                <?php if (empty($services)): ?>
                    <div class="col-12">
                        <p class="text-center">No services available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <div class="col-md-3 mb-4 fade-in">
                            <div class="card h-100">
                                <?php if ($service['icon']): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($service['icon']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['title']); ?>" style="max-height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="assets/uploads/placeholder-service.jpg" class="card-img-top" alt="Service Placeholder" style="max-height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                                    <p class="card-text"><?php echo substr(htmlspecialchars($service['description']), 0, 100); ?>...</p>
                                    <a href="services.php" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4 fade-in">
                <a href="services.php" class="btn btn-outline-primary">View All Services</a>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">Our Portfolio</h2>
            <div class="row">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <p class="text-center">No projects available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-3 mb-4 fade-in">
                            <div class="card h-100">
                                <?php if ($project['image']): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($project['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>" style="max-height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="assets/uploads/placeholder-project.jpg" class="card-img-top" alt="Project Placeholder" style="max-height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text"><?php echo substr(htmlspecialchars($project['description']), 0, 100); ?>...</p>
                                    <p class="card-text"><strong>Tech Stack:</strong> <?php echo htmlspecialchars($project['tech_stack'] ?: 'N/A'); ?></p>
                                    <a href="project.php?id=<?php echo $project['id']; ?>" class="btn btn-primary">View Project</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4 fade-in">
                <a href="portfolio.php" class="btn btn-outline-primary">View All Projects</a>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">Latest Blog Posts</h2>
            <div class="row">
                <?php if (empty($blog_posts)): ?>
                    <div class="col-12">
                        <p class="text-center">No blog posts available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($blog_posts as $post): ?>
                        <div class="col-md-4 mb-4 fade-in">
                            <div class="card h-100">
                                <?php if ($post['image']): ?>
                                    <img src="assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="max-height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="assets/uploads/placeholder-blog.jpg" class="card-img-top" alt="Blog Placeholder" style="max-height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p class="card-text"><?php echo substr(htmlspecialchars($post['content']), 0, 100); ?>...</p>
                                    <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4 fade-in">
                <a href="blog.php" class="btn btn-outline-primary">Read Our Blog</a>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">What Our Clients Say</h2>
            <div id="testimonialCarousel" class="carousel slide fade-in" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php if (empty($testimonials)): ?>
                        <div class="carousel-item active">
                            <p class="text-center">No testimonials available at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($testimonials as $index => $testimonial): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="d-flex justify-content-center">
                                    <div class="card p-4" style="max-width: 600px;">
                                        <?php if ($testimonial['photo']): ?>
                                            <img src="assets/uploads/<?php echo htmlspecialchars($testimonial['photo']); ?>" class="rounded-circle mx-auto mb-3" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" style="width: 100px; height: 100px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="assets/uploads/placeholder-client.jpg" class="rounded-circle mx-auto mb-3" alt="Client Placeholder" style="width: 100px; height: 100px; object-fit: cover;">
                                        <?php endif; ?>
                                        <p class="card-text">"<?php echo htmlspecialchars(substr($testimonial['quote'], 0, 150)) . '...'; ?>"</p>
                                        <h5 class="card-title text-center"><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                                        <h6 class="text-center"><?php echo htmlspecialchars($testimonial['company'] ?: 'N/A'); ?></h6>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <div class="text-center mt-4 fade-in">
                <a href="testimonials.php" class="btn btn-outline-primary">See All Testimonials</a>
            </div>
        </div>
    </section>

    <section class="contact-section py-5">
        <div class="container">
            <h2 class="section-title text-center fade-in">Get in Touch</h2>
            <div class="row">
                <div class="col-md-6 mb-4 fade-in">
                    <h4>Contact Information</h4>
                    <p><i class="bi bi-geo-alt"></i> Kathmandu, Nepal</p>
                    <p><i class="bi bi-envelope"></i> info@bepros.com</p>
                    <p><i class="bi bi-phone"></i> +977-9818685250</p>
                </div>
                <div class="col-md-6 fade-in">
                    <?php if ($contact_error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($contact_error); ?></div>
                    <?php endif; ?>
                    <?php if ($contact_success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($contact_success); ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="contact_submit" value="1">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">BePros Nepal</h5>
<p class="text-muted">Delivering innovative solutions in software development, digital marketing, and Homescape solutions since 2023.</p>            </div>
            <!-- Quick Links -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white text-decoration-none hover-link">Home</a></li>
                    <li><a href="about.php" class="text-white text-decoration-none hover-link">About</a></li>
                    <li><a href="services.php" class="text-white text-decoration-none hover-link">Services</a></li>
                    <li><a href="portfolio.php" class="text-white text-decoration-none hover-link">Portfolio</a></li>
                    <li><a href="contact.php" class="text-white text-decoration-none hover-link">Contact</a></li>
                </ul>
            </div>
            <!-- Contact Info -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Contact Us</h5>
                <p class="text-muted">
                    <i class="bi bi-envelope me-2"></i><a href="mailto:bepros.nepal@gmail.com" class="text-white text-decoration-none hover-link">bepros.nepal@gmail.com</a><br>
                    <i class="bi bi-telephone me-2"></i><a href="tel:+9779863313703" class="text-white text-decoration-none hover-link">+977-9863313703, +977-9746562144</a><br>
                    <i class="bi bi-geo-alt me-2"></i>Newroad, Kathmandu, Nepal
                </p>
            </div>
            <!-- Social Media -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Follow Us</h5>
                <div class="d-flex">
                    <a href="https://www.facebook.com/share/1F693fG8Yd/?mibextid=wwXIfr" class="text-white me-3" aria-label="BePros Nepal Facebook Page" target="_blank"><i class="bi bi-facebook fs-4"></i></a>
                    <a href="https://g.page/r/CdCmikQcB-ZtEAI/review" class="text-white me-3" aria-label="BePros Nepal Google Business Profile" target="_blank"><i class="bi bi-google fs-4"></i></a>
                    
                </div>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p class="mb-0">© 2025 BePros Nepal. All Rights Reserved.</p>
        </div>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
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