<?php
// Show all errors for debugging (only use in development!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the session
session_start();

// Include config file for database connection
require_once "config.php";  // Ensure this is properly included

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $business_name = $_POST['business_name'];
    $business_email = $_POST['business_email'];
    $business_password = $_POST['business_password'];  // The password entered by the business
    
    // Check if any field is empty
    if (empty($business_name) || empty($business_email) || empty($business_password)) {
        echo "All fields are required.";
        exit();
    }

    // Hash the password using bcrypt
    $hashed_password = password_hash($business_password, PASSWORD_BCRYPT);

    // Check if the username (business_name) already exists in the users table
    $check_user_query = "SELECT * FROM users WHERE username = ?";
    $stmt_check_user = $link->prepare($check_user_query);
    $stmt_check_user->bind_param("s", $business_name);
    $stmt_check_user->execute();
    $result_check_user = $stmt_check_user->get_result();

    if ($result_check_user->num_rows > 0) {
        echo "Username already exists. Please choose another one.";
        exit();  // Stop further execution
    }

    // SQL to insert into businesses table
    $sql_business = "INSERT INTO businesses (business_name, business_email, business_password) VALUES (?, ?, ?)";
    
    // Prepare the statement
    $stmt_business = $link->prepare($sql_business);  // Use $link here instead of $conn
    $stmt_business->bind_param("sss", $business_name, $business_email, $hashed_password);
    
    // Execute the statement and check if the insertion is successful
    if ($stmt_business->execute()) {
        // After inserting the business, now insert into users table
        $sql_user = "INSERT INTO users (username, email, password, user_type, created_at, updated_at) VALUES (?, ?, ?, 'business', NOW(), NOW())";
        
        // Prepare the statement for users table
        $stmt_user = $link->prepare($sql_user);  // Use $link here instead of $conn
        $stmt_user->bind_param("sss", $business_name, $business_email, $hashed_password);
        
        if ($stmt_user->execute()) {
            echo "Business registered successfully!";
        } else {
            echo "Error inserting into users table: " . $link->error;  // Use $link here instead of $conn
        }
    } else {
        echo "Error inserting into businesses table: " . $link->error;  // Use $link here instead of $conn
    }

    // Close the prepared statements and connection
    $stmt_business->close();
    $stmt_user->close();
    $link->close();  // Use $link here instead of $conn
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register your business on PerkUp, your loyalty and rewards platform.">
    <title>Business Registration - PerkUp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Business Registration</h2>
        <form action="business_registration.php" method="POST">
            <div class="mb-3">
                <label for="business_name" class="form-label">Business Name</label>
                <input type="text" class="form-control" id="business_name" name="business_name" required>
            </div>
            <div class="mb-3">
                <label for="business_email" class="form-label">Business Email</label>
                <input type="email" class="form-control" id="business_email" name="business_email" required>
            </div>
            <div class="mb-3">
                <label for="business_password" class="form-label">Business Password</label>
                <input type="password" class="form-control" id="business_password" name="business_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register Business</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
