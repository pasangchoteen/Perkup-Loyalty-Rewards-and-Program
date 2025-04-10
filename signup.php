<?php
// Show all errors for debugging (only use in development!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once "config.php";

// Initialize variables
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = $user_type_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                $username_err = "This username is already taken.";
            } else {
                $username = $param_username;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                $email_err = "This email is already registered.";
            } else {
                $email = $param_email;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // If no errors, insert into DB
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);

            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Encrypt password

            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php"); // Redirect to login page after successful signup
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Close the connection after all database operations are done
mysqli_close($link);
?>

<!-- HTML Form for Signup -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sign Up for PerkUp, your go-to loyalty and rewards platform.">
  <title>Sign Up - PerkUp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="logo">
        <h1><a href="homepage.php" class="text-white text-decoration-none d-flex align-items-center">
          <img src="logo.jpg" alt="Logo" class="brand-logo me-2"> PerkUp</a></h1>
      </div>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a href="signup.php" class="nav-link text-white">Sign Up</a></li>
          <li class="nav-item"><a href="login.php" class="nav-link text-white">Login</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section id="signup-section" class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
      <h2 class="text-center mb-4">Create an Account</h2>
      
      <?php if (!empty($username_err) || !empty($email_err) || !empty($password_err) || !empty($confirm_password_err)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php
            if (!empty($username_err)) { echo "<li>" . htmlspecialchars($username_err) . "</li>"; }
            if (!empty($email_err)) { echo "<li>" . htmlspecialchars($email_err) . "</li>"; }
            if (!empty($password_err)) { echo "<li>" . htmlspecialchars($password_err) . "</li>"; }
            if (!empty($confirm_password_err)) { echo "<li>" . htmlspecialchars($confirm_password_err) . "</li>"; }
            ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="signup.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
        </div>
        <div class="mb-3">
          <label for="confirm-password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        <p class="text-center mt-3">Already have an account? <a href="login.php" class="switch-form">Login</a></p>
      </form>
    </div>
  </section>

  <footer class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between">
      <nav>
        <ul class="nav">
          <li class="nav-item"><a href="#privacy" class="nav-link text-white">Privacy Policy</a></li>
          <li class="nav-item"><a href="#terms" class="nav-link text-white">Terms of Service</a></li>
        </ul>
      </nav>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
