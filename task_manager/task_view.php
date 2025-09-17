<?php
session_start();
$conn = new mysqli("localhost", "root", "", "task_manager");

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$task_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];


// Fetch comments
$comments = $conn->query("SELECT comments.*, users.username 
                          FROM comments 
                          JOIN users ON comments.user_id = users.id
                          WHERE task_id = $task_id
                          ORDER BY created_at DESC");


// Handle new comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = $conn->real_escape_string($_POST['comment']);
    
    // Insert comment
    $conn->query("INSERT INTO comments (task_id, user_id, comment) 
                  VALUES ($task_id, $user_id, '$comment')");

    // Notification for the staff themselves
    $conn->query("INSERT INTO notifications (user_id, message) 
                  VALUES ($user_id, 'You added a comment on Task #$task_id')");

    // Notification for all Admin users (if the commenter is not admin)
    if ($_SESSION['user']['role'] !== 'Admin') {
        $admins = $conn->query("SELECT id FROM users WHERE role='Admin'");
        while($admin = $admins->fetch_assoc()) {
            $admin_id = $admin['id'];
            $msg = "Staff ".$_SESSION['user']['username']." commented on Task #$task_id";
            $conn->query("INSERT INTO notifications (user_id, message) 
                          VALUES ($admin_id, '$msg')");
        }
    }
}


// Determine back URL based on role
$back_url = ($_SESSION['user']['role'] === 'Admin') ? 'dashboard.php' : 'my_tasks.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Task Comments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Task #<?php echo $task_id; ?> - Comments</h3>

  <form method="POST" class="mb-3">
    <textarea name="comment" class="form-control" placeholder="Write a comment..." required></textarea>
    <button type="submit" class="btn btn-primary mt-2">Add Comment</button>
  </form>

  <div>
    <?php while($row = $comments->fetch_assoc()): ?>
      <div class="border rounded p-2 mb-2">
        <strong><?php echo $row['username']; ?>:</strong>
        <p><?php echo $row['comment']; ?></p>
        <small class="text-muted"><?php echo $row['created_at']; ?></small>
      </div>
    <?php endwhile; ?>
  </div>

  <a href="<?php echo $back_url; ?>" class="btn btn-secondary mt-3">⬅ Back</a>
</body>
</html>
