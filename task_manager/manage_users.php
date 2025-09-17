<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// restrict to admins
if ($_SESSION['user']['role'] !== 'Admin') {
    die("Access denied. Admins only.");
}

$conn = new mysqli("localhost", "root", "", "task_manager");

// fetch all users
$users = $conn->query("
    SELECT users.id, users.username, users.role,
           profiles.full_name, profiles.address, profiles.age, profiles.gender
    FROM users
    LEFT JOIN profiles ON users.id = profiles.user_id
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
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
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_users.php" class="active">👥 Manage Users</a>
    <a href="add_task.php">➕ Add Task</a>
    <a href="overdue.php">⚠️ Overdue Tasks</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>User Management</h2>
    <a href="user_add.php" class="btn btn-success mb-3">+ Add User</a>

    <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Username</th>
        <th>Full Name</th>
        <th>Address</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>

      <tbody>
  <?php while($row = $users->fetch_assoc()) { ?>
  <tr>
    <td><?php echo $row['username']; ?></td>
    <td><?php echo $row['full_name'] ?: '<i>No profile</i>'; ?></td>
    <td><?php echo $row['address'] ?: '-'; ?></td>
    <td><?php echo $row['age'] ?: '-'; ?></td>
    <td><?php echo $row['gender'] ?: '-'; ?></td>
    <td><span class="badge bg-info"><?php echo $row['role']; ?></span></td>
    <td>
      <a href="user_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
      <a href="user_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
         onclick="return confirm('Delete this user?');">Delete</a>
    </td>
  </tr>
  <?php } ?>
</tbody>

    </table>
  </div>
</body>
</html>
