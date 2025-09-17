<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit; }

$conn = new mysqli("localhost", "root", "", "task_manager");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$user_id = $_SESSION['user']['id'];
$tasks = $conn->query("SELECT * FROM tasks WHERE assigned_to=$user_id AND status='Completed'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Completed Tasks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { display: flex; min-height: 100vh; }
    .sidebar { width: 220px; background: #343a40; color: white; padding: 20px 0; }
    .sidebar a { color: white; text-decoration: none; display: block; padding: 10px 20px; }
    .sidebar a:hover, .sidebar a.active { background: #495057; }
    .content { flex-grow: 1; padding: 20px; }
  </style>
</head>
<body>
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


  <div class="content">
    <h2>✅ Completed Tasks - <?php echo $_SESSION['user']['username']; ?></h2>
    <hr>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Due Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($tasks->num_rows > 0) { while($row = $tasks->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['due_date'] ?: '<span class="text-muted">Not set</span>'; ?></td>
            <td><span class="badge bg-success">Completed</span></td>
          </tr>
        <?php }} else { ?>
          <tr><td colspan="4" class="text-center text-muted">No completed tasks.</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
