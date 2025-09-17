<?php
session_start();
if ($_SESSION['user']['role'] !== 'Admin') {
    die("Access denied.");
}

$conn = new mysqli("localhost", "root", "", "task_manager");

$id = intval($_GET['id']);

// fetch user + profile details
$user = $conn->query("
    SELECT users.*, profiles.full_name, profiles.address, profiles.age, profiles.gender
    FROM users
    LEFT JOIN profiles ON users.id = profiles.user_id
    WHERE users.id = $id
")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $age = !empty($_POST['age']) ? intval($_POST['age']) : NULL;
    $gender = $_POST['gender'];

    // update users table
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();

    // update or insert profile
    $check = $conn->query("SELECT * FROM profiles WHERE user_id=$id");
    if ($check->num_rows > 0) {
        $stmt2 = $conn->prepare("UPDATE profiles SET full_name=?, address=?, age=?, gender=? WHERE user_id=?");
        $stmt2->bind_param("ssisi", $full_name, $address, $age, $gender, $id);
    } else {
        $stmt2 = $conn->prepare("INSERT INTO profiles (user_id, full_name, address, age, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("issis", $id, $full_name, $address, $age, $gender);
    }
    $stmt2->execute();

    header("Location: manage_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Edit User</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </div>
    <div class="mb-3">
      <label>Password (leave blank to keep current)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control">
        <option value="Staff" <?php if($user['role']=='Staff') echo 'selected'; ?>>Staff</option>
        <option value="Admin" <?php if($user['role']=='Admin') echo 'selected'; ?>>Admin</option>
      </select>
    </div>
    <hr>
    <h4>Profile Info</h4>
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>">
    </div>
    <div class="mb-3">
      <label>Address</label>
      <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>">
    </div>
    <div class="mb-3">
      <label>Age</label>
      <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>">
    </div>
    <div class="mb-3">
      <label>Gender</label>
      <select name="gender" class="form-control">
        <option value="Male" <?php if($user['gender']=='Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($user['gender']=='Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if($user['gender']=='Other') echo 'selected'; ?>>Other</option>
      </select>
    </div>
    <button type="submit" class="btn btn-warning">Update</button>
    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>
