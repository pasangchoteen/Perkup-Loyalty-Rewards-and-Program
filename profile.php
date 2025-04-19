<?php
$pageTitle = "My Profile";
$currentPage = "profile";
require_once 'header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Please log in to view your profile', 'error');
}

// Get user data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $bio = sanitizeInput($_POST['bio']);
    
    // Update profile image if uploaded
    $profileImage = $user['profile_image'];
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;
        
        // Check if file is an image
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $validExtensions)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                $profileImage = $uploadFile;
            }
        }
    }
    
    // Update user data
    $updateStmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, bio = ?, profile_image = ? WHERE id = ?");
    $updateStmt->bind_param("ssssi", $firstName, $lastName, $bio, $profileImage, $userId);
    
    if ($updateStmt->execute()) {
        redirectWithMessage('profile.php', 'Profile updated successfully');
    } else {
        redirectWithMessage('profile.php', 'Error updating profile: ' . $conn->error, 'error');
    }
    
    $updateStmt->close();
}

// Get user activity (example: recent logins)
$activityStmt = $conn->prepare("
    SELECT 'login' as activity_type, created_at 
    FROM user_logins 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Create user_logins table if it doesn't exist
$conn->query("
    CREATE TABLE IF NOT EXISTS user_logins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

// Insert current login if not already recorded
$currentTime = date('Y-m-d H:i:s');
$ipAddress = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$loginStmt = $conn->prepare("
    INSERT INTO user_logins (user_id, ip_address, user_agent) 
    SELECT ?, ?, ? 
    WHERE NOT EXISTS (
        SELECT 1 FROM user_logins 
        WHERE user_id = ? AND ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    )
");
$loginStmt->bind_param("issss", $userId, $ipAddress, $userAgent, $userId, $ipAddress);
$loginStmt->execute();
$loginStmt->close();

// Get user activity
$activityStmt->bind_param("i", $userId);
$activityStmt->execute();
$activityResult = $activityStmt->get_result();
$activities = [];
while ($activity = $activityResult->fetch_assoc()) {
    $activities[] = $activity;
}
$activityStmt->close();
?>

<!-- Profile Header -->
<section class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'img/default-profile.jpg'; ?>" alt="Profile Image" class="profile-img">
            </div>
            <div class="col-md-9">
                <h1 class="mb-2"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p class="lead mb-0">@<?php echo htmlspecialchars($user['username']); ?></p>
                <p class="text-white-50"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Left Column - Profile Info -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Profile Information</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <p class="mb-0 text-muted">Full Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <p class="mb-0 text-muted">Username</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0"><?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <p class="mb-0 text-muted">Email</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <p class="mb-0 text-muted">Member Since</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0 text-muted">Bio</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0"><?php echo !empty($user['bio']) ? htmlspecialchars($user['bio']) : 'No bio provided'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Account Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2"></i> Change Password
                            </button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="fas fa-trash-alt me-2"></i> Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Activity & Stats -->
            <div class="col-lg-4">
                <!-- User Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Account Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <h6 class="text-muted">Login Count</h6>
                                <h4><?php echo count($activities); ?></h4>
                            </div>
                            <div class="col-6 mb-3">
                                <h6 class="text-muted">Days Active</h6>
                                <h4><?php echo floor((time() - strtotime($user['created_at'])) / (60 * 60 * 24)); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($activities) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($activities as $activity): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-sign-in-alt text-primary me-2"></i>
                                            <span>Login</span>
                                        </div>
                                        <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center text-muted">No recent activity</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                        <div class="form-text">Upload a new profile image (JPG, PNG, or GIF).</div>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
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

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="change-password.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn  data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="delete-account.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Warning: This action cannot be undone. All your data will be permanently deleted.
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

