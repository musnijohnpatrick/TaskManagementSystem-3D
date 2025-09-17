<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "task_manager");

$tasks = $conn->query("SELECT tasks.*, users.username AS assigned_user 
                       FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id");

                       
?>

<?php
// Fetch notifications for logged-in user
$user_id = $_SESSION['user']['id'];
$notifications = $conn->query("SELECT * FROM notifications 
                               WHERE user_id = $user_id 
                               ORDER BY created_at DESC 
                               LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Dashboard</title>
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
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <?php if($_SESSION['user']['role'] === 'Admin') { ?>
      <a href="manage_users.php">👥 Manage Users</a>
    <?php } ?>
    <a href="add_task.php">➕ Add Task</a>
    <a href="overdue.php">⚠️ Overdue Tasks</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>Welcome, <?php echo $_SESSION['user']['username']; ?>!</h2>
    <hr>

    <h4>🔔 Notifications</h4>
<div class="card p-3 mb-3">
  <?php if ($notifications->num_rows > 0): ?>
    <ul class="list-group">
      <?php while($n = $notifications->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?php echo $n['message']; ?>
          <small class="text-muted"><?php echo $n['created_at']; ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">No notifications yet</p>
  <?php endif; ?>
</div>


    <h4>Task List</h4>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Assigned To</th>
          <th>Date Created</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $tasks->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $row['title']; ?></td>
          <td><?php echo $row['description']; ?></td>
          <td><?php echo $row['assigned_user'] ?? 'Unassigned'; ?></td>
          <td><?php echo date("Y-m-d", strtotime($row['date_created'])); ?></td>
          <td><?php echo $row['due_date'] ? $row['due_date'] : '<span class="text-muted">Not set</span>'; ?></td>
          <td><span class="badge bg-info"><?php echo $row['status']; ?></span></td>
     <td>
  <a href="update_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>

  <?php if($row['status'] !== 'Completed') { ?>
    <a href="update_task.php?id=<?php echo $row['id']; ?>&status=Completed" class="btn btn-sm btn-primary">Mark Completed</a>
  <?php } ?>

  <a href="delete_task.php?id=<?php echo $row['id']; ?>" 
     class="btn btn-sm btn-danger"
     onclick="return confirm('Are you sure you want to delete this task?');">
     Delete
  </a>

  <!-- ✅ New button for comments -->
  <a href="task_view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Comments</a>
</td>

        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
