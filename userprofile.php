<?php
$pageTitle = "My Profile";
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

// Check if user is logged in and is a customer
if (!isCustomer()) {
    redirectWithMessage("login.php", "You must be logged in as a customer to access this page.", "error");
    exit;
}

// Ensure session is started if not already done in config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables and arrays
$customer_data = [];
$rewards_history = [];
$referrals = [];
$businesses = [];
$update_err = $update_success = '';

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
$upload_dir = 'uploads/';
$max_file_size = 2 * 1024 * 1024; // 2MB

// CSRF Token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get customer data - UPDATED TO INCLUDE CREATED_AT
$sql = "SELECT c.*, u.username, u.email, u.created_at 
        FROM customers c 
        JOIN users u ON c.customer_email = u.email 
        WHERE c.customer_email = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["email"]);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $customer_data = $row;
        } else {
            redirectWithMessage("login.php", "Customer record not found.", "error");
            exit;
        }
    } else {
        error_log("Execute failed: " . mysqli_error($link));
        redirectWithMessage("error.php", "An error occurred while processing your request.", "error");
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Reward history
$sql = "SELECT rh.*, r.reward_name, b.business_name 
        FROM reward_history rh 
        LEFT JOIN rewards r ON rh.reward_id = r.reward_id 
        LEFT JOIN businesses b ON r.business_id = b.business_id 
        WHERE rh.customer_id = ? 
        ORDER BY rh.reward_date DESC 
        LIMIT 10";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_data["customer_id"]);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $rewards_history[] = $row;
        }
    } else {
        error_log("Execute failed (reward history): " . mysqli_error($link));
    }
    mysqli_stmt_close($stmt);
}

// Referrals
$sql = "SELECT r.*, c.customer_first_name, c.customer_last_name 
        FROM referrals r 
        JOIN customers c ON r.referred_id = c.customer_id 
        WHERE r.referrer_id = ? 
        ORDER BY r.referral_date DESC";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_data["customer_id"]);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $referrals[] = $row;
        }
    } else {
        error_log("Execute failed (referrals): " . mysqli_error($link));
    }
    mysqli_stmt_close($stmt);
}

// Businesses joined
$sql = "SELECT b.*, ub.joined_date 
        FROM user_businesses ub 
        JOIN businesses b ON ub.business_id = b.business_id 
        WHERE ub.customer_id = ? 
        ORDER BY ub.joined_date DESC";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_data["customer_id"]);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $businesses[] = $row;
        }
    } else {
        error_log("Execute failed (businesses): " . mysqli_error($link));
    }
    mysqli_stmt_close($stmt);
}

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        redirectWithMessage("userprofile.php", "Invalid CSRF token.", "error");
        exit;
    }
    
    // Validate and trim inputs
    $first_name   = trim($_POST["first_name"]);
    $last_name    = trim($_POST["last_name"]);
    $phone_number = trim($_POST["phone_number"]);
    $address      = trim($_POST["address"]);
    
    // Optional: Validate phone number format (adjust regex as needed)
    if (!empty($phone_number) && !preg_match("/^\+?[0-9]{10,15}$/", $phone_number)) {
        $update_err = "Invalid phone number format.";
    }
    
    // Set default message for image upload status
    $uploadMessage = "";
    // Assume current profile image remains unchanged unless a new file is uploaded
    $new_profile_image = $customer_data['profile_image'];

    // Process image upload if a file was provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $file_tmp   = $_FILES['profile_image']['tmp_name'];
            $file_name  = basename($_FILES['profile_image']['name']);
            $file_size  = $_FILES['profile_image']['size'];
            $file_type  = $_FILES['profile_image']['type']; // Use the reported MIME type
            
            // Get file extension
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type and size
            if (!in_array($ext, $allowed_extensions)) {
                $update_err = "Image upload failed: Invalid file extension. Allowed types are JPG, PNG, and GIF.";
            } elseif ($file_size > $max_file_size) {
                $update_err = "Image upload failed: File size exceeds the 2MB limit.";
            } else {
                // Generate a unique filename
                $new_file_name = uniqid('profile_', true) . '.' . $ext;
                $destination = $upload_dir . $new_file_name;

    
                $uploadFile = $uploadDir . basename($_FILES['profile_image']['name']);
                
                // Make sure upload directory exists with proper permissions
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Move the uploaded file to the destination directory
                if (move_uploaded_file($file_tmp, $destination)) {
                    $new_profile_image = $new_file_name;
                    $uploadMessage = "Image uploaded successfully.";
                    
                    // Optionally: Delete the old image if it exists and is not the default image
                    if (!empty($customer_data['profile_image']) && 
                        $customer_data['profile_image'] != 'default.png' && 
                        file_exists($upload_dir . $customer_data['profile_image'])) {
                        unlink($upload_dir . $customer_data['profile_image']);
                    }
                } else {
                    $update_err = "Image upload failed: Could not move the uploaded file. Check directory permissions.";
                    error_log("Failed to move uploaded file from $file_tmp to $destination");
                }
            }
        } else {
            // Map upload error codes to messages
            $phpFileUploadErrors = array(
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload'
            );
            $update_err = "Image upload failed: " . 
                         ($phpFileUploadErrors[$_FILES['profile_image']['error']] ?? 'Unknown error');
        }
    }
    
    // Proceed with database update if no validation errors have occurred
    if (empty($update_err)) {
        // Update SQL now includes the profile_image field
        $sql = "UPDATE customers 
                SET customer_first_name = ?, customer_last_name = ?, phone_number = ?, address = ?, profile_image = ?
                WHERE customer_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $phone_number, $address, $new_profile_image, $customer_data["customer_id"]);
            if (mysqli_stmt_execute($stmt)) {
                // Prepare a combined success message
                $message = "Profile updated successfully.";
                if (!empty($uploadMessage)) {
                    $message .= " " . $uploadMessage;
                }
                redirectWithMessage("userprofile.php", $message, "success");
                exit;
            } else {
                error_log("Execute failed (update profile): " . mysqli_error($link));
                $update_err = "An error occurred while updating your profile.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // If there are any errors, you may want to display $update_err (and optionally $uploadMessage) on the page.
}

// Group rewards by business_id
$rewards_by_business = [];
$sql = "SELECT * FROM rewards WHERE is_active = 1";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rewards_by_business[$row['business_id']][] = $row;
    }
} else {
    error_log("Query failed (group rewards): " . mysqli_error($link));
}

require_once 'header.php';
?>

<div class="container py-5">
    <!-- Profile Header -->
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?php echo $upload_dir . htmlspecialchars($customer_data["profile_image"] ?? 'default.png'); ?>" 
                     alt="Profile Image" class="profile-img mb-3">
            </div>
            <div class="col-md-9">
                <h2 class="mb-2"><?php echo htmlspecialchars($customer_data["customer_first_name"] . " " . $customer_data["customer_last_name"]); ?></h2>
                <div class="activity-time">
                    <p><?php echo htmlspecialchars($customer_data["username"]); ?> | <?php echo htmlspecialchars($customer_data["email"]); ?></p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($customer_data["membership_status"]); ?> Member</span>
                    <div class="activity-time">
                        <span>Member since <?php echo date("M d, Y", strtotime($customer_data["created_at"] ?? 'now')); ?></span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </button>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#referralModal">
                        <i class="fas fa-share-alt me-1"></i> Share Referral Code
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo number_format($customer_data["membership_points"]); ?></h3>
                <p>Total Points</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo count($businesses); ?></h3>
                <p>Businesses Joined</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo count($referrals); ?></h3>
                <p>Successful Referrals</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Businesses Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Businesses</h4>
                    <a href="businesses.php" class="btn btn-sm btn-outline-primary">Find More</a>
                </div>
                <div class="card-body">
                    <?php if (empty($businesses)): ?>
                        <p>You haven't joined any businesses yet.</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($businesses as $business): ?>
                                <!-- Business Card -->
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="<?php echo $upload_dir . htmlspecialchars($business["business_logo"] ?? 'default_business.png'); ?>" 
                                                     alt="<?php echo htmlspecialchars($business["business_name"]); ?>" 
                                                     class="me-3 rounded-circle" 
                                                     width="50" 
                                                     height="50">
                                                <div>
                                                    <h5 class="mb-0"><?php echo htmlspecialchars($business["business_name"]); ?></h5>
                                                    <div class="activity-time">
                                                        <small><?php echo htmlspecialchars($business["business_category"]); ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="activity-time">
                                                <p><?php echo htmlspecialchars(substr($business["business_description"], 0, 100)) . 
                                                   (strlen($business["business_description"]) > 100 ? "..." : ""); ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Joined: <?php echo date("M d, Y", strtotime($business["joined_date"])); ?></span>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary mt-2" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rewardsModal_<?php echo $business['business_id']; ?>">
                                                    View Rewards
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Reward History Section -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Reward History</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($rewards_history)): ?>
                        <p>No reward history yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Business</th>
                                        <th>Type</th>
                                        <th>Reward</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rewards_history as $history): ?>
                                        <tr>
                                            <td><?php echo date("M d, Y", strtotime($history["reward_date"])); ?></td>
                                            <td><?php echo htmlspecialchars($history["business_name"] ?? "N/A"); ?></td>
                                            <td>
                                                <?php if ($history["reward_type"] == "Earned"): ?>
                                                    <span class="badge bg-success">Earned</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Redeemed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($history["reward_name"] ?? "Points"); ?></td>
                                            <td>
                                                <?php if ($history["reward_type"] == "Earned"): ?>
                                                    <span class="text-success">+<?php echo $history["points_earned"]; ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger">-<?php echo $history["points_redeemed"]; ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Referral Code Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">My Referral Code</h4>
                </div>
                <div class="card-body text-center">
                    <div class="referral-code-display mb-3">
                        <h2 class="text-accent"><?php echo htmlspecialchars($customer_data["referral_code"]); ?></h2>
                    </div>
                    <p>Share this code with friends and earn rewards when they sign up!</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#referralModal">
                        <i class="fas fa-share-alt me-1"></i> Share Code
                    </button>
                </div>
            </div>
            
            <!-- Referrals Section -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">My Referrals</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($referrals)): ?>
                        <p>You haven't referred anyone yet.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($referrals as $referral): ?>
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($referral["customer_first_name"] . " " . $referral["customer_last_name"]); ?></h6>
                                        <small class="text-muted"><?php echo date("M d, Y", strtotime($referral["referral_date"])); ?></small>
                                    </div>
                                    <?php if ($referral["status"] == "Completed"): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display any error messages -->
                    <?php if (!empty($update_err)): ?>
                        <div class="alert alert-danger"><?php echo $update_err; ?></div>
                    <?php endif; ?>
                    
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <!-- Profile Image -->
                    <div class="profile-img-upload mb-4 text-center">
                        <img src="<?php echo $upload_dir . htmlspecialchars($customer_data['profile_image'] ?? 'default.png'); ?>" 
                             class="img-thumbnail rounded-circle mb-3"
                             id="profileImagePreview"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="mb-3">
                            <label for="profileImageInput" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="profile_image" id="profileImageInput" 
                                   accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">Max size: 2MB. Allowed formats: JPG, PNG, GIF</div>
                        </div>
                    </div>

                    <!-- First Name -->
                    <div class="mb-3">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($customer_data['customer_first_name']); ?>" required>
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($customer_data['customer_last_name']); ?>" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label class="form-label" for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" 
                               value="<?php echo htmlspecialchars($customer_data['phone_number'] ?? ''); ?>">
                        <div class="form-text">Format: 10-15 digits, can include + prefix</div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php 
                            echo htmlspecialchars(trim($customer_data['address'] ?? '')); 
                        ?></textarea>
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

<!-- Referral Modal -->
<div class="modal fade" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="referralModalLabel">Share Your Referral Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h2 class="text-accent"><?php echo htmlspecialchars($customer_data["referral_code"]); ?></h2>
                    <p class="text-muted">Share this code with friends and earn rewards when they sign up!</p>
                </div>
                
                <div class="mb-3">
                    <label for="referral-link" class="form-label">Referral Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="referral-link" value="<?php echo SITE_URL; ?>/signup.php?ref=<?php echo $customer_data["referral_code"]; ?>" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('referral-link')">Copy</button>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/signup.php?ref=' . $customer_data["referral_code"]); ?>" target="_blank" class="btn btn-outline-primary">
                        <i class="fab fa-facebook-f me-2"></i> Share on Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Join PerkUp and earn rewards! Use my referral code: ' . $customer_data["referral_code"]); ?>&url=<?php echo urlencode(SITE_URL . '/signup.php?ref=' . $customer_data["referral_code"]); ?>" target="_blank" class="btn btn-outline-primary">
                        <i class="fab fa-twitter me-2"></i> Share on Twitter
                    </a>
                    <a href="mailto:?subject=<?php echo urlencode('Join PerkUp Rewards Program'); ?>&body=<?php echo urlencode('Hey! I thought you might be interested in PerkUp, a rewards program for local businesses. Use my referral code ' . $customer_data["referral_code"] . ' when you sign up: ' . SITE_URL . '/signup.php?ref=' . $customer_data["referral_code"]); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i> Share via Email
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rewards Modals - Each business gets its own modal -->
<?php foreach ($businesses as $business): ?>
    <div class="modal fade" id="rewardsModal_<?php echo $business['business_id']; ?>" 
         tabindex="-1" 
         aria-labelledby="rewardsModalLabel_<?php echo $business['business_id']; ?>" 
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rewardsModalLabel_<?php echo $business['business_id']; ?>">
                        <?php echo htmlspecialchars($business["business_name"]); ?> Rewards
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($rewards_by_business[$business['business_id']])): ?>
                        <div class="row g-3">
                            <?php foreach ($rewards_by_business[$business['business_id']] as $reward): ?>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5><?php echo htmlspecialchars($reward['reward_name']); ?></h5>
                                            <p><?php echo htmlspecialchars($reward['reward_description']); ?></p>
                                            <div class="mt-2">
                                                <span class="badge bg-success">
                                                    <?php echo number_format($reward['points_required']); ?> Points
                                                </span>
                                                <?php if (isset($reward['is_redeemable']) && $reward['is_redeemable']): ?>
                                                    <button class="btn btn-sm btn-primary float-end"
                                                            onclick="redeemReward(<?php echo $reward['reward_id']; ?>)">
                                                        Redeem
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No active rewards available for this business.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    
    // Show a temporary "Copied!" message
    const button = element.nextElementSibling;
    const originalText = button.innerHTML;
    button.innerHTML = 'Copied!';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}

// Image Preview
document.addEventListener('DOMContentLoaded', function() {
    const profileImageInput = document.getElementById('profileImageInput');
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const preview = document.getElementById('profileImagePreview');
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

// Reward Redemption
function redeemReward(rewardId) {
    if (confirm('Are you sure you want to redeem this reward?')) {
        fetch(`redeem.php?reward_id=${rewardId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reward redeemed successfully!');
                    location.reload();
                } else {
                    alert(data.error || 'Error redeeming reward');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
    }
}
</script>

<?php require_once 'footer.php'; ?>


