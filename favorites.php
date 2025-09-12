<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];

// Get favorite books
$sql = "SELECT b.* FROM books b
        JOIN favorites f ON b.book_id = f.book_id
        WHERE f.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - BookVerse</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                    <a class="nav-link" href="mybooks.php">My Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="favorites.php">Favorites</a>
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
    <div class="container py-4">
        <h2 class="mb-4">❤️ My Favorite Books</h2>
        <div class="row">
            <?php if ($favorites): ?>
                <?php foreach ($favorites as $book): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($book['cover'])): ?>
                                <img src="<?= htmlspecialchars($book['cover']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="card-text">
                                    <strong>Author:</strong> <?= htmlspecialchars($book['author'] ?? 'Unknown') ?><br>
                                    <strong>Year:</strong> <?= htmlspecialchars($book['published_year'] ?? '-') ?><br>
                                    <strong>Price:</strong> $<?= number_format($book['price'], 2) ?>
                                </p>
                                <a href="bookDetails.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                <a href="toggle_favorite.php?book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-danger">Remove</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You don’t have any favorites yet ❤️</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
