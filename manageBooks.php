<?php
require "db.php";
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();


// alert if update message success

$stmt = $pdo->query("SELECT * FROM books ORDER BY book_id DESC");
$books = $stmt->fetchAll();
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Book updated successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php if ($_GET['error'] == 1): ?>
            Failed to update book.
        <?php elseif ($_GET['error'] == 2): ?>
            Title, author, and category are required.
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- alert if delete message success -->

<?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
    <div class="alert alert-success">Book deleted successfully.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'delete_failed'): ?>
    <div class="alert alert-danger">Failed to delete book.</div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

  <h2>Manage Books</h2>
  <a href="addBook.php" class="btn btn-primary mb-3">➕ Add New Book</a>
  <a href="admin.php" class="btn btn-secondary mb-3">⬅ Back</a>

  <?php if (empty($books)): ?>
    <p>No books found.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <tr>
        <th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Price</th><th>Actions</th>
      </tr>
      <?php foreach ($books as $book): ?>
      <tr>
        <td><?= $book['book_id'] ?></td>
        <td><?= htmlspecialchars($book['title']) ?></td>
        <td><?= htmlspecialchars($book['author']) ?></td>
        <td><?= htmlspecialchars($book['category']) ?></td>
        <td>$<?= number_format($book['price'], 2) ?></td>
        <td>
          <a href="editbook.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="deletebook.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

</body>
</html>
