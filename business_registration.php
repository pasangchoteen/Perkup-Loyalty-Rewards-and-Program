<?php
$pageTitle = "Business Registration";
$currentPage = "admin";
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isAdmin()) {
    // Redirect to login page with error message
    redirectWithMessage("login.php", "You must be logged in as an administrator to access this page.", "error");
    exit;
}

// Define variables and initialize with empty values
$business_name = $business_email = $business_description = $business_address = $business_phone = $business_category = "";
$username = $password = $confirm_password = "";
$business_name_err = $business_email_err = $username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate business name
    if (empty(trim($_POST["business_name"]))) {
        $business_name_err = "Please enter a business name.";
    } else {
        $business_name = trim($_POST["business_name"]);
    }
    
    // Validate business email
    if (empty(trim($_POST["business_email"]))) {
        $business_email_err = "Please enter a business email.";
    } elseif (!filter_var(trim($_POST["business_email"]), FILTER_VALIDATE_EMAIL)) {
        $business_email_err = "Please enter a valid email address.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["business_email"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $business_email_err = "This email is already registered.";
                } else {
                    $business_email = trim($_POST["business_email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Get other business details
    $business_description = trim($_POST["business_description"]);
    $business_address = trim($_POST["business_address"]);
    $business_phone = trim($_POST["business_phone"]);
    $business_category = trim($_POST["business_category"]);
    
    // Check input errors before inserting in database
    if (empty($business_name_err) && empty($business_email_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Begin transaction
        mysqli_begin_transaction($link);
        
        try {
            // Prepare an insert statement for users table
            // Always set user_type to 'business' for business_registration.php
            $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'business')";
            
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
                
                // Set parameters
                $param_username = $username;
                $param_email = $business_email;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    $user_id = mysqli_insert_id($link);
                    
                    // Insert into businesses table
                    $sql_business = "INSERT INTO businesses (business_email, business_name, business_description, business_address, business_phone, business_category) VALUES (?, ?, ?, ?, ?, ?)";
                    
                    if ($stmt_business = mysqli_prepare($link, $sql_business)) {
                        mysqli_stmt_bind_param($stmt_business, "ssssss", $business_email, $business_name, $business_description, $business_address, $business_phone, $business_category);
                        
                        if (!mysqli_stmt_execute($stmt_business)) {
                            throw new Exception("Error inserting business data.");
                        }
                        
                        mysqli_stmt_close($stmt_business);
                    } else {
                        throw new Exception("Error preparing business statement.");
                    }
                    
                    // Commit transaction
                    mysqli_commit($link);
                    
                    // Redirect to success page
                    redirectWithMessage("admin.php", "Business account created successfully.", "success");
                    exit();
                } else {
                    throw new Exception("Error executing user statement.");
                }
                
                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing user statement.");
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($link);
            echo "Something went wrong. Please try again later. Error: " . $e->getMessage();
        }
    }
    
    // Close connection
    mysqli_close($link);
}

require_once 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Register New Business</h2>
                    <p class="text-center text-muted">Admin use only - Create a new business account</p>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <h4 class="mb-3 text-accent">Business Information</h4>
                        
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" name="business_name" id="business_name" class="form-control <?php echo (!empty($business_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $business_name; ?>">
                            <span class="invalid-feedback"><?php echo $business_name_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <input type="email" name="business_email" id="business_email" class="form-control <?php echo (!empty($business_email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $business_email; ?>">
                            <span class="invalid-feedback"><?php echo $business_email_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_description" class="form-label">Business Description</label>
                            <textarea name="business_description" id="business_description" class="form-control" rows="3"><?php echo $business_description; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <input type="text" name="business_address" id="business_address" class="form-control" value="<?php echo $business_address; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="business_phone" class="form-label">Business Phone</label>
                                <input type="text" name="business_phone" id="business_phone" class="form-control" value="<?php echo $business_phone; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_category" class="form-label">Business Category</label>
                            <select name="business_category" id="business_category" class="form-select">
                                <option value="">Select a category</option>
                                <option value="Food & Drinks" <?php echo ($business_category == "Food & Drinks") ? "selected" : ""; ?>>Food & Drinks</option>
                                <option value="Retail" <?php echo ($business_category == "Retail") ? "selected" : ""; ?>>Retail</option>
                                <option value="Services" <?php echo ($business_category == "Services") ? "selected" : ""; ?>>Services</option>
                                <option value="Entertainment" <?php echo ($business_category == "Entertainment") ? "selected" : ""; ?>>Entertainment</option>
                                <option value="Travel" <?php echo ($business_category == "Travel") ? "selected" : ""; ?>>Travel</option>
                            </select>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h4 class="mb-3 text-accent">Account Information</h4>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register Business</button>
                            <a href="admin.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
