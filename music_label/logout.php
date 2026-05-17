<?php
session_start();

if (isset($_POST['confirm_logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_POST['cancel'])) {
    header("Location: index.php"); 
    exit();
}

$page_title = "Logout Confirm";
require_once 'includes/header.php';
?>
<div class="auth-container" style="text-align: center; margin: 0 auto;">
    <h3 style="margin-bottom: 20px;">Are you sure you want to log out?</h3>
    <form method="POST" style="display: flex; justify-content: center; gap: 15px;">
        <button type="submit" name="confirm_logout" class="btn-danger">Yes, Logout</button>
        <button type="submit" name="cancel">Cancel</button>
    </form>
</div>
<?php require_once 'includes/footer.php'; ?>