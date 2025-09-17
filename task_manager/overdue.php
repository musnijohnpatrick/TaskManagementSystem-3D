<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "task_manager");

// Fetch overdue tasks (due_date < today and not completed)
$today = date("Y-m-d");
$sql = "SELECT tasks.*, users.username AS assigned_user 
        FROM tasks 
        LEFT JOIN users ON tasks.assigned_to = users.id
        WHERE tasks.due_date IS NOT NULL 
          AND tasks.due_date < ? 
          AND tasks.status != 'Completed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Overdue Tasks</title>
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
    <?php if($_SESSION['user']['role'] === 'Admin') { ?>
      <a href="manage_users.php">👥 Manage Users</a>
    <?php } ?>
    <a href="add_task.php">➕ Add Task</a>
    <a href="overdue.php" class="active">⚠️ Overdue Tasks</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>⚠️ Overdue Tasks</h2>
    <hr>

    <table class="table table-bordered table-striped">
        <thead class="table-danger">
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
                <?php if ($result->num_rows > 0) { 
                    while($row = $result->fetch_assoc()) { ?>
                    <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['assigned_user'] ?? 'Unassigned'; ?></td>
                    <td><?php echo date("Y-m-d", strtotime($row['date_created'])); ?></td>
                    <td class="text-danger fw-bold"><?php echo $row['due_date']; ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending') { ?>
                        <span class="badge bg-warning text-dark">Pending (Overdue)</span>
                        <?php } elseif ($row['status'] == 'In Progress') { ?>
                        <span class="badge bg-info text-dark">In Progress (Overdue)</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="delete_task.php?id=<?php echo $row['id']; ?>" 
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this overdue task?');">
                        Delete
                        </a>
                    </td>
                    </tr>
                <?php } } else { ?>
                    <tr>
                    <td colspan="7" class="text-center text-muted">No overdue tasks 🎉</td>
                    </tr>
                <?php } ?>
             </tbody>

    </table>
  </div>
</body>
</html>
