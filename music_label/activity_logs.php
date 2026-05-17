<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

$logs = $pdo->query("SELECT * FROM activity_logs ORDER BY timestamp DESC")->fetchAll();
$page_title = "Activity Logs - Music Label Manager";

require_once 'includes/header.php';
?>

<div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>Log ID</th>
                <th>User</th>
                <th>Action Type</th>
                <th>Details</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['log_id'] ?></td>
                    <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                    <td>
                        <span style="
                            padding: 4px 8px; 
                            border-radius: 4px; 
                            font-size: 0.8em;
                            font-weight: bold;
                            color: white;
                            background: <?= 
                                $log['action_type'] === 'CREATE' ? 'var(--success-color)' : 
                                ($log['action_type'] === 'UPDATE' ? 'var(--accent-color)' : 
                                ($log['action_type'] === 'DELETE' ? 'var(--danger-color)' : '#64748b'))
                            ?>;">
                            <?= htmlspecialchars($log['action_type']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($log['details']) ?></td>
                    <td style="color: var(--text-secondary); font-size: 0.9em;">
                        <?= date('M d, Y g:i A', strtotime($log['timestamp'])) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No activity logs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>