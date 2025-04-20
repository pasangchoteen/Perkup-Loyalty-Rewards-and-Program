<?php

$pageTitle = "Business Dashboard";
$currentPage = "profile";
$extraCSS = "css/profile.css";

require_once 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug information for file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    error_log("POST request received for profile update");
    if (isset($_FILES['profile_image'])) {
        error_log("File upload details: " . json_encode($_FILES['profile_image']));
    } else {
        error_log("No file upload found in request");
    }
}

// Check if user is logged in and is a business
if (!isBusiness()) {
    // Redirect to login page with error message
    redirectWithMessage("login.php", "You must be logged in as a business to access this page.", "error");
    exit;
}

// Ensure session is started if not already done in config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get business data
$business_data = array();
$rewards = array();
$customers = array();
$recent_activity = array();
// Initialize variables and arrays
$business_data = [];
$referrals = [];
$businesses = [];
$update_err = $update_success = '';
$success_msg = $error_msg = '';

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
$upload_dir = 'uploads/';
$max_file_size = 2 * 1024 * 1024; // 2MB

// CSRF Token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get business data from database
$sql = "SELECT b.*, u.username, u.email 
        FROM businesses b 
        JOIN users u ON b.business_email = u.email 
        WHERE b.business_email = ?";


if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["email"]);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $business_data= $row;
        } else {
            redirectWithMessage("login.php", "Business record not found.", "error");
            exit;
        }
    } else {
        error_log("Execute failed: " . mysqli_error($link));
        redirectWithMessage("error.php", "An error occurred while processing your request.", "error");
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Get rewards offered by the business
$sql = "SELECT * FROM rewards WHERE business_id = ? ORDER BY points_required ASC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $business_data["business_id"]);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $rewards[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Get customers associated with the business
$sql = "SELECT c.*, ub.joined_date 
        FROM user_businesses ub 
        JOIN customers c ON ub.customer_id = c.customer_id 
        WHERE ub.business_id = ? 
        ORDER BY ub.joined_date DESC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $business_data["business_id"]);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Get recent reward activity
$sql = "SELECT rh.*, c.customer_first_name, c.customer_last_name, r.reward_name 
        FROM reward_history rh 
        JOIN customers c ON rh.customer_id = c.customer_id 
        LEFT JOIN rewards r ON rh.reward_id = r.reward_id 
        WHERE (r.business_id = ? OR rh.reward_id IS NULL) 
        ORDER BY rh.reward_date DESC 
        LIMIT 10";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $business_data["business_id"]);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $recent_activity[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Update business profile
if (!isset($_SESSION['business_email']) && isset($_SESSION['email'])) {
    $_SESSION['business_email'] = $_SESSION['email'];
}

// Fetch the business data using the business_email
$business_email = $_SESSION['business_email'];
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate and sanitize inputs
    $business_name        = trim($_POST['business_name']);
    $business_category    = trim($_POST['business_category']);
    $business_description = trim($_POST['business_description']);
    $business_address     = trim($_POST['business_address']);
    $business_phone       = trim($_POST['business_phone']);


    // Process image upload if a file was provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $file_tmp   = $_FILES['profile_image']['tmp_name'];
            $file_name  = basename($_FILES['profile_image']['name']);
            $file_size  = $_FILES['profile_image']['size'];
            $file_type  = $_FILES['profile_image']['type'];

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validate file type and size
            if (!in_array($ext, $allowed_extensions)) {
                $error_msg = "Image upload failed: Invalid file extension. Allowed types are JPG, PNG, and GIF.";
            } elseif ($file_size > $max_file_size) {
                $error_msg = "Image upload failed: File size exceeds the 2MB limit.";
            } else {
                // Generate a unique file name
                $new_file_name = uniqid('business_logo_', true) . '.' . $ext;
                $destination = $upload_dir . $new_file_name;

                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($file_tmp, $destination)) {
                    $new_business_logo = $new_file_name;
                    $uploadMessage = "Image uploaded successfully.";

                    // Optionally delete old logo if it's not default
                    if (!empty($business_data['business_logo']) && 
                        $business_data['business_logo'] != 'default_business.png' && 
                        file_exists($upload_dir . $business_data['business_logo'])) {
                        unlink($upload_dir . $business_data['business_logo']);
                    }
                } else {
                    $error_msg = "Image upload failed: Could not move the uploaded file. Check directory permissions.";
                    error_log("Failed to move uploaded file from $file_tmp to $destination");
                }
            }
        } else {
            $phpFileUploadErrors = array(
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload'
            );
            $error_msg = "Image upload failed: " . 
                         ($phpFileUploadErrors[$_FILES['profile_image']['error']] ?? 'Unknown error');
        }
    }

    // If no errors, proceed with updating the business profile
    if (empty($error_msg)) {
        $business_id = $business_data['business_id']; // Assuming you already have this available

        $update_query = "UPDATE businesses SET
            business_name = ?, 
            business_category = ?, 
            business_description = ?, 
            business_address = ?, 
            business_phone = ?, 
            business_logo = ?
            WHERE business_id = ?";

        if ($stmt = mysqli_prepare($link, $update_query)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $business_name, $business_category, $business_description, $business_address, $business_phone, $new_business_logo, $business_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = 'Business profile updated successfully.';
                if (!empty($uploadMessage)) {
                    $success_msg .= " " . $uploadMessage;
                }
            } else {
                $error_msg = 'Failed to update business profile.';
                error_log("Execute failed (update business profile): " . mysqli_error($link));
            }
            mysqli_stmt_close($stmt);
        }
    }
}



// Process new reward creation if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_reward"])) {
    $reward_name = trim($_POST["reward_name"]);
    $reward_description = trim($_POST["reward_description"]);
    $points_required = intval($_POST["points_required"]);
    $category_id = intval($_POST["category_id"]);
    $start_date = trim($_POST["start_date"]);
    $end_date = trim($_POST["end_date"]);
    
    // Insert new reward
    $sql = "INSERT INTO rewards (business_id, category_id, reward_name, reward_description, points_required, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "iississ", $business_data["business_id"], $category_id, $reward_name, $reward_description, $points_required, $start_date, $end_date);
        
        if (mysqli_stmt_execute($stmt)) {
            // Insert successful, refresh the page to show the new reward
            redirectWithMessage("businessprofile.php", "Reward created successfully.", "success");
        } else {
            $reward_err = "Something went wrong. Please try again later.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Handle Reward Updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_reward'])) {
    $reward_id = intval($_POST['reward_id']);
    $reward_name = mysqli_real_escape_string($link, $_POST['reward_name']);
    $reward_description = mysqli_real_escape_string($link, $_POST['reward_description']);
    $points_required = intval($_POST['points_required']);
    
    $sql = "UPDATE rewards SET 
            reward_name = ?,
            reward_description = ?,
            points_required = ?
            WHERE reward_id = ? AND business_id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssiii", 
            $reward_name,
            $reward_description,
            $points_required,
            $reward_id,
            $business_data["business_id"]
        );
        
        if (mysqli_stmt_execute($stmt)) {
            redirectWithMessage("businessprofile.php", "Reward updated successfully.", "success");
        } else {
            redirectWithMessage("businessprofile.php", "Error updating reward.", "error");
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle Deletions
function handleDelete($link, $table, $id_field, $id_value, $redirect) {
    $sql = "DELETE FROM $table WHERE $id_field = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_value);
        if (mysqli_stmt_execute($stmt)) {
            redirectWithMessage($redirect, "Item deleted successfully.", "success");
        } else {
            redirectWithMessage($redirect, "Error deleting item.", "error");
        }
        mysqli_stmt_close($stmt);
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete Reward
    if (isset($_POST['delete_reward'])) {
        handleDelete($link, 'rewards', 'reward_id', $_POST['reward_id'], 'businessprofile.php');
    }
}

require_once 'header.php';
?>

<div class="container py-5">
    <!-- Business Profile Header -->
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
            <img src="<?php echo $upload_dir . htmlspecialchars($business_data["business_logo"] ?? 'default_business.png'); ?>" 
                alt="<?php echo htmlspecialchars($business_data["business_name"]); ?>" 
                class="profile-img mb-3">
            </div>
            <div class="col-md-9">
                <h2 class="mb-2"><?php echo htmlspecialchars($business_data["business_name"] ?? 'Your Business'); ?></h2>
                <div class="activity-time">
                    <p><?php echo htmlspecialchars($business_data["business_category"] ?? 'Category'); ?></p>
                    <p>
                        <i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($business_data["business_address"] ?? "No address provided"); ?>
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </button>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createRewardModal">
                        <i class="fas fa-plus me-1"></i> Create Reward
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo count($customers); ?></h3>
                <p>Total Customers</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo count($rewards); ?></h3>
                <p>Active Rewards</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo count($recent_activity); ?></h3>
                <p>Recent Activities</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Rewards Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Rewards</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRewardModal">
                        <i class="fas fa-plus me-1"></i> Create Reward
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($rewards)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't created any rewards yet.</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createRewardModal">
                                Create Your First Reward
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reward Name</th>
                                        <th>Description</th>
                                        <th>Points Required</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rewards as $reward): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reward["reward_name"]); ?></td>
                                            <td><?php echo htmlspecialchars(substr($reward["reward_description"], 0, 50)) . (strlen($reward["reward_description"]) > 50 ? "..." : ""); ?></td>
                                            <td><?php echo $reward["points_required"]; ?></td>
                                            <td>
                                                <?php if ($reward["is_active"]): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-primary edit-reward-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editRewardModal"
                                                    data-id="<?= $reward['reward_id'] ?>"
                                                    data-name="<?= htmlspecialchars($reward['reward_name']) ?>"
                                                    data-description="<?= htmlspecialchars($reward['reward_description']) ?>"
                                                    data-points="<?= $reward['points_required'] ?>">
                                                        Edit
                                                </button>
                                                    <form method="POST" class="d-inline" 
                                                        onsubmit="return confirm('Are you sure you want to delete this reward?')">
                                                        <input type="hidden" name="reward_id" value="<?= $reward['reward_id'] ?>">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <button type="submit" name="delete_reward" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Activity Section -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_activity)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activity to display.</p>
                        </div>
                    <?php else: ?>
                        <div class="activity-feed">
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <?php if ($activity["reward_type"] == "Earned"): ?>
                                            <i class="fas fa-plus"></i>
                                        <?php else: ?>
                                            <i class="fas fa-gift"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="activity-content">
                                        <h6>
                                            <?php echo htmlspecialchars($activity["customer_first_name"] . " " . $activity["customer_last_name"]); ?>
                                            <?php if ($activity["reward_type"] == "Earned"): ?>
                                                earned <span class="text-success"><?php echo $activity["points_earned"]; ?> points</span>
                                            <?php else: ?>
                                                redeemed <span class="text-primary"><?php echo htmlspecialchars($activity["reward_name"] ?? "a reward"); ?></span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="activity-time">
                                            <?php echo date("M d, Y h:i A", strtotime($activity["reward_date"])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Business Info Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Business Information</h4>
                </div>
                <div class="card-body">
                    <!-- Business Description -->
                    <p class="mb-3">
                        <?php echo nl2br(htmlspecialchars($business_data["business_description"] ?? "No description provided.")); ?>
                    </p>
                    
                    <!-- Contact Information -->
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="fas fa-phone me-2"></i> Contact</h6>
                        <div class="activity-time">
                            <p>
                                <?php echo htmlspecialchars($business_data["business_phone"] ?? "No phone number provided."); ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Email Information -->
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="fas fa-envelope me-2"></i> Email</h6>
                        <div class="activity-time">
                            <p>
                                <?php echo htmlspecialchars($business_data["business_email"] ?? "No email provided."); ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Address Information -->
                    <div>
                        <h6 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i> Address</h6>
                        <div class="activity-time">
                            <p>
                                <?php echo nl2br(htmlspecialchars($business_data["business_address"] ?? "No address provided.")); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Customers Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recent Customers</h4>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#allCustomersModal">
                        View All
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($customers)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No customers yet.</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($customers, 0, 5) as $customer): ?>
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($customer["customer_first_name"] . " " . $customer["customer_last_name"]); ?></h6>
                                        <div class= "activity-time">
                                            <small>Joined: <?php echo date("M d, Y", strtotime($customer["joined_date"])); ?></small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo $customer["membership_points"]; ?> pts</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <?php if (count($customers) > 5): ?>
                            <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#allCustomersModal">
                                View All
                            </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($success_msg): ?>
    <p style="color:green;"><?php echo $success_msg; ?></p>
<?php endif; ?>

<?php if ($error_msg): ?>
    <p style="color:red;"><?php echo $error_msg; ?></p>
<?php endif; ?>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Business Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Business Logo -->
                    <div class="profile-img-upload mb-4 text-center">
                        <img src="<?php echo $upload_dir . htmlspecialchars($business_data['business_logo'] ?? 'default_business.jpg'); ?>" 
                             class="img-thumbnail rounded-circle mb-3"
                             id="businessLogoPreview"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="mb-3">
                            <label for="profileImageInput" class="form-label">Business Logo</label>
                            <input type="file" class="form-control" name="profile_image" id="profileImageInput" 
                                   accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">Max size: 2MB. Allowed formats: JPG, PNG, GIF</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="business_name" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" value="<?php echo htmlspecialchars($business_data["business_name"] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="business_category" class="form-label">Business Category</label>
                        <select class="form-select" id="business_category" name="business_category">
                            <option value="Food & Drinks" <?php echo ($business_data["business_category"] ?? '') == "Food & Drinks" ? "selected" : ""; ?>>Food & Drinks</option>
                            <option value="Retail" <?php echo ($business_data["business_category"] ?? '') == "Retail" ? "selected" : ""; ?>>Retail</option>
                            <option value="Services" <?php echo ($business_data["business_category"] ?? '') == "Services" ? "selected" : ""; ?>>Services</option>
                            <option value="Entertainment" <?php echo ($business_data["business_category"] ?? '') == "Entertainment" ? "selected" : ""; ?>>Entertainment</option>
                            <option value="Travel" <?php echo ($business_data["business_category"] ?? '') == "Travel" ? "selected" : ""; ?>>Travel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="business_description" class="form-label">Business Description</label>
                        <textarea class="form-control" id="business_description" name="business_description" rows="3"><?php echo htmlspecialchars($business_data["business_description"] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="business_address" class="form-label">Business Address</label>
                        <textarea class="form-control" id="business_address" name="business_address" rows="2"><?php echo htmlspecialchars($business_data["business_address"] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="business_phone" class="form-label">Business Phone</label>
                        <input type="text" class="form-control" id="business_phone" name="business_phone" value="<?php echo htmlspecialchars($business_data["business_phone"] ?? ''); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Reward Modal -->
<div class="modal fade" id="createRewardModal" tabindex="-1" aria-labelledby="createRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRewardModalLabel">Create New Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reward_name" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" id="reward_name" name="reward_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="reward_description" class="form-label">Reward Description</label>
                        <textarea class="form-control" id="reward_description" name="reward_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="points_required" class="form-label">Points Required</label>
                        <input type="number" class="form-control" id="points_required" name="points_required" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Reward Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php
                            // Get reward categories
                            $sql = "SELECT * FROM reward_categories ORDER BY category_name";
                            $result = mysqli_query($link, $sql);
                            
                            while ($category = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $category["category_id"] . '">' . htmlspecialchars($category["category_name"]) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_reward" class="btn btn-primary">Create Reward</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- All Customers Modal -->
<div class="modal fade" id="allCustomersModal" tabindex="-1" aria-labelledby="allCustomersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="allCustomersModalLabel">All Customers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (!empty($customers)): ?>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Joined Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($customers as $customer): ?>
                  <tr>
                    <td style="color: white !important;"><?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?></td>
                    <td style="color: white !important;"><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                    <td style="color: white !important;"><?php echo htmlspecialchars($customer['phone_number'] ?? 'N/A'); ?></td>
                    <td style="color: white !important;"><?php echo htmlspecialchars(date('F j, Y', strtotime($customer['joined_date']))); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No customers found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Edit Reward Modal -->
<div class="modal fade" id="editRewardModal" tabindex="-1" aria-labelledby="editRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRewardModalLabel">Edit Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="reward_id" id="editRewardId">

                    <div class="mb-3">
                        <label for="editRewardName" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" name="reward_name" id="editRewardName" required>
                    </div>

                    <div class="mb-3">
                        <label for="editRewardDescription" class="form-label">Description</label>
                        <textarea class="form-control" name="reward_description" id="editRewardDescription" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editRewardPoints" class="form-label">Points Required</label>
                        <input type="number" class="form-control" name="points_required" id="editRewardPoints" min="1" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_reward" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// Set default dates for the reward form
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const nextMonth = new Date(today);
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    document.getElementById('start_date').value = formatDate(today);
    document.getElementById('end_date').value = formatDate(nextMonth);
});

// Functions for editing and deleting rewards
function editReward(rewardId) {
    // This would typically open a modal with the reward details for editing
    alert('Edit reward with ID: ' + rewardId + ' (functionality to be implemented)');
}

function deleteReward(rewardId) {
    if (confirm('Are you sure you want to delete this reward?')) {
        // This would typically send an AJAX request to delete the reward
        alert('Delete reward with ID: ' + rewardId + ' (functionality to be implemented)');
    }
}

// Image Preview - FIXED: Changed profileImagePreview to businessLogoPreview
document.addEventListener('DOMContentLoaded', function() {
    const profileImageInput = document.getElementById('profileImageInput');
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const preview = document.getElementById('businessLogoPreview'); // Changed from profileImagePreview
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Fix for modal backdrop issues
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            const modalBackdrops = document.querySelectorAll('.modal-backdrop');
            modalBackdrops.forEach(backdrop => {
                backdrop.parentNode.removeChild(backdrop);
            });
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-reward-btn");
    
    editButtons.forEach(button => {
        button.addEventListener("click", () => {
            const rewardId = button.getAttribute("data-id");
            const rewardName = button.getAttribute("data-name");
            const rewardDescription = button.getAttribute("data-description");
            const pointsRequired = button.getAttribute("data-points");

            // Fill modal inputs
            document.getElementById("editRewardId").value = rewardId;
            document.getElementById("editRewardName").value = rewardName;
            document.getElementById("editRewardDescription").value = rewardDescription;
            document.getElementById("editRewardPoints").value = pointsRequired;
        });
    });
});


</script>

<?php require_once 'footer.php'; ?>
