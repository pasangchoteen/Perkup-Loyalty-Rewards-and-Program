<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['business_id']) || !is_numeric($_GET['business_id'])) {
    echo json_encode([]);
    exit;
}

$business_id = (int) $_GET['business_id'];
$rewards = [];

$sql = "SELECT reward_name, reward_description, points_required
        FROM rewards
        WHERE business_id = ? AND is_active = 1
        ORDER BY points_required ASC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $business_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $rewards[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
}

echo json_encode($rewards);
exit;
