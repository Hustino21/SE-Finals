<?php
function logActivity($pdo, $username, $action_type, $details) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (username, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$username, $action_type, $details]);
}
?>