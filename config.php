<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'perkup_db');

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

// Set charset to ensure proper encoding
mysqli_set_charset($link, "utf8mb4");

// Site configuration
define('SITE_NAME', 'PerkUp');
define('SITE_URL', 'http://localhost/perkup');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

// Function to check if user is a customer
function isCustomer() {
    return isLoggedIn() && $_SESSION["user_type"] === "customer";
}

// Function to check if user is a business
function isBusiness() {
    return isLoggedIn() && $_SESSION["user_type"] === "business";
}

// Function to check if user is an admin
function isAdmin() {
    return isLoggedIn() && $_SESSION["user_type"] === "admin";
}

// Function to redirect with a message
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit;
}

// Function to display flash messages
function displayFlashMessages() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        $alertClass = ($type === 'error') ? 'alert-danger' : 'alert-' . $type;
        
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo $_SESSION['flash_message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
