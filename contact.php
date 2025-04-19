<?php
$pageTitle = "Contact Us";
$currentPage = "contact";
require_once 'config.php';

// Define variables and initialize with empty values
$name = $email = $phone = $business_name = $message = "";
$name_err = $email_err = $business_name_err = $message_err = "";
$success_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate business name
    if (empty(trim($_POST["business_name"]))) {
        $business_name_err = "Please enter your business name.";
    } else {
        $business_name = trim($_POST["business_name"]);
    }
    
    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }
    
    // Get phone
    $phone = trim($_POST["phone"]);
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($business_name_err) && empty($message_err)) {
        // In a real application, you would save this to a database and/or send an email
        // For now, we'll just show a success message
        $success_message = "Thank you for your interest in PerkUp! Our team will contact you shortly to set up your business account.";
        
        // Clear form fields after successful submission
        $name = $email = $phone = $business_name = $message = "";
    }
}

require_once 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Contact Us</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center mb-4">Interested in registering your business with PerkUp? Fill out the form below and our team will contact you to set up your account.</p>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                <span class="invalid-feedback"><?php echo $name_err; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo $phone; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" name="business_name" id="business_name" class="form-control <?php echo (!empty($business_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $business_name; ?>">
                                <span class="invalid-feedback"><?php echo $business_name_err; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea name="message" id="message" class="form-control <?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $message; ?></textarea>
                                <span class="invalid-feedback"><?php echo $message_err; ?></span>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="text-accent mb-3">Why Join PerkUp?</h4>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-light">
                            <i class="fas fa-check-circle text-accent me-2"></i> Increase customer retention and loyalty
                        </li>
                        <li class="list-group-item bg-transparent text-light">
                            <i class="fas fa-check-circle text-accent me-2"></i> Drive repeat business with targeted rewards
                        </li>
                        <li class="list-group-item bg-transparent text-light">
                            <i class="fas fa-check-circle text-accent me-2"></i> Attract new customers through referrals
                        </li>
                        <li class="list-group-item bg-transparent text-light">
                            <i class="fas fa-check-circle text-accent me-2"></i> Gain valuable insights into customer behavior
                        </li>
                        <li class="list-group-item bg-transparent text-light">
                            <i class="fas fa-check-circle text-accent me-2"></i> Easy to set up and manage
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
