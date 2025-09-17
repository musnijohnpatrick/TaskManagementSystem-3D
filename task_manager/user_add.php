<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    die("Access denied.");
}

$conn = new mysqli("localhost", "root", "", "task_manager");

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username   = $_POST['username'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role       = $_POST['role'];

    $full_name  = $_POST['full_name'];
    $address    = $_POST['address'];
    $age        = $_POST['age'];
    $gender     = $_POST['gender'];

    // 1️⃣ Insert into users
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $user_id = $stmt->insert_id; // get new user's ID

    // 2️⃣ Insert into profiles
    $stmt2 = $conn->prepare("INSERT INTO profiles (user_id, full_name, address, age, gender) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("issis", $user_id, $full_name, $address, $age, $gender);
    $stmt2->execute();

    header("Location: manage_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 220px;
      background: #343a40;
      color: #fff;
      flex-shrink: 0;
      padding-top: 20px;
    }
    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #495057;
      color: #fff;
    }
    .content {
      flex-grow: 1;
      padding: 20px;
      background: #f8f9fa;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center mb-4">Dashboard</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_users.php" class="active">👥 Manage Users</a>
    <a href="add_task.php">➕ Add Task</a>
    <a href="all_tasks.php">📋 All Tasks</a>
    <a href="overdue.php">⚠️ Overdue Tasks</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Content -->
  <div class="content">
    <h2>Add User</h2>
    <form method="POST">
      <!-- Login Credentials -->
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control">
          <option value="Staff">Staff</option>
          <option value="Admin">Admin</option>
        </select>
      </div>

      <!-- Profile Info -->
      <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Address</label>
        <input type="text" name="address" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Age</label>
        <input type="number" name="age" class="form-control" min="1" required>
      </div>
      <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-control" required>
          <option value="">-- Select Gender --</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <button type="submit" class="btn btn-success">Add User</button>
    </form>
  </div>
</body>
</html>
