<!-- Footer -->
<footer class="footer mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="text-accent">PerkUp</h5>
                <p >Empowering local businesses to build lasting customer relationships through customizable loyalty programs.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="text-accent">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="features.php">Features</a></li>
                    <li><a href="how-it-works.php">How It Works</a></li>
                    <li><a href="pricing.php">Pricing</a></li>
                    <li><a href="businesses.php">Businesses</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="text-accent">Account</h5>
                <ul class="list-unstyled">
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                    <?php if (isCustomer()): ?>
                        <li><a href="userprofile.php">My Profile</a></li>
                    <?php elseif (isBusiness()): ?>
                        <li><a href="businessprofile.php">Business Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-accent">Legal</h5>
                <ul class="list-unstyled">
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms-of-service.php">Terms of Service</a></li>
                    <li><a href="cookie-policy.php">Cookie Policy</a></li>
                    <li><a href="gdpr.php">GDPR Compliance</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4 bg-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p>Designed with <i class="fas fa-heart text-accent"></i> for loyal customers</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($extraJS)): ?>
<!-- Page-specific JavaScript -->
<script src="<?php echo $extraJS; ?>"></script>
<?php endif; ?>
</body>
</html>
