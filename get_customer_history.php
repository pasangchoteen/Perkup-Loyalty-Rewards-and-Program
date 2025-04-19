<?php
// Initialize the session
session_start();

// Check if the user is logged in and is a business
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "business") {
    header("HTTP/1.1 403 Forbidden");
    echo "Unauthorized access";
    exit;
}

// Include config file
require_once "config.php";

// Get the customer ID from the GET parameters
$customer_id = $_GET["customer_id"];

// Validate input
if (empty($customer_id) || !is_numeric($customer_id)) {
    echo "<p class='text-center text-danger'>Invalid customer ID</p>";
    exit;
}

// Get  {
    echo "<p class='text-center text-danger'>Invalid customer ID</p>";
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
    echo "<p class='text-center text-danger'>Customer not found</p>";
    exit;
}

// Get the customer's history
$history_sql = "SELECT rh.reward_type, rh.points_earned, rh.points_redeemed, r.reward_name, rh.reward_date 
               FROM reward_history rh 
               LEFT JOIN rewards r ON rh.reward_id = r.reward_id 
               WHERE rh.customer_id = ? 
               ORDER BY rh.reward_date DESC LIMIT 10";
$history_stmt = mysqli_prepare($link, $history_sql);
mysqli_stmt_bind_param($history_stmt, "i", $customer_id);
mysqli_stmt_execute($history_stmt);
$history_result = mysqli_stmt_get_result($history_stmt);

// Display the history
if (mysqli_num_rows($history_result) > 0) {
    echo "<ul class='list-group'>";
    while ($history = mysqli_fetch_assoc($history_result)) {
        $date = date("M d, Y", strtotime($history['reward_date']));
        $time = date("h:i A", strtotime($history['reward_date']));
        
        if ($history['reward_type'] === 'Earned') {
            echo "<li class='list-group-item'>";
            echo "<div class='d-flex justify-content-between'>";
            echo "<span>Earned {$history['points_earned']} points</span>";
            echo "<small>{$date} at {$time}</small>";
            echo "</div>";
            echo "</li>";
        } else {
            echo "<li class='list-group-item'>";
            echo "<div class='d-flex justify-content-between'>";
            echo "<span>Redeemed {$history['points_redeemed']} points for {$history['reward_name']}</span>";
            echo "<small>{$date} at {$time}</small>";
            echo "</div>";
            echo "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p class='text-center'>No history found for this customer</p>";
}

// Close statements
mysqli_stmt_close($check_stmt);
mysqli_stmt_close($history_stmt);

// Close connection
mysqli_close($link);
?>
