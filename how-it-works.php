<?php
$pageTitle = "How It Works";
$currentPage = "how-it-works";
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold">How It Works</h1>
                <p class="lead">Learn how our platform can help your business grow and succeed.</p>
            </div>
            <div class="col-lg-6">
                <img src="img/how-it-works-hero.svg" alt="How It Works" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Process Steps -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Simple Process, Powerful Results</h2>
            <p class="text-muted">Follow these steps to get started with our platform</p>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Step 1 -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-4 bg-primary text-white d-flex align-items-center justify-content-center py-4">
                                <div class="text-center">
                                    <div class="display-1 fw-bold">1</div>
                                    <h3>Sign Up</h3>
                                </div>
                            </div>
                            <div class="col-md-8 p-4">
                                <h4>Create Your Account</h4>
                                <p>Getting started is easy. Simply create an account by providing your basic information. Choose between a personal account or a business account depending on your needs.</p>
                                <ul class="mt-3">
                                    <li>Fill out the registration form</li>
                                    <li>Verify your email address</li>
                                    <li>Set up your profile with relevant information</li>
                                    <li>Choose your account preferences</li>
                                </ul>
                                <a href="signup.php" class="btn btn-primary mt-3">Sign Up Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-8 p-4 order-md-1 order-2">
                                <h4>Customize Your Experience</h4>
                                <p>Once your account is set up, you can customize your experience based on your specific needs and preferences. Add your business details, set up your profile, and configure your settings.</p>
                                <ul class="mt-3">
                                    <li>Complete your profile with detailed information</li>
                                    <li>Upload your logo and business images</li>
                                    <li>Set your preferences and notification settings</li>
                                    <li>Connect with other users and businesses</li>
                                </ul>
                            </div>
                            <div class="col-md-4 bg-info text-white d-flex align-items-center justify-content-center py-4 order-md-2 order-1">
                                <div class="text-center">
                                    <div class="display-1 fw-bold">2</div>
                                    <h3>Customize</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-4 bg-success text-white d-flex align-items-center justify-content-center py-4">
                                <div class="text-center">
                                    <div class="display-1 fw-bold">3</div>
                                    <h3>Engage</h3>
                                </div>
                            </div>
                            <div class="col-md-8 p-4">
                                <h4>Engage with the Platform</h4>
                                <p>Start using the platform's features to grow your business or enhance your personal experience. Connect with other users, explore businesses, and take advantage of all the tools available.</p>
                                <ul class="mt-3">
                                    <li>Discover and connect with businesses</li>
                                    <li>Participate in discussions and events</li>
                                    <li>Share content and updates</li>
                                    <li>Build your network and reputation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-8 p-4 order-md-1 order-2">
                                <h4>Grow and Succeed</h4>
                                <p>As you continue to use the platform, you'll see growth and success in your endeavors. Track your progress, analyze your performance, and make data-driven decisions to optimize your results.</p>
                                <ul class="mt-3">
                                    <li>Monitor your analytics and performance metrics</li>
                                    <li>Adjust your strategies based on insights</li>
                                    <li>Expand your reach and influence</li>
                                    <li>Achieve your business or personal goals</li>
                                </ul>
                            </div>
                            <div class="col-md-4 bg-warning text-white d-flex align-items-center justify-content-center py-4 order-md-2 order-1">
                                <div class="text-center">
                                    <div class="display-1 fw-bold">4</div>
                                    <h3>Grow</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Find answers to common questions about our platform</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How much does it cost to use the platform?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer various pricing plans to suit different needs and budgets. Our basic plan is free, while our premium plans offer additional features and benefits. Visit our <a href="pricing.php">Pricing</a> page for detailed information on all our plans.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Can I upgrade or downgrade my plan later?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, you can upgrade or downgrade your plan at any time. Changes to your subscription will take effect immediately or at the end of your current billing cycle, depending on the type of change.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Is my data secure on your platform?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely. We take data security very seriously. All data is encrypted both in transit and at rest. We use industry-standard security measures to protect your information and comply with all relevant data protection regulations. For more details, please review our <a href="privacy-policy.php">Privacy Policy</a>.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How can I get support if I have questions or issues?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer multiple support channels to assist you. You can contact our support team via email, use our live chat feature during business hours, or browse our comprehensive knowledge base for self-help resources. Premium plan subscribers also have access to priority support.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Can I cancel my subscription at any time?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, you can cancel your subscription at any time. If you cancel, you'll continue to have access to your paid features until the end of your current billing cycle. After that, your account will revert to the free plan or be deactivated, depending on your preference.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of users already benefiting from our platform.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="signup.php" class="btn btn-light btn-lg px-5">Sign Up Now</a>
            <a href="pricing.php" class="btn btn-outline-light btn-lg px-5">View Pricing</a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>

