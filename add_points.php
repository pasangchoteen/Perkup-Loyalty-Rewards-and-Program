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
    // Get the customer ID and points from the POST data
    $customer_id = $_POST["customer_id"];
    $points = $_POST["points"];
    
    // Validate input
    if (empty($customer_id) || empty($points) || !is_numeric($points) || $points <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    // Get the business ID from the session
    $business_id = $_SESSION["id"];
    
    // Check if the customer belongs to this business
    $check_sql = "SELECT * FROM user_businesses WHERE customer_id = ? AND business_id = ?";
    $check_stmt = mysqli_prepare($link, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $customer_id, $business_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit;
    }
    
    // Update the customer's points
    $update_sql = "UPDATE customers SET membership_points = membership_points + ? WHERE customer_id = ?";
    $update_stmt = mysqli_prepare($link, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $points, $customer_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Log the points addition in the reward history
        $reward_sql = "INSERT INTO reward_history (customer_id, reward_id, reward_type, points_earned, reward_date) 
                      VALUES (?, NULL, 'Earned', ?, NOW())";
        $reward_stmt = mysqli_prepare($link, $reward_sql);
        mysqli_stmt_bind_param($reward_stmt, "ii", $customer_id, $points);
        mysqli_stmt_execute($reward_stmt);
        
        echo json_encode(['success' => true, 'message' => 'Points added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating points']);
    }
    
    // Close statements
    mysqli_stmt_close($check_stmt);
    mysqli_stmt_close($update_stmt);
    mysqli_stmt_close($reward_stmt);
} else {
    // Not a POST request
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

// Close connection
mysqli_close($link);
?>
