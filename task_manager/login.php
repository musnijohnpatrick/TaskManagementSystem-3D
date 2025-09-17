<?php
session_start();
$conn = new mysqli("localhost", "root", "", "task_manager");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password']; // plain password

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $db_pass = $user['password'];
    $valid = false;

    // Check hashed (bcrypt) password
    if (password_verify($password, $db_pass)) {
        $valid = true;
    }

    // Check MD5 (for old admin account)
    if (!$valid && $db_pass === md5($password)) {
        $valid = true;
    }

    if ($valid) {
        $_SESSION['user'] = $user;

        // Redirect based on role
        if ($user['role'] === 'Admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: my_tasks.php");
        }
        exit;
    }
}

echo "<script>alert('Invalid credentials'); window.location='index.php';</script>";
?>
