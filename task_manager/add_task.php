<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit;
}
$conn = new mysqli("localhost", "root", "", "task_manager");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : NULL;
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : NULL;

    // ✅ Validate due date
    if ($due_date && strtotime($due_date) < strtotime(date('Y-m-d'))) {
        die("Invalid due date. You cannot set a past date.");
    }

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, assigned_to, due_date, status) 
                            VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssis", $title, $description, $assigned_to, $due_date);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}

$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Task</title>
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
    <h4 class="text-center mb-4">Task System</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <?php if($_SESSION['user']['role'] === 'Admin') { ?>
      <a href="manage_users.php">👥 Manage Users</a>
    <?php } ?>
    <a href="add_task.php" class="active">➕ Add Task</a>
    <a href="overdue.php">⚠️ Overdue Tasks</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>Add Task</h2>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control"
               min="<?php echo date('Y-m-d'); ?>"
               value="<?php echo date('Y-m-d'); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Assign To</label>
        <select name="assigned_to" class="form-select">
          <option value="">-- Select User --</option>
          <?php while($u = $users->fetch_assoc()) { ?>
            <option value="<?php echo $u['id']; ?>"><?php echo $u['username']; ?></option>
          <?php } ?>
        </select>
      </div>
      <button type="submit" class="btn btn-success">Save Task</button>
      <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>
