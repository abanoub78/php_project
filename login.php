<?php
$error = "";
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error = "Invalid email or password!";
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body {
          /* background: linear-gradient(135deg, #74ebd5, #9face6); */
          background-image: url("./pics/bg1");
          background-size: cover;
          background-repeat: no-repeat;
          height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
      }
      .login-card {
          max-width: 700px;
          width: 100%;
          border-radius: 15px;
          box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      }
      .login-card .card-header {
          background: #87BDDB;
          color: white;
          font-weight: bold;
          text-align: center;
          border-radius: 15px 15px 0 0;
      }
  </style>
</head>
<body>

  <div class="card login-card">
    <div class="card-header">
      <h3>🔐 Login</h3>
    </div>
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger text-center">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="check_login.php">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
    <div class="card-footer text-center">
      <small>Don't have an account? <a href="register.php">Register</a></small>
    </div>
  </div>

</body>
</html>
