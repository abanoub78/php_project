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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    h2 {
      font-weight: bold;
      margin-bottom: 30px;
    }
    .card {
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .card-img-top {
      height: 250px;
      object-fit: cover;
    }
    .btn-return {
      background-color: #dc3545;
      color: #fff;
      border-radius: 20px;
      padding: 6px 15px;
      font-size: 0.9rem;
    }
    .btn-return:hover {
      background-color: #b02a37;
    }
    .empty-state {
      text-align: center;
      padding: 50px 20px;
    }
    .empty-state i {
      font-size: 60px;
      color: #6c757d;
    }
  </style>
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container hcc d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">📚 BookVerse</h1>
           <nav class="navbar navbar-expand-lg navbar-dark ms-3">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">

            <!-- Always show Home + Browse -->
            <li class="nav-item">
                <a class="nav-link " href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="books.php">Browse Books</a>
            </li>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- Guest -->
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>
         

            <?php elseif ($_SESSION['role'] === 'member'): ?>
                <!-- Member -->
                <li class="nav-item">
                    <a class="nav-link active" href="mybooks.php">My Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favorites.php">Favorites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>

            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <!-- Admin -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Admin Panel
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="list.php">Users</a></li>
                        <li><a class="dropdown-item" href="admin.php">Books</a></li>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
  </nav>

<!-- Right side (profile + logout/register) -->
<div class="d-flex align-items-center gap-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="profile d-flex align-items-center">
            <span class="welcome-text me-2">Welcome, <?= htmlspecialchars($_SESSION['fname']) ?></span>
            <a href="profile.php">
                <img src="./<?= htmlspecialchars($_SESSION['imgname']) ?>" class="user-avatar">
            </a>
        </div>
        <a class="nav-link" href="logout.php">Logout</a>
    <?php else: ?>
        <a class="nav-link" href="register.php">Register</a>
    <?php endif; ?>
</div>


</header>
<div class="container py-5">
  <h2 class="text-center text-primary">📚 My Borrowed Books</h2>

  <?php if ($myBooks): ?>
    <div class="row">
      <?php foreach ($myBooks as $book): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card shadow-sm">
            <?php if (!empty($book['cover'])): ?>
              <img src="<?= htmlspecialchars($book['cover']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
            <?php else: ?>
              <img src="https://via.placeholder.com/250x350?text=No+Cover" class="card-img-top" alt="No Cover">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title text-dark"><?= htmlspecialchars($book['title']) ?></h5>
              <p class="text-muted mb-2">
                <i class="bi bi-person"></i> <strong>Author:</strong> <?= htmlspecialchars($book['author']) ?><br>
                <i class="bi bi-calendar"></i> <strong>Year:</strong> <?= htmlspecialchars($book['published_year']) ?><br>
                <i class="bi bi-clock"></i> <strong>Borrowed:</strong> <?= htmlspecialchars($book['borrow_date']) ?><br>
                <i class="bi bi-arrow-return-left"></i> <strong>Return:</strong> <?= htmlspecialchars($book['return_date'] ?: 'Not returned yet') ?>
              </p>
              <div class="d-flex justify-content-between">
                <a href="return.php?borrow_id=<?= $book['borrow_id'] ?>&book_id=<?= $book['book_id'] ?>" 
                   class="btn btn-return">📤 Back to Shelf</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-book"></i>
      <h4 class="mt-3 text-muted">✅ You have no borrowed books.</h4>
    </div>
  <?php endif; ?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</div>
</html>
