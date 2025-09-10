<?php
// session_start();
// if (!isset($_SESSION['user_id'])) {
//   header("Location: login.php");
//   exit;
// }

$id = $_GET['id'] ?? null;
if (!$id) die("⚠️ No user ID provided!");

try {
  require "db.php";
  $userObj = new User();
$role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'member';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $updateData = [
          'fname'   => $_POST['fname'],
          'lname'   => $_POST['lname'],
          'email'   => $_POST['email'] ?? null,
          'role'   => $role,


      ];

      $userObj->update("users",$id, $updateData);
      header("Location: list.php?id=" . $id);
      exit;
  }


$userData = $userObj->getById("users",$id);
  if (!$userData) die("User not found!");

} catch (PDOException $e) {
  die("DB error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Edit User</h2>
  <form method="POST">
    <div class="mb-3">
      <label>First Name</label>
      <input type="text" name="fname" value="<?= htmlspecialchars($userData['fname']) ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Last Name</label>
      <input type="text" name="lname" value="<?= htmlspecialchars($userData['lname']) ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" class="form-control">
    </div>
<div class="mb-3 form-check">
  <input type="checkbox" name="role" value="admin" 
         class="form-check-input" 
         <?= $userData['role'] === 'admin' ? 'checked' : '' ?>>
  <label class="form-check-label">Register as Admin</label>
</div>


    <button type="submit" class="btn btn-primary">Update</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
