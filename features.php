<?php
$pageTitle = "Features";
$currentPage = "features";
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold">Features</h1>
                <p class="lead">Discover all the powerful features our platform offers to help your business grow.</p>
            </div>
            <div class="col-lg-6">
                <img src="img/features-hero.svg" alt="Features" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Main Features -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Core Features</h2>
            <p class="text-muted">Everything you need to succeed</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <div class="card-icon mx-auto">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="card-title text-center">User Management</h4>
                        <p class="card-text text-muted">Easily manage user accounts, permissions, and access levels with our intuitive user management system.</p>
                        <ul class="mt-3">
                            <li>Role-based access control</li>
                            <li>User activity tracking</li>
                            <li>Secure authentication</li>
                            <li>Profile customization</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <div class="card-icon mx-auto">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="card-title text-center">Analytics Dashboard</h4>
                        <p class="card-text text-muted">Gain valuable insights with our comprehensive analytics dashboard that tracks all important metrics.</p>
                        <ul class="mt-3">
                            <li>Real-time data visualization</li>
                            <li>Custom reporting</li>
                            <li>Performance tracking</li>
                            <li>Export capabilities</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body">
                        <div class="card-icon mx-auto">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4 class="card-title text-center">Notifications</h4>
                        <p class="card-text text-muted">Stay informed with our customizable notification system that keeps you updated on important events.</p>
                        <ul class="mt-3">
                            <li>Email notifications</li>
                            <li>In-app alerts</li>
                            <li>Scheduled reminders</li>
                            <li>Notification preferences</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Features -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Additional Features</h2>
            <p class="text-muted">More ways to enhance your experience</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                        <h5>Mobile Responsive</h5>
                        <p class="text-muted small">Access the platform from any device with our fully responsive design.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                        <h5>Cloud Storage</h5>
                        <p class="text-muted small">Store and access your files securely from anywhere with cloud storage.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h5>Scheduling</h5>
                        <p class="text-muted small">Plan and organize your activities with our intuitive scheduling system.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                        <h5>Messaging</h5>
                        <p class="text-muted small">Communicate effectively with integrated messaging features.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Comparison -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Feature Comparison</h2>
            <p class="text-muted">See how our plans compare</p>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Feature</th>
                        <th class="text-center">Free</th>
                        <th class="text-center">Basic</th>
                        <th class="text-center">Pro</th>
                        <th class="text-center">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>User Management</td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Analytics Dashboard</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Notifications</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Standard</td>
                        <td class="text-center">Advanced</td>
                        <td class="text-center">Custom</td>
                    </tr>
                    <tr>
                        <td>Mobile Responsive</td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Cloud Storage</td>
                        <td class="text-center">1GB</td>
                        <td class="text-center">10GB</td>
                        <td class="text-center">50GB</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td>Scheduling</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Messaging</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Priority Support</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="pricing.php" class="btn btn-primary">View Pricing</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Ready to Experience These Features?</h2>
        <p class="lead mb-4">Sign up today and start exploring all the powerful features our platform offers.</p>
        <a href="signup.php" class="btn btn-light btn-lg px-5">Get Started</a>
    </div>
</section>

<?php require_once 'footer.php'; ?>

 
