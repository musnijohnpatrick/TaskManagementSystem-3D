<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "task_manager");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user']['id'];

// Join users + profiles
$sql = "
    SELECT users.username, users.role, users.created_at,
           profiles.full_name, profiles.address, profiles.age, profiles.gender
    FROM users
    LEFT JOIN profiles ON users.id = profiles.user_id
    WHERE users.id = $user_id
";
$user = $conn->query($sql)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 220px;
      background: #343a40;
      color: white;
      padding: 20px 0;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #495057;
    }
    .content {
      flex-grow: 1;
      padding: 20px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
<div class="sidebar">
  <h4 class="text-center">Task Manager</h4>
  <hr class="bg-light">
  <a href="my_tasks.php" class="active">📝 My Tasks</a>
  <a href="profile.php">👤 Profile</a>
  <hr class="bg-light">
  <a href="user_overdue.php">⏰ Overdue</a>
  <a href="pending.php">⌛ Pending</a>
  <a href="inprogress.php">🚧 In Progress</a>
  <a href="completed.php">✅ Completed</a>
  <hr class="bg-light">
  <a href="logout.php" class="text-danger">🚪 Logout</a>
</div>


  <!-- Main Content -->
  <div class="content">
    <h2>👤 My Profile</h2>
    <hr>

    <div class="card shadow-sm" style="max-width: 500px;">
      <div class="card-body">
        <p><strong>Full Name:</strong> <?php echo $user['full_name'] ?: 'N/A'; ?></p>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Role:</strong> 
          <span class="badge bg-info text-dark"><?php echo $user['role']; ?></span>
        </p>
        <p><strong>Address:</strong> <?php echo $user['address'] ?: '-'; ?></p>
        <p><strong>Age:</strong> <?php echo $user['age'] ?: '-'; ?></p>
        <p><strong>Gender:</strong> <?php echo $user['gender'] ?: '-'; ?></p>
        <p><strong>Account Created:</strong> 
          <?php echo !empty($user['created_at']) ? date("F d, Y", strtotime($user['created_at'])) : 'N/A'; ?>
        </p>


      </div>
    </div>
  </div>
</body>
</html>
