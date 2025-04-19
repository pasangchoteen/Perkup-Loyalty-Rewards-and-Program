<?php
$pageTitle = "Home";
$currentPage = "home";
$extraCSS = "css/home.css";
$extraJS = "js/home.js";
require_once 'header.php';
?>

<!-- Hero Section with Enhanced 3D Elements -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="display-4 fw-bold mb-4 glow-text">Empower Your Business with Customer Loyalty</h1>
                <p class="lead mb-4">PerkUp is a customizable loyalty and rewards platform that drives engagement, retention, and growth for local businesses.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="contact.php" class="btn btn-primary btn-lg">Get Started (Business)</a>
                    <a href="signup.php" class="btn btn-outline-primary btn-lg">Join Now (Customer)</a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="position-relative" id="hero-3d-container">
                    <!-- 3D elements will be rendered here via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Overview -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-accent">Powerful Features</h2>
            <p >Everything you need to create a successful loyalty program</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 card-3d">
                    <div class="card-3d-inner">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-map-marker-alt fa-3x text-accent"></i>
                            </div>
                            <h4 class="card-title">Location-Based Promotions</h4>
                            <p>Send personalized offers to customers when they're near your business.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 card-3d">
                    <div class="card-3d-inner">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-users fa-3x text-accent"></i>
                            </div>
                            <h4 class="card-title">Referral Program</h4>
                            <p>Encourage customers to refer friends and reward both parties for successful referrals.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 card-3d">
                    <div class="card-3d-inner">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-4">
                                <i class="fas fa-award fa-3x text-accent"></i>
                            </div>
                            <h4 class="card-title">Tiered Rewards</h4>
                            <p>Create different membership levels to unlock better rewards as customers engage more.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5 bg-gradient">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-accent">How It Works</h2>
            <p >Simple steps to get started with PerkUp</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="text-center floating-element">
                            <div class="step-circle mb-3">
                                <span>1</span>
                            </div>
                            <h4 class="text-accent">Sign Up</h4>
                            <p >Create your account in just a few minutes</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="text-center floating-element" style="animation-delay: 0.2s;">
                            <div class="step-circle mb-3">
                                <span>2</span>
                            </div>
                            <h4 class="text-accent">Set Up Rewards</h4>
                            <p >Customize your rewards program to suit your business needs</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="text-center floating-element" style="animation-delay: 0.4s;">
                            <div class="step-circle mb-3">
                                <span>3</span>
                            </div>
                            <h4 class="text-accent">Start Earning</h4>
                            <p >Your customers start earning points with every purchase</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <a href="how-it-works.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-accent">What Our Clients Say</h2>
            <p >Businesses of all sizes are seeing real results with PerkUp</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"PerkUp has transformed how we engage with our customers. Our loyalty program has increased repeat visits by 40%!"</p>
                        <div class="d-flex align-items-center">
                            <img src="img/testimonial-1.jpg" alt="Testimonial" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Sarah Johnson</h6>
                                <small >Owner, Brew & Bean Coffee Shop</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"The referral program has been a game-changer for our business. We've seen a 25% increase in new customers."</p>
                        <div class="d-flex align-items-center">
                            <img src="img/testimonial-2.jpg" alt="Testimonial" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Michael Chen</h6>
                                <small >Manager, Urban Fitness Studio</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        </div>
                        <p class="card-text mb-4">"Our customers love the rewards system. It's easy to use and has significantly improved our customer retention."</p>
                        <div class="d-flex align-items-center">
                            <img src="img/testimonial-3.jpg" alt="Testimonial" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Emily Rodriguez</h6>
                                <small >Director, Bella Boutique</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 cta-section">
    <div class="container text-center">
        <h2 class="fw-bold mb-4 text-accent glow-text">Ready to Boost Customer Loyalty?</h2>
        <p class="lead mb-4">Join thousands of businesses that are growing their customer base and increasing revenue with PerkUp.</p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="contact.php" class="btn btn-primary btn-lg">Get Started (Business)</a>
            <a href="signup.php" class="btn btn-secondary btn-lg">Join Now (Customer)</a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
