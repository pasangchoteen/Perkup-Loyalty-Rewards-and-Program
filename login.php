<?php
// Initialize the session
session_start();


// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username_or_email = $password = "";
$username_or_email_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username/email is empty
    if (empty(trim($_POST["username"]))) {
        $username_or_email_err = "Please enter username or email.";
    } else {
        $username_or_email = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_or_email_err) && empty($password_err)) {

        // Prepare a select statement to check if the username or email exists
        $sql = "SELECT id, username, email, password, user_type FROM users WHERE username = ? OR email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username_or_email, $param_username_or_email);

            // Set parameters
            $param_username_or_email = $username_or_email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username/email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $email, $hashed_password, $user_type);

                    if (mysqli_stmt_fetch($stmt)) {
                        // Check if the password is correct
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["user_type"] = $user_type;

                            // Redirect user based on their user type
                            if ($user_type == 'user') {
                                header("location: userprofile.php"); // Redirect to user profile page
                            } elseif ($user_type == 'business') {
                                header("location: business.php"); // Redirect to business profile page
                            }
                            exit; // Ensure no further code is executed after redirect
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username/email or password.";
                        }
                    }
                } else {
                    // Username/email doesn't exist, display a generic error message
                    $login_err = "Invalid username/email or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!-- HTML Code for Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - PerkUp</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <!-- Header Section -->
  <header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="logo">
        <h1>
            <a href="homepage.php" class="text-white text-decoration-none d-flex align-items-center">
                <img src="logo.jpg" alt="User Profile Logo" class="brand-logo me-2">
                PerkUp
            </a>
        </h1>
    </div>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a href="#features" class="nav-link text-white">Features</a></li>
          <li class="nav-item"><a href="#how-it-works" class="nav-link text-white">How It Works</a></li>
          <li class="nav-item"><a href="signup.php" class="nav-link text-white">Sign Up</a></li>
          <li class="nav-item"><a href="login.php" class="nav-link text-white">Login</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Welcome back</h2>
            <p class="text-center mb-4">Login to your PerkUp account</p>

            <!-- Display error message if any -->
            <?php if (!empty($login_err)): ?>
              <div class="alert alert-danger">
                <?php echo $login_err; ?>
              </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username/Email</label>
                <input id="username" name="username" type="text" class="form-control" placeholder="Your username or email" required>
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between">
                  <label for="password" class="form-label">Password</label>
                  <a href="password-recovery.php" class="text-decoration-none">Forgot your password?</a>
                </div>
                <input id="password" name="password" type="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
              <div class="text-center mb-3">
                <span>Or continue with</span>
              </div>
              <div class="d-flex justify-content-around">
                <button type="button" class="btn btn-outline-dark" onclick="socialLogin('apple')">
                  <i class="bi bi-apple"></i>
                </button>
                <button type="button" class="btn btn-outline-dark" onclick="socialLogin('google')">
                  <i class="bi bi-google"></i>
                </button>
                <button type="button" class="btn btn-outline-dark" onclick="socialLogin('meta')">
                  <i class="bi bi-facebook"></i>
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              Don't have an account? <a href="signup.php" class="text-decoration-none">Sign up</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Section -->
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

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <!-- Custom JS -->
  <script src="auth.js"></script>
</body>
</html>
