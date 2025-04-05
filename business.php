<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perk Up Dashboard</title>

    <link rel="stylesheet" href="stylesss.css">

    <!-- This is the start of Bootstrap and Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- This is the end of Bootstrap and Font Awesome -->
    
</head>
<body>
    <!-- This is the start of Admin Navbar -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Logo Section -->
            <div class="logo">
                <h1>
                    <a href="homepage.php" class="text-white text-decoration-none d-flex align-items-center">
                        <img src="logo.jpg" alt="User Profile Logo" class="brand-logo me-2">
                        PerkUp
                    </a>
                </h1>
            </div>
    
            <!-- Navigation Section -->
            <nav>
                <ul class="nav">
                    <!-- Top items -->
                    <li class="nav-item">
                        <a href="#features" class="nav-link text-white">Features</a>
                    </li>
                    <li class="nav-item">
                        <a href="#how-it-works" class="nav-link text-white">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a href='logout.php' class="nav-link text-white">Logout</a>
                    </li>
                </ul>
                <ul class="nav ms-auto">
                    <!-- Down items -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#rewardsModal">Rewards</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#customerModal">Customers</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>    
    <!-- This is the end of Admin Navbar -->

    <!-- This is the start of Banner section -->
    <div class="container">
        <div class="banner">
            PerkUp Business Dashboard
        </div>
    </div>
    <!-- This is the end of Banner section -->

    <!-- This is the start of Customers Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Customers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="1.jpg" class="customer-picture"> Timothy Hardy
                            <button class="btn btn-outline-primary btn-sm" onclick="manageCustomer('Timothy Hardy')">Manage</button>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="2.jpg" class="customer-picture"> David Miller
                            <button class="btn btn-outline-primary btn-sm" onclick="manageCustomer('David Miller')">Manage</button>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="3.jpg" class="customer-picture"> Sarah Johnson
                            <button class="btn btn-outline-primary btn-sm" onclick="manageCustomer('Sarah Johnson')">Manage</button>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="4.jpg" class="customer-picture"> Emily Davis
                            <button class="btn btn-outline-primary btn-sm" onclick="manageCustomer('Emily Davis')">Manage</button>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="5.jpg" class="customer-picture"> Michael Brown
                            <button class="btn btn-outline-primary btn-sm" onclick="manageCustomer('Michael Brown')">Manage</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- This is the end of Customers Modal -->

    <!-- This is the start of Rewards Modal -->
    <div class="modal fade" id="rewardsModal" tabindex="-1" aria-labelledby="rewardsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eligible Customers for Rewards</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="3.jpg" class="customer-picture"> Sarah Johnson
                            <span class="badge bg-success">Eligible</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="4.jpg" class="customer-picture me"> Emily Davis
                            <span class="badge bg-success">Eligible</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <img src="5.jpg" class="customer-picture"> Michael Brown
                            <span class="badge bg-warning">Nearly Eligible</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- This is the end of Rewards Modal -->

    <!-- This is the start of Dashboard Overview -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="dashboard-card">
                    <h6>Total Customers</h6>
                    <h3>560</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <h6>Active Rewards</h6>
                    <h3>24</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <h6>Premium Members</h6>
                    <h3>120</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <h6>Revenue Generated</h6>
                    <h3>$12k</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- This is the end of Dashboard Overview -->

    <!-- This is the start of Recent Activity -->
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
                <tr>
                    <td>Timothy Hardy</td>
                    <td>Redeemed reward</td>
                    <td>Woolworths $100 Gift Card</td>
                    <td>10 mins ago</td>
                </tr>
                <tr>
                    <td>Michael Brown</td>
                    <td>Earned points</td>
                    <td>Purchase at BigW Store</td>
                    <td>45 mins ago</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- This is the end of Recent Activity -->

     <!-- This is the start of User Engagement -->
     <div class="container mt-4">
        <h4>User Engagement</h4>
        <div class="card p-3">
            <div class="mb-2">Weekly Active Users <div class="progress"><div class="progress-bar progress-purple" style="width: 78%"></div></div></div>
            <div class="mb-2">Point Redemption Rate <div class="progress"><div class="progress-bar progress-blue" style="width: 62%"></div></div></div>
            <div class="mb-2">Referral Success <div class="progress"><div class="progress-bar progress-pink" style="width: 45%"></div></div></div>
        </div>
    </div>
    <!-- This is the end of User Engagement -->

    <!-- This is the start of Footer -->
    <footer class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between">
          <nav>
            <ul class="nav">
              <li class="nav-item"><a href="#privacy" class="nav-link text-white">Privacy Policy</a></li>
              <li class="nav-item"><a href="#terms" class="nav-link text-white">Terms of Service</a></li>
              <li class="nav-item"><a href="#contact" class="nav-link text-white">Contact Us</a></li>
            </ul>
          </nav>
          <div class="social">
            <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </footer>
    <!-- This is the end of Footer -->

    <!-- This is the start of Required scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function manageCustomer(name) {
            alert("Managing " + name);
        }
    </script>
    <!-- This is the end of Required scripts -->
</body>
</html>
