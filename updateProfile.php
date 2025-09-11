<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$tablename = 'users';
$id =$_REQUEST['id'];
$update =new user();
$data = $update ->getById($tablename ,$id);
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url("./pics/bg1");
      background-size: cover;
      background-repeat: no-repeat;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
      padding: 15px; /* padding صغير علشان الموبايل */
    }
    .card {
      border-radius: 20px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
    }
    .card-header {
      border-radius: 20px 20px 0 0 !important;
      font-size: 1.5rem;
      font-weight: bold;
      background: #87BDDB;
      color: white;
    }
    .btn-primary {
      border-radius: 30px;
      padding: 10px 20px;
    }
    .form-label {
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="card col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
    <div class="card-header text-center">
      Update
    </div>
    <div class="card-body p-4">
      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form action="check_update.php" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="fname" class="form-control" value ="<?= $data['fname']?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lname" class="form-control" value ="<?= $data['lname']?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value ="<?= $data['email']?>" required>
          </div>
          <div class="mb-3 col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" value ="<?= $data['password']?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Image Profile</label>
            <input type="file" name="profimg" class="form-control" value ="<?= $data['imgname']?>" required>
          </div>
        </div>
        <div class="text-center d-flex  justify-content-center align-items-center gap-3">
          <input type="submit" value="update" class="btn btn-primary me-2 mb-2">
        </div>
      </form>
    </div>
  </div>
</body>
</html>