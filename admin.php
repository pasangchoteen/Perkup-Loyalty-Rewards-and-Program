<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set page title and include config
$pageTitle = "Admin Dashboard";
$currentPage = "admin";
$extraCSS = "css/admin.css";

// Include config file first
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin - using the function from config.php
if (!isAdmin()) {
    redirectWithMessage("login.php", "You must be logged in as an administrator to access this page.", "error");
    exit;
}

// CSRF Token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$stats = [
    'total_users' => 0,
    'total_businesses' => 0,
    'total_rewards' => 0,
    'total_points' => 0
];
$users = [];
$businesses = [];
$rewards = [];
$customers = [];
$action_success = $action_error = '';

// Test database connection
// if (!$link) {
//     die("Database connection failed: " . mysqli_connect_error());
// }

// Get basic statistics with error handling
try {
    // Total users
    $sql = "SELECT COUNT(*) as count FROM users";
    if ($result = mysqli_query($link, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $stats['total_users'] = $row['count'];
        }
        mysqli_free_result($result);
    } else {
        echo "Error in query: " . mysqli_error($link) . "<br>";
    }

    // Total businesses
    $sql = "SELECT COUNT(*) as count FROM businesses";
    if ($result = mysqli_query($link, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $stats['total_businesses'] = $row['count'];
        }
        mysqli_free_result($result);
    } else {
        echo "Error in query: " . mysqli_error($link) . "<br>";
    }

    // Total rewards
    $sql = "SELECT COUNT(*) as count FROM rewards";
    if ($result = mysqli_query($link, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $stats['total_rewards'] = $row['count'];
        }
        mysqli_free_result($result);
    } else {
        echo "Error in query: " . mysqli_error($link) . "<br>";
    }

    // Total points
    $sql = "SELECT SUM(membership_points) as total_points FROM customers";
    if ($result = mysqli_query($link, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $stats['total_points'] = $row['total_points'] ?? 0;
        }
        mysqli_free_result($result);
    } else {
        echo "Error in query: " . mysqli_error($link) . "<br>";
    }

    // Get users (simplified query)
    $sql = "SELECT u.*, 
            CASE 
                WHEN u.user_type = 'customer' THEN CONCAT(c.customer_first_name, ' ', c.customer_last_name)
                WHEN u.user_type = 'business' THEN b.business_name
                ELSE NULL 
            END as name
            FROM users u
            LEFT JOIN customers c ON u.email = c.customer_email
            LEFT JOIN businesses b ON u.email = b.business_email
            ORDER BY u.created_at DESC";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_free_result($result);
    } else {
        echo "Error in users query: " . mysqli_error($link) . "<br>";
    }

    // Get customers
    $sql = "SELECT c.*, u.created_at 
            FROM customers c 
            JOIN users u ON c.customer_email = u.email 
            ORDER BY c.customer_first_name, c.customer_last_name";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
        mysqli_free_result($result);
    } else {
        echo "Error in customers query: " . mysqli_error($link) . "<br>";
    }

    // Get businesses (simplified query)
    $sql = "SELECT * FROM businesses ORDER BY business_name";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $businesses[] = $row;
        }
        mysqli_free_result($result);
    } else {
        echo "Error in businesses query: " . mysqli_error($link) . "<br>";
    }

    // Get rewards
    $sql = "SELECT r.*, b.business_name 
            FROM rewards r
            JOIN businesses b ON r.business_id = b.business_id
            ORDER BY r.business_id, r.reward_name";
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rewards[] = $row;
        }
        mysqli_free_result($result);
    } else {
        echo "Error in rewards query: " . mysqli_error($link) . "<br>";
    }

} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Process actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $action_error = "Invalid CSRF token.";
    } else {
        // Handle different actions
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'delete_user':
                    if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
                        $user_id = $_POST['user_id'];
                        $sql = "DELETE FROM users WHERE id = ?";
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "i", $user_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $action_success = "User deleted successfully.";
                            } else {
                                $action_error = "Error deleting user: " . mysqli_error($link);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    break;
                
                case 'delete_business':
                    if (isset($_POST['business_id']) && is_numeric($_POST['business_id'])) {
                        $business_id = $_POST['business_id'];
                        $sql = "DELETE FROM businesses WHERE business_id = ?";
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "i", $business_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $action_success = "Business deleted successfully.";
                            } else {
                                $action_error = "Error deleting business: " . mysqli_error($link);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    break;
                
                case 'update_business':
                    if (isset($_POST['business_id']) && is_numeric($_POST['business_id'])) {
                        $business_id = $_POST['business_id'];
                        $business_name = trim($_POST['business_name']);
                        $business_description = trim($_POST['business_description']);
                        $business_category = trim($_POST['business_category']);
                        
                        $sql = "UPDATE businesses SET 
                                business_name = ?, 
                                business_description = ?, 
                                business_category = ? 
                                WHERE business_id = ?";
                        
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "sssi", $business_name, $business_description, $business_category, $business_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $action_success = "Business updated successfully.";
                            } else {
                                $action_error = "Error updating business: " . mysqli_error($link);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    break;
                
                case 'add_business':
                    $business_name = trim($_POST['business_name']);
                    $business_description = trim($_POST['business_description']);
                    $business_category = trim($_POST['business_category']);
                    $business_email = trim($_POST['business_email']);
                    
                    // First, create a user for the business
                    $username = strtolower(str_replace(' ', '', $business_name));
                    $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
                    
                    $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'business')";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "sss", $username, $business_email, $password);
                        if (mysqli_stmt_execute($stmt)) {
                            // Now create the business
                            $sql = "INSERT INTO businesses (business_email, business_name, business_description, business_category) 
                                    VALUES (?, ?, ?, ?)";
                            
                            if ($stmt2 = mysqli_prepare($link, $sql)) {
                                mysqli_stmt_bind_param($stmt2, "ssss", $business_email, $business_name, $business_description, $business_category);
                                if (mysqli_stmt_execute($stmt2)) {
                                    $action_success = "Business added successfully.";
                                } else {
                                    $action_error = "Error adding business: " . mysqli_error($link);
                                }
                                mysqli_stmt_close($stmt2);
                            }
                        } else {
                            $action_error = "Error creating user for business: " . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt);
                    }
                    break;
                
                case 'update_reward':
                    if (isset($_POST['reward_id']) && is_numeric($_POST['reward_id'])) {
                        $reward_id = $_POST['reward_id'];
                        $reward_name = trim($_POST['reward_name']);
                        $reward_description = trim($_POST['reward_description']);
                        $points_required = (int)$_POST['points_required'];
                        $business_id = (int)$_POST['business_id'];
                        $is_active = isset($_POST['is_active']) ? 1 : 0;
                        
                        $sql = "UPDATE rewards SET 
                                reward_name = ?, 
                                reward_description = ?, 
                                points_required = ?, 
                                business_id = ?,
                                is_active = ? 
                                WHERE reward_id = ?";
                        
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ssiiii", $reward_name, $reward_description, $points_required, $business_id, $is_active, $reward_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $action_success = "Reward updated successfully.";
                            } else {
                                $action_error = "Error updating reward: " . mysqli_error($link);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    break;
                
                case 'add_reward':
                    $reward_name = trim($_POST['reward_name']);
                    $reward_description = trim($_POST['reward_description']);
                    $points_required = (int)$_POST['points_required'];
                    $business_id = (int)$_POST['business_id'];
                    $is_active = isset($_POST['is_active']) ? 1 : 0;
                    
                    $sql = "INSERT INTO rewards (reward_name, reward_description, points_required, business_id, is_active) 
                            VALUES (?, ?, ?, ?, ?)";
                    
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ssiii", $reward_name, $reward_description, $points_required, $business_id, $is_active);
                        if (mysqli_stmt_execute($stmt)) {
                            $action_success = "Reward added successfully.";
                        } else {
                            $action_error = "Error adding reward: " . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt);
                    }
                    break;
                
                case 'update_user':
                    if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
                        $user_id = $_POST['user_id'];
                        $username = trim($_POST['username']);
                        $email = trim($_POST['email']);
                        $user_type = trim($_POST['user_type']);
                        
                        $sql = "UPDATE users SET 
                                username = ?, 
                                email = ?, 
                                user_type = ? 
                                WHERE id = ?";
                        
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $user_type, $user_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $action_success = "User updated successfully.";
                            } else {
                                $action_error = "Error updating user: " . mysqli_error($link);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                    break;
                
                case 'add_user':
                    $username = trim($_POST['username']);
                    $email = trim($_POST['email']);
                    $password = trim($_POST['password']);
                    $user_type = trim($_POST['user_type']);
                    
                    // Check if username or email already exists
                    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_store_result($stmt);
                            if (mysqli_stmt_num_rows($stmt) > 0) {
                                $action_error = "Username or email already exists.";
                            } else {
                                // Hash the password
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                
                                // Insert the new user
                                $sql = "INSERT INTO users (username, email, password, user_type) 
                                        VALUES (?, ?, ?, ?)";
                                
                                if ($stmt2 = mysqli_prepare($link, $sql)) {
                                    mysqli_stmt_bind_param($stmt2, "ssss", $username, $email, $hashed_password, $user_type);
                                    if (mysqli_stmt_execute($stmt2)) {
                                        $action_success = "User added successfully.";
                                        
                                        // If user type is customer, create a customer record
                                        if ($user_type == 'customer') {
                                            $referral_code = strtoupper(substr($username, 0, 2) . rand(100, 999));
                                            $sql = "INSERT INTO customers (customer_email, customer_first_name, customer_last_name, referral_code) 
                                                    VALUES (?, 'New', 'Customer', ?)";
                                            
                                            if ($stmt3 = mysqli_prepare($link, $sql)) {
                                                mysqli_stmt_bind_param($stmt3, "ss", $email, $referral_code);
                                                mysqli_stmt_execute($stmt3);
                                                mysqli_stmt_close($stmt3);
                                            }
                                        }
                                    } else {
                                        $action_error = "Error adding user: " . mysqli_error($link);
                                    }
                                    mysqli_stmt_close($stmt2);
                                }
                            }
                        } else {
                            $action_error = "Error checking existing user: " . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt);
                    }
                    break;
                
                default:
                    $action_error = "Unknown action.";
                    break;
            }
        }
    }
}

// Include header
require_once 'header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Admin Sidebar -->
            <div class="admin-sidebar">
                <h4 class="admin-card-title">Admin Dashboard</h4>
                <ul class="nav flex-column admin-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#users" data-bs-toggle="tab">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#customers" data-bs-toggle="tab">
                            <i class="fas fa-user-friends me-2"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#businesses" data-bs-toggle="tab">
                            <i class="fas fa-store me-2"></i> Businesses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#rewards" data-bs-toggle="tab">
                            <i class="fas fa-gift me-2"></i> Rewards
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#settings" data-bs-toggle="tab">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Alert Messages -->
            <?php if (!empty($action_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $action_success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($action_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $action_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="dashboard">
                    <div class="admin-card">
                        <h2 class="admin-card-title">System Overview</h2>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="stats-card bg-primary text-white">
                                    <div class="stats-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3><?php echo number_format($stats['total_users']); ?></h3>
                                        <p>Total Users</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-success text-white">
                                    <div class="stats-icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3><?php echo number_format($stats['total_businesses']); ?></h3>
                                        <p>Businesses</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-info text-white">
                                    <div class="stats-icon">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3><?php echo number_format($stats['total_rewards']); ?></h3>
                                        <p>Rewards</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-warning text-white">
                                    <div class="stats-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="stats-info">
                                        <h3><?php echo number_format($stats['total_points']); ?></h3>
                                        <p>Total Points</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="admin-card">
                                    <h4 class="admin-card-title">Recent Users</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Type</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $count = 0;
                                                foreach ($users as $user): 
                                                    if ($count >= 5) break;
                                                    $count++;
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $user['user_type'] == 'admin' ? 'danger' : ($user['user_type'] == 'business' ? 'success' : 'primary'); ?>">
                                                                <?php echo ucfirst(htmlspecialchars($user['user_type'])); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="#users" class="btn btn-sm btn-outline-primary" data-bs-toggle="tab">View All Users</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="admin-card">
                                    <h4 class="admin-card-title">Recent Businesses</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Business Name</th>
                                                    <th>Category</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $count = 0;
                                                foreach ($businesses as $business): 
                                                    if ($count >= 5) break;
                                                    $count++;
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($business['business_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($business['business_category']); ?></td>
                                                        <td><?php echo htmlspecialchars($business['business_email']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="#businesses" class="btn btn-sm btn-outline-primary" data-bs-toggle="tab">View All Businesses</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Users Tab -->
                <div class="tab-pane fade" id="users">
                    <div class="admin-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="admin-card-title">User Management</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-2"></i> Add User
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['user_type'] == 'admin' ? 'danger' : ($user['user_type'] == 'business' ? 'success' : 'primary'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($user['user_type'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary edit-user-btn" 
                                                            data-user-id="<?php echo $user['id']; ?>"
                                                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                            data-user-type="<?php echo htmlspecialchars($user['user_type']); ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editUserModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-user-btn"
                                                            data-user-id="<?php echo $user['id']; ?>"
                                                            data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Customers Tab -->
                <div class="tab-pane fade" id="customers">
                    <div class="admin-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="admin-card-title">Customer Management</h2>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Tier</th>
                                        <th>Email</th>
                                        <th>Points</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><?php echo $customer['customer_id']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($customer['customer_first_name'] . ' ' . $customer['customer_last_name']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $customer['membership_status'] == 'Gold' ? 'warning' : ($customer['membership_status'] == 'Platinum' ? 'info' : 'primary'); ?>">
                                                    <?php echo htmlspecialchars($customer['membership_status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                                            <td><?php echo number_format($customer['membership_points']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($customer['created_at'] ?? 'now')); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <!-- Placeholder edit button -->
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            data-customer-id="<?php echo $customer['customer_id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Placeholder delete button -->
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            data-customer-id="<?php echo $customer['customer_id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Businesses Tab -->
                <div class="tab-pane fade" id="businesses">
                    <div class="admin-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="admin-card-title">Business Management</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
                                <i class="fas fa-plus me-2"></i> Add Business
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($businesses as $business): ?>
                                        <tr>
                                            <td><?php echo $business['business_id']; ?></td>
                                            <td><?php echo htmlspecialchars($business['business_name']); ?></td>
                                            <td><?php echo htmlspecialchars($business['business_category']); ?></td>
                                            <td><?php echo htmlspecialchars($business['business_email']); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary edit-business-btn" 
                                                            data-business-id="<?php echo $business['business_id']; ?>"
                                                            data-business-name="<?php echo htmlspecialchars($business['business_name']); ?>"
                                                            data-business-description="<?php echo htmlspecialchars($business['business_description']); ?>"
                                                            data-business-category="<?php echo htmlspecialchars($business['business_category']); ?>"
                                                            data-business-email="<?php echo htmlspecialchars($business['business_email']); ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editBusinessModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-business-btn"
                                                            data-business-id="<?php echo $business['business_id']; ?>"
                                                            data-business-name="<?php echo htmlspecialchars($business['business_name']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Rewards Tab -->
                <div class="tab-pane fade" id="rewards">
                    <div class="admin-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="admin-card-title">Reward Management</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRewardModal">
                                <i class="fas fa-plus me-2"></i> Add Reward
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Business</th>
                                        <th>Points</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rewards as $reward): ?>
                                        <tr>
                                            <td><?php echo $reward['reward_id']; ?></td>
                                            <td><?php echo htmlspecialchars($reward['reward_name']); ?></td>
                                            <td><?php echo htmlspecialchars($reward['business_name']); ?></td>
                                            <td><?php echo number_format($reward['points_required']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $reward['is_active'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $reward['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary edit-reward-btn" 
                                                            data-reward-id="<?php echo $reward['reward_id']; ?>"
                                                            data-reward-name="<?php echo htmlspecialchars($reward['reward_name']); ?>"
                                                            data-reward-description="<?php echo htmlspecialchars($reward['reward_description']); ?>"
                                                            data-points-required="<?php echo $reward['points_required']; ?>"
                                                            data-business-id="<?php echo $reward['business_id']; ?>"
                                                            data-is-active="<?php echo $reward['is_active']; ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editRewardModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-reward-btn"
                                                            data-reward-id="<?php echo $reward['reward_id']; ?>"
                                                            data-reward-name="<?php echo htmlspecialchars($reward['reward_name']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    <div class="admin-card">
                        <h2 class="admin-card-title">System Settings</h2>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="PerkUp">
                            </div>
                            
                            <div class="mb-3">
                                <label for="points_per_dollar" class="form-label">Points Per Dollar</label>
                                <input type="number" class="form-control" id="points_per_dollar" name="points_per_dollar" value="10">
                            </div>
                            
                            <div class="mb-3">
                                <label for="referral_points" class="form-label">Referral Points</label>
                                <input type="number" class="form-control" id="referral_points" name="referral_points" value="100">
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="enable_registrations" name="enable_registrations" checked>
                                <label class="form-check-label" for="enable_registrations">Enable User Registrations</label>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="enable_business_registrations" name="enable_business_registrations" checked>
                                <label class="form-check-label" for="enable_business_registrations">Enable Business Registrations</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="add_user">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user_type" class="form-label">User Type</label>
                        <select class="form-select" id="user_type" name="user_type" required>
                            <option value="customer">Customer</option>
                            <option value="business">Business</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_user_type" class="form-label">User Type</label>
                        <select class="form-select" id="edit_user_type" name="user_type" required>
                            <option value="customer">Customer</option>
                            <option value="business">Business</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Business Modal -->
<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="add_business">
                    
                    <div class="mb-3">
                        <label for="business_name" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_email" class="form-label">Business Email</label>
                        <input type="email" class="form-control" id="business_email" name="business_email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="business_category" name="business_category" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_description" class="form-label">Description</label>
                        <textarea class="form-control" id="business_description" name="business_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Business</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Business Modal -->
<div class="modal fade" id="editBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="update_business">
                    <input type="hidden" id="edit_business_id" name="business_id">
                    
                    <div class="mb-3">
                        <label for="edit_business_name" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="edit_business_name" name="business_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_business_category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="edit_business_category" name="business_category" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_business_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_business_description" name="business_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Reward Modal -->
<div class="modal fade" id="addRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="add_reward">
                    
                    <div class="mb-3">
                        <label for="reward_name" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" id="reward_name" name="reward_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reward_description" class="form-label">Description</label>
                        <textarea class="form-control" id="reward_description" name="reward_description" rows="3"></textarea>
                      id="reward_description" name="reward_description" rows="3"></textarea>
                    
                    <div class="mb-3">
                        <label for="points_required" class="form-label">Points Required</label>
                        <input type="number" class="form-control" id="points_required" name="points_required" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_id" class="form-label">Business</label>
                        <select class="form-select" id="business_id" name="business_id" required>
                            <?php foreach ($businesses as $business): ?>
                                <option value="<?php echo $business['business_id']; ?>">
                                    <?php echo htmlspecialchars($business['business_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="reward_is_active" name="is_active" checked>
                        <label class="form-check-label" for="reward_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Reward</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Reward Modal -->
<div class="modal fade" id="editRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="update_reward">
                    <input type="hidden" id="edit_reward_id" name="reward_id">
                    
                    <div class="mb-3">
                        <label for="edit_reward_name" class="form-label">Reward Name</label>
                        <input type="text" class="form-control" id="edit_reward_name" name="reward_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_reward_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_reward_description" name="reward_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_points_required" class="form-label">Points Required</label>
                        <input type="number" class="form-control" id="edit_points_required" name="points_required" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_business_id" class="form-label">Business</label>
                        <select class="form-select" id="edit_business_id" name="business_id" required>
                            <?php foreach ($businesses as $business): ?>
                                <option value="<?php echo $business['business_id']; ?>">
                                    <?php echo htmlspecialchars($business['business_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_reward_is_active" name="is_active">
                        <label class="form-check-label" for="edit_reward_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" id="delete_action" name="action">
                    <input type="hidden" id="delete_id" name="id">
                    
                    <p id="delete_message">Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit User
    const editUserBtns = document.querySelectorAll('.edit-user-btn');
    editUserBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const username = this.getAttribute('data-username');
            const email = this.getAttribute('data-email');
            const userType = this.getAttribute('data-user-type');
            
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_user_type').value = userType;
        });
    });
    
    // Edit Business
    const editBusinessBtns = document.querySelectorAll('.edit-business-btn');
    editBusinessBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const businessId = this.getAttribute('data-business-id');
            const businessName = this.getAttribute('data-business-name');
            const businessDescription = this.getAttribute('data-business-description');
            const businessCategory = this.getAttribute('data-business-category');
            
            document.getElementById('edit_business_id').value = businessId;
            document.getElementById('edit_business_name').value = businessName;
            document.getElementById('edit_business_description').value = businessDescription;
            document.getElementById('edit_business_category').value = businessCategory;
        });
    });
    
    // Edit Reward
    const editRewardBtns = document.querySelectorAll('.edit-reward-btn');
    editRewardBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const rewardId = this.getAttribute('data-reward-id');
            const rewardName = this.getAttribute('data-reward-name');
            const rewardDescription = this.getAttribute('data-reward-description');
            const pointsRequired = this.getAttribute('data-points-required');
            const businessId = this.getAttribute('data-business-id');
            const isActive = this.getAttribute('data-is-active') === '1';
            
            document.getElementById('edit_reward_id').value = rewardId;
            document.getElementById('edit_reward_name').value = rewardName;
            document.getElementById('edit_reward_description').value = rewardDescription;
            document.getElementById('edit_points_required').value = pointsRequired;
            document.getElementById('edit_business_id').value = businessId;
            document.getElementById('edit_reward_is_active').checked = isActive;
        });
    });
    
    // Delete User
    const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
    deleteUserBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const username = this.getAttribute('data-username');
            
            document.getElementById('delete_action').value = 'delete_user';
            document.getElementById('delete_id').name = 'user_id';
            document.getElementById('delete_id').value = userId;
            document.getElementById('delete_message').textContent = `Are you sure you want to delete the user "${username}"?`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });
    });
    
    // Delete Business
    const deleteBusinessBtns = document.querySelectorAll('.delete-business-btn');
    deleteBusinessBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const businessId = this.getAttribute('data-business-id');
            const businessName = this.getAttribute('data-business-name');
            
            document.getElementById('delete_action').value = 'delete_business';
            document.getElementById('delete_id').name = 'business_id';
            document.getElementById('delete_id').value = businessId;
            document.getElementById('delete_message').textContent = `Are you sure you want to delete the business "${businessName}"?`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });
    });
    
    // Delete Reward
    const deleteRewardBtns = document.querySelectorAll('.delete-reward-btn');
    deleteRewardBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const rewardId = this.getAttribute('data-reward-id');
            const rewardName = this.getAttribute('data-reward-name');
            
            document.getElementById('delete_action').value = 'delete_reward';
            document.getElementById('delete_id').name = 'reward_id';
            document.getElementById('delete_id').value = rewardId;
            document.getElementById('delete_message').textContent = `Are you sure you want to delete the reward "${rewardName}"?`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>
