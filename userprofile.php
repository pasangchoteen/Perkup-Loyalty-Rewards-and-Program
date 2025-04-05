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
    <title>Perk Up</title>

    <link rel="stylesheet" href="stylesss.css">

    <!-- This is the start of Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- This is the end of Bootstrap CSS -->
    <!-- This is the start of Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   <!-- This is the end of Font Awesome for icons -->
   
</head>
<body>
     <!-- This is the start of header -->
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
                        <a href="logout.php" class="nav-link text-white">Logout</a>
                    </li>
                </ul>
                <ul class="nav ms-auto">
                    <!-- Down items -->
                    <li class="nav-item">
                        <a href="#dashboard" class="nav-link text-white">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="#rewards" class="nav-link text-white">Rewards</a>
                    </li>
                    <li class="nav-item">
                        <a href="#referrals" class="nav-link text-white">Referrals</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#userModal" class="nav-link text-white">
                            <i class="fas fa-users me-1"></i> Users
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>    
    <!-- This is the end of Header -->
    
    <!-- This is the start of Profile Section -->
    <div class="container mt-4">
       <div class="card mb-4">
            <div class="banner"></div>
            <div class="profile-container">
                <img src="1.jpg" alt="Profile Picture" class="profile-picture">
                <div class="profile-info">
                    <span class="badge badge-premium mb-2">Premium Member</span>
                    <h2>Timothy Hardy</h2>
                    <p class="text-muted">Mt. Druitt, NSW</p>
                    
                    <div class="container" style="max-width: 400px;">
                        <div class="d-flex justify-content-between align-items-center mt-3 mb-1">
                            <span class="text-muted small">Membership Points</span>
                            <span class="fw-bold small">750 / 1000</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" 
                                 aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted small mt-2 mb-4">250 more points until Gold Tier</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- This is the end of Profile Section -->
        
        <div class="row">
           <!-- This is the start of Rewards Card -->
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="card-icon">
                        <i class="fas fa-gift fa-2x"></i>
                    </div>
                    <h5>Rewards</h5>
                    <p class="text-muted small">View your available rewards</p>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Available Rewards</span>
                        <span class="fw-bold">3</span>
                    </div>
                    <button class="btn btn-primary w-100">View Rewards</button>
                </div>
            </div>
            <!-- This is the end of Rewards Card -->
            
            <!-- This is the start of Referral Card -->
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="card-icon">
                        <i class="fas fa-share-alt fa-2x"></i>
                    </div>
                    <h5>Referral Code</h5>
                    <p class="text-muted small">Share with friends & earn points</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control referral-code" value="TIM2024" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyButton">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <button class="btn btn-outline-primary w-100">Share Link</button>
                </div>
            </div>
            <!-- This is the end of Referral Card -->
            
            <!-- This is the start of Settings Card -->
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="card-icon">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                    <h5>Account Settings</h5>
                    <p class="text-muted small">Manage your profile & preferences</p>
                    <p class="text-muted small mb-3">
                        Update your profile information, notification preferences, and security settings.
                    </p>
                    <button class="btn btn-outline-primary w-100">Manage Settings</button>
                </div>
            </div>
            <!-- This is the end of Settings Card -->
        </div>
    </div>
    
    <!-- This is the start of USERS MODAL -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">All Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="user-grid">
                        <!-- User 1 -->
                        <div class="user-item">
                            <img src="2.jpg" alt="User 1" class="user-picture">
                            <h6>David Miller</h6>
                        </div>
                        <!-- User 2 -->
                        <div class="user-item">
                            <img src="5.jpg" alt="User 2" class="user-picture">
                            <h6>Michael Brown</h6>
                        </div>
                        <!-- User 3 -->
                        <div class="user-item">
                            <img src="3.jpg" alt="User 3" class="user-picture">
                            <h6>Sarah Johnson</h6>
                        </div>
                        <!-- User 4 -->
                        <div class="user-item">
                            <img src="4.jpg" alt="User 4" class="user-picture">
                            <h6>Emily Davis</h6>
                        </div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- This is the end of USERS MODAL -->
    
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
    
   <!-- This is the start of Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- This is the end of Bootstrap JS -->
    
     <!-- This is the start of Custom Script -->
    <script>
        document.getElementById('copyButton').addEventListener('click', function() {
            var referralCode = document.querySelector('.referral-code');
            referralCode.select();
            document.execCommand('copy');
            
            var icon = this.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            icon.style.color = 'green';
            
            setTimeout(function() {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
                icon.style.color = '';
            }, 2000);
        });
    </script>
    <!-- This is the end of Custom Script -->
</body>
</html>
