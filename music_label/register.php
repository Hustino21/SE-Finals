<?php
// Connect to database
require_once 'config/database.php';

// Check if registration form is submitted
if (isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $raw_password = $_POST['password'];

    // Require 8+ chars, 1 number, 1 uppercase, 1 lowercase
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/";

    // Validate password pattern
    if (!preg_match($pattern, $raw_password)) {
        $error = "Password must be at least 8 characters long and include a number, an uppercase letter, and a lowercase letter.";
    } else {
        // Hash password for security
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        
        // Insert new user into database
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$first_name, $last_name, $username, $password]);
            // Redirect to login on success
            header("Location: login.php?msg=Registration successful");
            exit();
        } catch (PDOException $e) {
            // Handle duplicate username or database errors
            $error = "Username already exists or database error.";
        }
    }
}
// Set page title and load header
$page_title = "Register";
require_once 'includes/header.php';
?>
<!-- Authentication container -->
<div class="auth-container" style="margin: 0 auto;">
    <?php if (isset($error))
        echo "<div class='msg-error'>$error</div>"; ?>
    <div class="card form-card">
        <h3 style="margin-top: 0; justify-content: center;">Create an Account</h3>
        <form method="POST">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="register" class="full-width">Register</button>
        </form>
    </div>
    <p style="text-align: center; margin-top: 15px;"><a href="login.php">Already have an account? Login here.</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>