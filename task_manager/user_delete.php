<?php
session_start();
if ($_SESSION['user']['role'] !== 'Admin') {
    die("Access denied.");
}

$conn = new mysqli("localhost", "root", "", "task_manager");

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id=$id");

header("Location: manage_users.php");
exit;
