
<?php
// if(!isset($_COOKIE['fname'])){
//   header("Location:login.php");

// }\
        session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

?>

<?php
require "db.php";
$user = new User();
$users = $user->getAll("users");
?>


<!DOCTYPE html>
<html lang="en">
<head>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Users List</title>
  <style>
    table {
      width: 80%;
      border-collapse: collapse;
      margin: 20px auto;
    }
    th, td {
      border: 1px solid #555;
      padding: 8px;
      text-align: center;
    }
    th {
      background: #333;
      color: #fff;
    }
  </style>
</head>
<body>
  <?php 
  
  echo "<h2> Hello : {$_SESSION['fname']} {$_SESSION['lname']}</h2>";
  
  ?>
  <h2 style="text-align:center;"> Users List</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
         <th>Email</th>
          <th>Role</th>
         <th>Password</th>
        <th>img</th>

        <th>Actions</th>

      </tr>
    </thead>
    <tbody>
        <a href="register.php" class="btn btn-success">Create new user</a>              
      <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['fname']) ?></td>
            <td><?= htmlspecialchars($user['lname']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= htmlspecialchars($user['password']) ?></td>
            <td><img src="./<?= htmlspecialchars($user['imgname']) ?>" width="60"></td>
            <td>
  <a href="view.php?id=<?= $user['id'] ?>" class="btn btn-primary">View</a>
  <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-primary">Edit</a>
  <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-danger"
     onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
</td>

                  
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8">No users found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
