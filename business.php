<?php
// Fetch business data
$business_id = $_SESSION["id"];
$business_sql = "SELECT b.* FROM businesses b 
                JOIN users u ON b.business_email = u.email 
                WHERE u.id = ?";

$business_stmt = mysqli_prepare($link, $business_sql);
mysqli_stmt_bind_param($business_stmt, "i", $business_id);
mysqli_stmt_execute($business_stmt);
$business_result = mysqli_stmt_get_result($business_stmt);
$business_data = mysqli_fetch_assoc($business_result);
mysqli_stmt_close($business_stmt);

// Fetch total customers
$customers_sql = "SELECT COUNT(*) as total FROM user_businesses WHERE business_id = ?";
$customers_stmt = mysqli_prepare($link, $customers_sql);
mysqli_stmt_bind_param($customers_stmt, "i", $business_id);
mysqli_stmt_execute($customers_stmt);
$customers_result = mysqli_stmt_get_result($customers_stmt);
$customers_data = mysqli_fetch_assoc($customers_result);
$total_customers = $customers_data['total'];
mysqli_stmt_close($customers_stmt);

// Fetch active rewards
$rewards_sql = "SELECT COUNT(*) as total FROM rewards WHERE business_id = ?";
$rewards_stmt = mysqli_prepare($link, $rewards_sql);
mysqli_stmt_bind_param($rewards_stmt, "i", $business_id);
mysqli_stmt_execute($rewards_stmt);
$rewards_result = mysqli_stmt_get_result($rewards_stmt);
$rewards_data = mysqli_fetch_assoc($rewards_result);
$active_rewards = $rewards_data['total'];
mysqli_stmt_close($rewards_stmt);

// Fetch premium members
$premium_sql = "SELECT COUNT(*) as total FROM customers c 
               JOIN user_businesses ub ON c.customer_id = ub.customer_id 
               WHERE ub.business_id = ? AND c.membership_status = 'Premium'";
$premium_stmt = mysqli_prepare($link, $premium_sql);
mysqli_stmt_bind_param($premium_stmt, "i", $business_id);
mysqli_stmt_execute($premium_stmt);
$premium_result = mysqli_stmt_get_result($premium_stmt);
$premium_data = mysqli_fetch_assoc($premium_result);
$premium_members = $premium_data['total'];
mysqli_stmt_close($premium_stmt);

// Fetch recent activity
$activity_sql = "SELECT c.customer_first_name, c.customer_last_name, r.reward_name, rh.reward_type, rh.reward_date 
                FROM reward_history rh 
                JOIN customers c ON rh.customer_id = c.customer_id 
                JOIN rewards r ON rh.reward_id = r.reward_id 
                WHERE r.business_id = ? 
                ORDER BY rh.reward_date DESC LIMIT 5";
$activity_stmt = mysqli_prepare($link, $activity_sql);
mysqli_stmt_bind_param($activity_stmt, "i", $business_id);
mysqli_stmt_execute($activity_stmt);
$activity_result = mysqli_stmt_get_result($activity_stmt);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card">
                <h6>Total Customers</h6>
                <h3><?php echo $total_customers; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h6>Active Rewards</h6>
                <h3><?php echo $active_rewards; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h6>Premium Members</h6>
                <h3><?php echo $premium_members; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <h6>Revenue Generated</h6>
                <h3>$<?php echo number_format($total_customers * 12, 0, '.', ','); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h4>Recent Activity</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Action</th>
                <th>Details</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($activity_result) > 0): ?>
                <?php while ($activity = mysqli_fetch_assoc($activity_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['customer_first_name'] . ' ' . $activity['customer_last_name']); ?></td>
                        <td><?php echo $activity['reward_type'] === 'Earned' ? 'Earned points' : 'Redeemed reward'; ?></td>
                        <td><?php echo htmlspecialchars($activity['reward_name']); ?></td>
                        <td><?php 
                            $time_diff = time() - strtotime($activity['reward_date']);
                            if ($time_diff < 60) {
                                echo 'Just now';
                            } elseif ($time_diff < 3600) {
                                echo floor($time_diff / 60) . ' mins ago';
                            } elseif ($time_diff < 86400) {
                                echo floor($time_diff / 3600) . ' hours ago';
                            } else {
                                echo floor($time_diff / 86400) . ' days ago';
                            }
                        ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No recent activity</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Fetch customers for the modal
$modal_customers_sql = "SELECT c.customer_id, c.customer_first_name, c.customer_last_name, c.membership_points 
                       FROM customers c 
                       JOIN user_businesses ub ON c.customer_id = ub.customer_id 
                       WHERE ub.business_id = ? 
                       ORDER BY c.membership_points DESC LIMIT 10";
$modal_customers_stmt = mysqli_prepare($link, $modal_customers_sql);
mysqli_stmt_bind_param($modal_customers_stmt, "i", $business_id);
mysqli_stmt_execute($modal_customers_stmt);
$modal_customers_result = mysqli_stmt_get_result($modal_customers_stmt);
?>

<!-- Update the Customers Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Customers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php if (mysqli_num_rows($modal_customers_result) > 0): ?>
                        <?php while ($customer = mysqli_fetch_assoc($modal_customers_result)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="<?php echo rand(1, 5); ?>.jpg" class="customer-picture"> 
                                <?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?>
                                <button class="btn btn-outline-primary btn-sm" 
                                        onclick="manageCustomer('<?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?>', <?php echo $customer['customer_id']; ?>)">
                                    Manage
                                </button>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center">No customers found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Update the Rewards Modal -->
<?php
// Fetch eligible customers for rewards
$eligible_customers_sql = "SELECT c.customer_id, c.customer_first_name, c.customer_last_name, c.membership_points 
                          FROM customers c 
                          JOIN user_businesses ub ON c.customer_id = ub.customer_id 
                          WHERE ub.business_id = ? AND c.membership_points >= 100 
                          ORDER BY c.membership_points DESC LIMIT 10";
$eligible_customers_stmt = mysqli_prepare($link, $eligible_customers_sql);
mysqli_stmt_bind_param($eligible_customers_stmt, "i", $business_id);
mysqli_stmt_execute($eligible_customers_stmt);
$eligible_customers_result = mysqli_stmt_get_result($eligible_customers_stmt);

// Fetch nearly eligible customers
$nearly_eligible_sql = "SELECT c.customer_id, c.customer_first_name, c.customer_last_name, c.membership_points 
                       FROM customers c 
                       JOIN user_businesses ub ON c.customer_id = ub.customer_id 
                       WHERE ub.business_id = ? AND c.membership_points >= 50 AND c.membership_points < 100 
                       ORDER BY c.membership_points DESC LIMIT 5";
$nearly_eligible_stmt = mysqli_prepare($link, $nearly_eligible_sql);
mysqli_stmt_bind_param($nearly_eligible_stmt, "i", $business_id);
mysqli_stmt_execute($nearly_eligible_stmt);
$nearly_eligible_result = mysqli_stmt_get_result($nearly_eligible_stmt);
?>

<div class="modal fade" id="rewardsModal" tabindex="-1" aria-labelledby="rewardsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eligible Customers for Rewards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php if (mysqli_num_rows($eligible_customers_result) > 0): ?>
                        <?php while ($customer = mysqli_fetch_assoc($eligible_customers_result)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="<?php echo rand(1, 5); ?>.jpg" class="customer-picture"> 
                                <?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?>
                                <span class="badge bg-success">Eligible</span>
                            </li>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    
                    <?php if (mysqli_num_rows($nearly_eligible_result) > 0): ?>
                        <?php while ($customer = mysqli_fetch_assoc($nearly_eligible_result)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="<?php echo rand(1, 5); ?>.jpg" class="customer-picture"> 
                                <?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?>
                                <span class="badge bg-warning">Nearly Eligible</span>
                            </li>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    
                    <?php if (mysqli_num_rows($eligible_customers_result) === 0 && mysqli_num_rows($nearly_eligible_result) === 0): ?>
                        <li class="list-group-item text-center">No eligible customers found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function manageCustomer(name, customerId) {
    // Create a modal to manage the customer
    const modal = `
        <div class="modal fade" id="manageCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage ${name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Add Points</label>
                            <div class="input-group">
                                <input type="number" id="pointsToAdd" class="form-control" min="1" value="10">
                                <button class="btn btn-primary" onclick="addPoints(${customerId})">Add</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Send Notification</label>
                            <textarea id="notificationText" class="form-control" rows="3" placeholder="Enter notification message"></textarea>
                            <button class="btn btn-primary mt-2" onclick="sendNotification(${customerId})">Send</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer History</label>
                            <div id="customerHistory" class="p-3 bg-light rounded">
                                <p class="text-center">Loading history...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add the modal to the document
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Show the modal
    const manageModal = new bootstrap.Modal(document.getElementById('manageCustomerModal'));
    manageModal.show();
    
    // Load customer history
    fetch(`get_customer_history.php?customer_id=${customerId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('customerHistory').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('customerHistory').innerHTML = '<p class="text-center text-danger">Error loading history</p>';
        });
    
    // Remove the modal when it's hidden
    document.getElementById('manageCustomerModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

function addPoints(customerId) {
    const points = document.getElementById('pointsToAdd').value;
    
    fetch('add_points.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `customer_id=${customerId}&points=${points}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Points added successfully!');
            // Reload the page to update data
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while adding points.');
    });
}

function sendNotification(customerId) {
    const message = document.getElementById('notificationText').value;
    
    if (!message) {
        alert('Please enter a notification message.');
        return;
    }
    
    fetch('send_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `customer_id=${customerId}&message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notification sent successfully!');
            document.getElementById('notificationText').value = '';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while sending the notification.');
    });
}
</script>
