<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Music Label Manager' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h2>Music Label Management System</h2>
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <strong>Logged in as:</strong> <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <a href="index.php"><button style="margin-top:0;">Dashboard</button></a>
                    <a href="activity_logs.php"><button style="margin-top:0;">Activity Logs</button></a>
                    <a href="logout.php"><button class="btn-danger" style="margin-top:0;">Logout</button></a>
                </div>
            </div>
            <?php endif; ?>
        </div>