<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "task_manager");

// Get task data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $task = $conn->query("SELECT * FROM tasks WHERE id=$id")->fetch_assoc();
}

// Update task on form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : NULL;
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : NULL;
    $status = $_POST['status'];

        if ($due_date && strtotime($due_date) < strtotime(date('Y-m-d'))) {
        die("Invalid due date. You cannot set a past date.");
    }

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, assigned_to=?, due_date=?, status=? WHERE id=?");
    $stmt->bind_param("ssissi", $title, $description, $assigned_to, $due_date, $status, $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}

// Fetch users for assignment dropdown
$users = $conn->query("SELECT id, username FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Task</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Edit Task</h2>
  <form method="POST">
    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">

    <div class="mb-3">
      <label>Title</label>
      <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($task['title']); ?>" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" required><?php echo htmlspecialchars($task['description']); ?></textarea>
    </div>

    <div class="mb-3">
      <label>Assign To</label>
      <select name="assigned_to" class="form-control">
        <option value="">-- Unassigned --</option>
        <?php while($u = $users->fetch_assoc()) { ?>
          <option value="<?php echo $u['id']; ?>" <?php if($task['assigned_to']==$u['id']) echo "selected"; ?>>
            <?php echo $u['username']; ?>
          </option>
        <?php } ?>
      </select>
    </div>

   <div class="mb-3">
  <label>Due Date</label>
  <input type="date" name="due_date" class="form-control"
         min="<?php echo date('Y-m-d'); ?>"
         value="<?php echo $task['due_date'] ?? date('Y-m-d'); ?>">
</div>


    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="Pending" <?php if($task['status']=='Pending') echo 'selected'; ?>>Pending</option>
        <option value="In Progress" <?php if($task['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
        <option value="Completed" <?php if($task['status']=='Completed') echo 'selected'; ?>>Completed</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Task</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>
