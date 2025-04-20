<?php
include 'config.php';
include 'includes/header.php';

// Replace with session or dynamic value if needed
$business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
if ($business_id <= 0) {
    echo '<div class="alert alert-danger">Invalid or missing business ID.</div>';
    include 'includes/footer.php';
    exit;
}

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch customers associated with this business with pagination
$sql = "SELECT c.*, ub.joined_date 
        FROM user_businesses ub
        JOIN customers c ON ub.customer_id = c.customer_id
        WHERE ub.business_id = ?
        ORDER BY ub.joined_date DESC
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'iii', $business_id, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get total customer count for pagination
$countSql = "SELECT COUNT(*) AS total FROM user_businesses WHERE business_id = ?";
$countStmt = mysqli_prepare($link, $countSql);
mysqli_stmt_bind_param($countStmt, 'i', $business_id);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalCustomers = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalCustomers / $limit);
?>

<h1 class="mb-4">Customers for Business #<?php echo $business_id; ?></h1>

<?php if (mysqli_num_rows($result) === 0): ?>
    <div class="text-center py-4">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <p class="text-muted">No customers found for this business.</p>
    </div>
<?php else: ?>
    <ul class="list-group list-group-flush">
        <?php while ($customer = mysqli_fetch_assoc($result)): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0"><?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?></h6>
                    <div class="activity-time">
                        <small>
                            Joined: <?php echo date('M d, Y', strtotime($customer['joined_date'])); ?>
                        </small>
                    </div>
                </div>
                <span class="badge badge-primary badge-pill"><?php echo $customer['membership_points']; ?> pts</span>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?business_id=<?php echo $business_id; ?>&page=1">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="?business_id=<?php echo $business_id; ?>&page=<?php echo $page - 1; ?>">&laquo;</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?business_id=<?php echo $business_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?business_id=<?php echo $business_id; ?>&page=<?php echo $page + 1; ?>">&raquo;</a></li>
                <li class="page-item"><a class="page-link" href="?business_id=<?php echo $business_id; ?>&page=<?php echo $totalPages; ?>">&raquo;&raquo;</a></li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
