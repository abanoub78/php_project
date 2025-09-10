<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

// هات الكتب اللي لسه مستعيرها المستخدم
$sql = "
    SELECT b.borrow_id, b.borrow_date, b.return_date, 
           bo.book_id, bo.title, bo.author, bo.cover, bo.published_year 
    FROM borrowings b
    JOIN books bo ON b.book_id = bo.book_id
    WHERE b.user_id = :user_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$myBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
  <h2>📚 My Borrowed Books</h2>

  <?php if ($myBooks): ?>
    <div class="row">
      <?php foreach ($myBooks as $book): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <?php if (!empty($book['cover'])): ?>
              <img src="<?= htmlspecialchars($book['cover']) ?>" class="card-img-top" alt="">
            <?php else: ?>
              <img src="https://via.placeholder.com/200x300?text=No+Cover" class="card-img-top" alt="">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
    
              <p class="mb-2">
                <strong>Author:</strong> <?= htmlspecialchars($book['author']) ?> <br>
                <strong>Year:</strong> <?= htmlspecialchars($book['published_year']) ?><br>
                <strong>Borrowed on:</strong> <?= htmlspecialchars($book['borrow_date']) ?><br>
                <strong>Return date:</strong> <?= htmlspecialchars($book['return_date']) ?>
              </p>

              <a href="return.php?borrow_id=<?= $book['borrow_id'] ?>&book_id=<?= $book['book_id'] ?>" 
                 class="btn btn-sm btn-danger">📤 Back to Shelf</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted">✅ You have no borrowed books.</p>
  <?php endif; ?>
</body>
</html>
