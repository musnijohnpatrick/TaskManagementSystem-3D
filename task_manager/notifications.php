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

// Fetch notifications for the logged-in user
$notifications = $conn->query("SELECT * FROM notifications 
                               WHERE user_id = $user_id 
                               ORDER BY created_at DESC 
                               LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
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
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Task Manager</h4>
        <hr class="bg-light">
        <a href="my_tasks.php">📝 My Tasks</a>
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
        <h2>🔔 Notifications</h2>
        <hr>
        <div class="card p-3 mb-3">
            <?php if ($notifications && $notifications->num_rows > 0): ?>
                <ul class="list-group">
                    <?php while($n = $notifications->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($n['message']); ?>
                            <small class="text-muted"><?php echo $n['created_at']; ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No notifications yet</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>