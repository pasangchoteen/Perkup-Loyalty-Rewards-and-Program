<?php
// Initialize the session
session_start();

// Check if the user is logged in and is a business
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "business") {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include config file
require_once "config.php";

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the customer ID and message from the POST data
    $customer_id = $_POST["customer_id"];
    $message = $_POST["message"];
    
    // Validate input
    if (empty($customer_id) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    // Get the business ID from the session
    $business_id = $_SESSION["id"];
    
    // Check if the customer belongs to this business
    $check_sql = "SELECT c.customer_email FROM customers c 
                 JOIN user_businesses ub ON c.customer_id = ub.customer_id 
                 WHERE c.customer_id = ? AND ub.business_id = ?";
    $check_stmt = mysqli_prepare($link, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $customer_id, $business_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit;
    }
    
    // Get the customer's email
    $customer = mysqli_fetch_assoc($check_result);
    $customer_email = $customer['customer_email'];
    
    // Get the business name
    $business_sql = "SELECT business_name FROM businesses WHERE business_id = ?";
    $business_stmt = mysqli_prepare($link, $business_sql);
    mysqli_stmt_bind_param($business_stmt, "i", $business_id);
    mysqli_stmt_execute($business_stmt);
    $business_result = mysqli_stmt_get_result($business_stmt);
    $business = mysqli_fetch_assoc($business_result);
    $business_name = $business['business_name'];
    
    // In a real application, you would send an email or push notification here
    // For this example, we'll just simulate success
    
    // Log the notification in the database (you would need to create a notifications table)
    // This is just a placeholder for the actual implementation
    
    echo json_encode(['success' => true, 'message' => 'Notification sent to ' . $customer_email]);
    
    // Close statements
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($business_stmt);
} else {
    // Not a POST request
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

// Close connection
mysqli_close($link);
?>
