<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "task_manager");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to previous page (dashboard or overdue)
$redirect = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
header("Location: $redirect");
exit;
