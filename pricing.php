<?php
$pageTitle = "Pricing";
$currentPage = "pricing";
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold">Pricing Plans</h1>
                <p class="lead">Choose the perfect plan for your needs and budget.</p>
            </div>
            <div class="col-lg-6">
                <img src="img/pricing-hero.svg" alt="Pricing" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Plans -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Transparent Pricing, No Hidden Fees</h2>
            <p class="text-muted">Select the plan that works best for you</p>
            
            <!-- Billing Toggle -->
            <div class="d-flex align-items-center justify-content-center mt-4">
                <span class="me-2">Monthly</span>
                <div class="form-check form-switch mx-2">
                    <input class="form-check-input" type="checkbox" id="billingToggle">
                    <label class="form-check-label" for="billingToggle"></label>
                </div>
                <span>Annual <span class="badge bg-success ms-1">Save 20%</span></span>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Free Plan -->
            <div class="col-md-4">
                <div class="card h-100 pricing-card">
                    <div class="card-body">
                        <h5 class="card-title">Free</h5>
                        <p class="text-muted">For individuals just getting started</p>
                        <div class="price">
                            $0<small>/month</small>
                        </div>
                        <ul class="feature-list">
                            <li>Basic account features</li>
                            <li>Limited access to tools</li>
                            <li>1GB storage</li>
                            <li>Community support</li>
                            <li>Ad-supported experience</li>
                        </ul>
                        <a href="signup.php" class="btn btn-outline-primary w-100">Get Started</a>
                    </div>
                </div>
            </div>
            
            <!-- Basic Plan -->
            <div class="col-md-4">
                <div class="card h-100 pricing-card border-primary">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <span class="badge bg-white text-primary">Most Popular</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Basic</h5>
                        <p class="text-muted">For small businesses and professionals</p>
                        <div class="price monthly-price">
                            $29<small>/month</small>
                        </div>
                        <div class="price annual-price" style="display: none;">
                            $23<small>/month</small>
                        </div>
                        <ul class="feature-list">
                            <li>All Free features</li>
                            <li>Full access to tools</li>
                            <li>10GB storage</li>
                            <li>Priority email support</li>
                            <li>Ad-free experience</li>
                            <li>Advanced analytics</li>
                        </ul>
                        <a href="signup.php?plan=basic" class="btn btn-primary w-100">Choose Basic</a>
                    </div>
                </div>
            </div>
            
            <!-- Pro Plan -->
            <div class="col-md-4">
                <div class="card h-100 pricing-card">
                    <div class="card-body">
                        <h5 class="card-title">Pro</h5>
                        <p class="text-muted">For growing businesses and teams</p>
                        <div class="price monthly-price">
                            $79<small>/month</small>
                        </div>
                        <div class="price annual-price" style="display: none;">
                            $63<small>/month</small>
                        </div>
                        <ul class="feature-list">
                            <li>All Basic features</li>
                            <li>Team collaboration tools</li>
                            <li>50GB storage</li>
                            <li>24/7 phone and email support</li>
                            <li>Custom branding options</li>
                            <li>API access</li>
                            <li>Dedicated account manager</li>
                        </ul>
                        <a href="signup.php?plan=pro" class="btn btn-outline-primary w-100">Choose Pro</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enterprise Plan -->
        <div class="card mt-5">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3>Enterprise Plan</h3>
                        <p class="lead mb-4">Custom solutions for large organizations with specific needs</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i> Unlimited storage</li>
                            <li><i class="fas fa-check text-success me-2"></i> Custom integrations</li>
                            <li><i class="fas fa-check text-success me-2"></i> Dedicated support team</li>
                            <li><i class="fas fa-check text-success me-2"></i> On-premise deployment options</li>
                            <li><i class="fas fa-check text-success me-2"></i> Custom development</li>
                        </ul>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                        <h4>Contact us for pricing</h4>
                        <a href="contact.php" class="btn btn-primary btn-lg mt-3">Get in Touch</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Comparison -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Feature Comparison</h2>
            <p class="text-muted">Compare plans to find the right fit for your needs</p>
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
                        <td>Storage</td>
                        <td class="text-center">1GB</td>
                        <td class="text-center">10GB</td>
                        <td class="text-center">50GB</td>
                        <td class="text-center">Unlimited</td>
                    </tr>
                    <tr>
                        <td>Analytics</td>
                        <td class="text-center">Basic</td>
                        <td class="text-center">Advanced</td>
                        <td class="text-center">Advanced</td>
                        <td class="text-center">Custom</td>
                    </tr>
                    <tr>
                        <td>Support</td>
                        <td class="text-center">Community</td>
                        <td class="text-center">Email</td>
                        <td class="text-center">24/7 Phone & Email</td>
                        <td class="text-center">Dedicated Team</td>
                    </tr>
                    <tr>
                        <td>API Access</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Custom Branding</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Team Collaboration</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Custom Integrations</td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Find answers to common questions about our pricing</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="pricingFaqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="pricingHeadingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapseOne" aria-expanded="true" aria-controls="pricingCollapseOne">
                                Can I change my plan later?
                            </button>
                        </h2>
                        <div id="pricingCollapseOne" class="accordion-collapse collapse show" aria-labelledby="pricingHeadingOne" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body">
                                Yes, you can upgrade, downgrade, or cancel your plan at any time. Changes to your subscription will take effect immediately or at the end of your current billing cycle, depending on the type of change.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="pricingHeadingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapseTwo" aria-expanded="false" aria-controls="pricingCollapseTwo">
                                Is there a free trial available?
                            </button>
                        </h2>
                        <div id="pricingCollapseTwo" class="accordion-collapse collapse" aria-labelledby="pricingHeadingTwo" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body">
                                Yes, we offer a 14-day free trial for our Basic and Pro plans. No credit card is required to start your trial. You can upgrade to a paid plan at any time during or after your trial period.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="pricingHeadingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapseThree" aria-expanded="false" aria-controls="pricingCollapseThree">
                                Do you offer discounts for non-profits or educational institutions?
                            </button>
                        </h2>
                        <div id="pricingCollapseThree" class="accordion-collapse collapse" aria-labelledby="pricingHeadingThree" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body">
                                Yes, we offer special pricing for non-profit organizations, educational institutions, and students. Please contact our sales team for more information about our discount programs.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="pricingHeadingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapseFour" aria-expanded="false" aria-controls="pricingCollapseFour">
                                What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="pricingCollapseFour" class="accordion-collapse collapse" aria-labelledby="pricingHeadingFour" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body">
                                We accept all major credit cards (Visa, MasterCard, American Express, Discover), PayPal, and bank transfers for annual plans. For Enterprise customers, we also offer invoicing options.
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
        <p class="lead mb-4">Choose the plan that's right for you and start using our platform today.</p>
        <a href="signup.php" class="btn btn-light btn-lg px-5">Sign Up Now</a>
    </div>
</section>

<!-- Pricing Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const billingToggle = document.getElementById('billingToggle');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const annualPrices = document.querySelectorAll('.annual-price');
    
    billingToggle.addEventListener('change', function() {
        if (this.checked) {
            // Show annual prices
            monthlyPrices.forEach(el => el.style.display = 'none');
            annualPrices.forEach(el => el.style.display = 'block');
        } else {
            // Show monthly prices
            monthlyPrices.forEach(el => el.style.display = 'block');
            annualPrices.forEach(el => el.style.display = 'none');
        }
    });
});
</script>

<?php require_once 'footer.php'; ?>

