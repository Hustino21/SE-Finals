<?php
// Start user session
session_start();
// Include database connection and logging functionality
require_once '../config/database.php';
require_once '../includes/logger.php';

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_user = $_SESSION['username'];

// Handle adding a new album
if (isset($_POST['add_album'])) {
    $stmt = $pdo->prepare("INSERT INTO albums (artist_id, album_title, release_year, added_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['artist_id'], $_POST['album_title'], $_POST['release_year'], $current_user]);
    // Log the creation activity
    logActivity($pdo, $current_user, 'CREATE', "Inserted new album: " . $_POST['album_title']);
}

// Handle updating an existing album
if (isset($_POST['update_album'])) {
    $stmt = $pdo->prepare("UPDATE albums SET album_title = ?, release_year = ?, added_by = ? WHERE album_id = ?");
    $stmt->execute([$_POST['new_title'], $_POST['new_year'], $current_user, $_POST['album_id']]);
    // Log the update activity
    logActivity($pdo, $current_user, 'UPDATE', "Updated album ID " . $_POST['album_id'] . " to " . $_POST['new_title']);
}

// Handle deleting an album
if (isset($_POST['delete_album'])) {
    $stmt = $pdo->prepare("DELETE FROM albums WHERE album_id = ?");
    $stmt->execute([$_POST['album_id']]);
    // Log the deletion activity
    logActivity($pdo, $current_user, 'DELETE', "Deleted album ID " . $_POST['album_id']);
}

// Redirect back to the dashboard after action
header("Location: ../index.php");
exit();
?>