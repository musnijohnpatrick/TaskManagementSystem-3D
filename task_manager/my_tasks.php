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

// ✅ Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("SELECT id FROM tasks WHERE id=? AND assigned_to=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE tasks SET status=? WHERE id=? AND assigned_to=?");
        $stmt->bind_param("sii", $status, $id, $user_id);
        $stmt->execute();
    }

    header("Location: my_tasks.php");
    exit;
}

// ✅ Fetch only this staff's tasks
$tasks = $conn->query("SELECT * FROM tasks WHERE assigned_to = $user_id");
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
  <title>My Tasks</title>
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
  <a href="notifications.php">
    🔔 Notifications
    <?php if($notifications->num_rows > 0): ?>
      <span style="display:inline-block; width:8px; height:8px; background:red; border-radius:50%; margin-left:5px;"></span>
    <?php endif; ?>
  </a>
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
    <h2>My Tasks - <?php echo $_SESSION['user']['username']; ?></h2>
    <hr>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Date Created</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($tasks->num_rows > 0) { ?>
          <?php while($row = $tasks->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo date("Y-m-d", strtotime($row['date_created'])); ?></td>
            <td>
              <?php 
                if ($row['due_date'] && strtotime($row['due_date']) < strtotime(date('Y-m-d'))) {
                    echo "<span class='text-danger fw-bold'>" . $row['due_date'] . " (Overdue)</span>";
                } else {
                    echo $row['due_date'] ?: '<span class="text-muted">Not set</span>';
                }
              ?>
            </td>
            <td>
              <?php 
                $statusClass = match($row['status']) {
                  'Pending' => 'bg-secondary',
                  'In Progress' => 'bg-warning',
                  'Completed' => 'bg-success',
                  default => 'bg-info'
                };
              ?>
              <span class="badge <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span>
            </td>
         
              <td>
                <div class="d-flex flex-column">
                  <!-- Status update form -->
                  <form method="POST" class="d-flex mb-2">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <select name="status" class="form-select form-select-sm me-2">
                      <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                      <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                      <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                  </form>

                  <!-- Comments button -->
                  <a href="task_view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Comments</a>
                </div>
              </td>


          </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="6" class="text-center text-muted">No tasks assigned to you.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
