<?php
session_start();
require_once '../config/database.php';
require_once '../includes/logger.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_user = $_SESSION['username'];

if (isset($_POST['add_artist'])) {
    $stmt = $pdo->prepare("INSERT INTO artists (artist_name, genre, date_signed, added_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['artist_name'], $_POST['genre'], $_POST['date_signed'], $current_user]);
    logActivity($pdo, $current_user, 'CREATE', "Inserted new artist: " . $_POST['artist_name']);
}

if (isset($_POST['update_artist'])) {
    $stmt = $pdo->prepare("UPDATE artists SET artist_name = ?, genre = ?, added_by = ? WHERE artist_id = ?");
    $stmt->execute([$_POST['new_artist_name'], $_POST['new_genre'], $current_user, $_POST['artist_id']]);
    logActivity($pdo, $current_user, 'UPDATE', "Updated artist ID " . $_POST['artist_id'] . " to " . $_POST['new_artist_name']);
}

if (isset($_POST['delete_artist'])) {
    $stmt = $pdo->prepare("DELETE FROM artists WHERE artist_id = ?");
    $stmt->execute([$_POST['artist_id']]);
    logActivity($pdo, $current_user, 'DELETE', "Deleted artist ID " . $_POST['artist_id']);
}

header("Location: ../index.php");
exit();
?>