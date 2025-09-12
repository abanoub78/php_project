<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

  <h2>📚 Admin Dashboard</h2>

  <div class="d-grid gap-3 col-6 mx-auto mt-4">
    <a href="addBook.php" class="btn btn-primary">➕ Add Book</a>
    <a href="manageBooks.php" class="btn btn-warning">📖 Manage Books</a>
    <a href="Bookborrowers.php" class="btn btn-danger">👤 Book borrowers</a>

  </div>

</body>
</html>
