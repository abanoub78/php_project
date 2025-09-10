<?php
require "db.php";

$pdo = Database::getInstance()->getConnection();

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id > 0) {
    $sql = "SELECT * FROM books WHERE book_id = :book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':book_id' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $book = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - <?= $book ? htmlspecialchars($book['title']) : 'Not Found' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <!-- Header -->
      <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">📚 BookVerse</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="books.php">Browse Books</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
        <section class="book-details-section">
            <?php if ($book): ?>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <?php if (!empty($book['cover'])): ?>
                            <img src="<?= htmlspecialchars($book['cover']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($book['title']) ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x450" class="img-fluid rounded" alt="No cover available">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h2><?= htmlspecialchars($book['title']) ?></h2>
                        <p><strong>Author:</strong> <?= htmlspecialchars($book['author'] ?? 'Unknown') ?></p>
                        <p><strong>Published Year:</strong> <?= htmlspecialchars($book['published_year'] ?? '-') ?></p>
                        <p><strong>Category:</strong> <?= htmlspecialchars($book['category'] ?? 'Unknown') ?></p>
                        <p><strong>Price:</strong> $<?= number_format($book['price'], 2) ?></p>
                        <p><strong>Available Copies:</strong> <?= htmlspecialchars($book['copies_available']) ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($book['description'] ?? 'No description available.') ?></p>
                        <div class="mt-3">
                            <?php if ($book['copies_available'] > 0): ?>
                                <a href="borrow.php?id=<?= $book['book_id'] ?>" class="btn btn-primary">Borrow Book</a>
                            <?php else: ?>
                                <p class="text-danger">This book is currently unavailable.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-center text-danger">Book not found.</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-2">&copy; 2025 Our Library. All rights reserved.</p>
            <div class="social-links">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./javaScript/code.js"></script>
</body>
</html>