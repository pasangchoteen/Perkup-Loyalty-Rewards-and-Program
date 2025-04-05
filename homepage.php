<?php
// Optionally, you can include database connection if required
// require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PerkUp is a customizable loyalty and rewards platform for local businesses. Build customer relationships and drive growth.">
    <title>PerkUp - Loyalty & Rewards Platform</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Header Section -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <h1>
                    <a href="homepage.php" class="text-white text-decoration-none d-flex align-items-center">
                        <img src="logo.jpg" alt="Logo" class="brand-logo me-2">
                        PerkUp
                    </a>
                </h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="#features" class="nav-link text-white">Features</a></li>
                    <li class="nav-item"><a href="#how-it-works" class="nav-link text-white">How It Works</a></li>
                    <li class="nav-item"><a href="signup.php?type=business" class="nav-link text-white">Sign Up</a></li>
                    <li class="nav-item"><a href="signup.php?type=user" class="nav-link text-white">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="hero py-5 bg-light text-center">
        <div class="container">
            <h2 class="display-4">Empowering Local Businesses to Build Lasting Customer Relationships</h2>
            <p class="lead">A customizable loyalty and rewards platform that drives engagement, retention, and growth.</p>
            <div class="cta-buttons mt-4">
                <a href="signup.php?type=business" class="btn btn-primary btn-lg me-3">Get Started (Business)</a>
                <a href="signup.php?type=user" class="btn btn-outline-primary btn-lg">Join Now (Customer)</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center mb-4">Features</h2>
            <div class="row">
                <div class="col-md-4 text-center">
                    <i class="bi bi-person-circle display-4 mb-3"></i>
                    <h4>Customer Engagement</h4>
                    <p>Reward your customers for their loyalty with personalized offers and discounts.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-wallet display-4 mb-3"></i>
                    <h4>Earn & Redeem Points</h4>
                    <p>Customers can easily collect and redeem points, making them more likely to return.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-bar-chart display-4 mb-3"></i>
                    <h4>Business Insights</h4>
                    <p>Get valuable insights into your customers' behavior and grow your business.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">How It Works</h2>
            <div class="row">
                <div class="col-md-4">
                    <h5>1. Sign Up</h5>
                    <p>Create your account and choose between customer or business registration.</p>
                </div>
                <div class="col-md-4">
                    <h5>2. Set Up Rewards</h5>
                    <p>Customize your rewards program to suit your business needs and goals.</p>
                </div>
                <div class="col-md-4">
                    <h5>3. Start Earning</h5>
                    <p>Your customers start earning points with every purchase, making them more loyal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between">
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="#privacy" class="nav-link text-white">Privacy Policy</a></li>
                    <li class="nav-item"><a href="#terms" class="nav-link text-white">Terms of Service</a></li>
                </ul>
            </nav>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
