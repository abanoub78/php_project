<?php
// if(!isset($_COOKIE['fname'])){
//   header("Location:login.php");

// }

?>
<?php
//1-get id from url
$id = $_GET['id'] ?? null;

if (!$id) {
    die(" No user ID provided!");
}

try {
  require "db.php";
  $user = new User();
  $user = $user->getById("users",$id);


    if (!$user) {
        die("User not found!");
    }

} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>User Details</h2>
  <table class="table table-bordered">
    <tr><th>ID</th><td><?= htmlspecialchars($user['id']) ?></td></tr>
    <tr><th>First Name</th><td><?= htmlspecialchars($user['fname']) ?></td></tr>
    <tr><th>Last Name</th><td><?= htmlspecialchars($user['lname']) ?></td></tr>
    
  </table>

</div>
</body>
</html>
