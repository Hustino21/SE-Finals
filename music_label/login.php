<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
$page_title = "Login";
require_once 'includes/header.php';
?>
<div class="auth-container" style="margin: 0 auto;">
    <?php if (isset($_GET['msg']))
        echo "<div class='msg-success'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>
    <?php if (isset($error))
        echo "<div class='msg-error'>$error</div>"; ?>
    <div class="card form-card">
        <h3 style="margin-top: 0; justify-content: center;">Login</h3>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login" class="full-width">Login</button>
        </form>
    </div>
    <p style="text-align: center; margin-top: 15px;"><a href="register.php">Don't have an account? Register here.</a>
    </p>
</div>
<?php require_once 'includes/footer.php'; ?>