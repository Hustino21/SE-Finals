<?php
require_once 'config/database.php';

if (isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $raw_password = $_POST['password']; 

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/";
    
    if (!preg_match($pattern, $raw_password)) {
        $error = "Password must be at least 8 characters long and include a number, an uppercase letter, and a lowercase letter.";
    } else {
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$first_name, $last_name, $username, $password]);
            header("Location: login.php?msg=Registration successful");
            exit();
        } catch (PDOException $e) {
            $error = "Username already exists or database error.";
        }
    }
}
$page_title = "Register";
require_once 'includes/header.php';
?>
<div class="auth-container" style="margin: 0 auto;">
    <?php if(isset($error)) echo "<div class='msg-error'>$error</div>"; ?>
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