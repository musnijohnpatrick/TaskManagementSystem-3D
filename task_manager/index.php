<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Management System - Login</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Footer card styling */
    .footer-card {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 280px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      background-color: #fff;
      padding: 15px;
      font-size: 0.9rem;
    }
    .footer-card strong {
      font-weight: 600;
    }
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow-lg p-4" style="width: 450px; border-radius: 15px;">
    <div class="card-body">
      <h3 class="text-center mb-4">Task Management System</h3>
      <p class="text-muted text-center mb-4">Sign in to manage your tasks</p>
      
      <form action="login.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>

  <!-- Footer card -->
  <div class="footer-card">
    <p class="mb-1"><strong>Programmer:</strong> Musni, John Patrick</p>
    <p class="mb-0"><strong>Members:</strong></p>
    <ul class="mb-0 ps-3">
      <li>Aguilar, Kyrene Erica</li>
      <li>Estabilio, Ceejay</li>
      <li>Gallos, Jomerie</li>
      <li>Ibe, Jairos Andrie</li>
      <li>Piccio, Patrick</li>
    </ul>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
