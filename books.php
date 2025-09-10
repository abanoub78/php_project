<?php
session_start();
require "db.php";  

$pdo = Database::getInstance()->getConnection(); 

$search = $_GET['search'] ?? '';

// Fetch books
if (!empty($search)) {
    $sql = "SELECT * FROM books 
            WHERE title LIKE :search OR author LIKE :search OR category LIKE :search
            ORDER BY book_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
} else {
    $sql = "SELECT * FROM books ORDER BY book_id DESC"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Books - BookVerse</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      padding-top: 90px; /* space for fixed header */
    }
    ul, li {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    header {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .user-avatar {
      width: 40px; height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #fff;
    }
    .search-section .form-control {
      border-radius: 30px;
    }
    .search-section .btn {
      border-radius: 30px;
      padding: 0 20px;
    }
    .card img {
      height: 200px;
      object-fit: cover;
      border-radius: 10px 10px 0 0;
    }
    .card-title {
      font-size: 1rem;
      font-weight: bold;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    footer {
      background: linear-gradient(135deg, #0d6efd, #6610f2);
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">📚 BookVerse</h1>
      <nav class="navbar navbar-expand-lg navbar-dark">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
           <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link " href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="books.php">Browse Books</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="books.php">My Books</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="favorites.php">Favorites</a>
                            </li>                                                       
                            <li class="nav-item">
                                <a class="nav-link" href="contact.php">Contact Us</a>
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
      <div class="d-flex align-items-center gap-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="me-2">Welcome, <?= htmlspecialchars($_SESSION['fname']) ?></span>
          <a href="profile.php"><img src="./<?= htmlspecialchars($_SESSION['imgname']) ?>" class="user-avatar"></a>
        <?php endif; ?>
                      <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="logout.php">Logout</a>
              <?php else: ?>
                <a class="nav-link" href="register.php">Register</a>
              <?php endif; ?>
            </li>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="container py-4">
    <!-- Search -->
    <section class="search-section mb-4">
      <form method="get" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="🔎 Search books..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
      </form>
    </section>

    <!-- Books -->
    <section>
      <h3 class="mb-3"><?= !empty($search) ? "Results for '".htmlspecialchars($search)."'" : "All Books" ?></h3>
      <div class="row">
        <?php if ($books): ?>
          <?php foreach ($books as $book): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
              <div class="card h-100 shadow-sm">
                <?php if (!empty($book['cover'])): ?>
                  <img src="<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                <?php else: ?>
                  <img src="https://via.placeholder.com/200x300?text=No+Cover" alt="No cover">
                <?php endif; ?>
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                  <p class="card-text small">
                    <strong>Author:</strong> <?= htmlspecialchars($book['author'] ?? 'Unknown') ?><br>
                    <strong>Year:</strong> <?= htmlspecialchars($book['published_year'] ?? '-') ?><br>
                    <strong>Available:</strong> <?= htmlspecialchars($book['copies_available']) ?><br>
                    <strong>Price:</strong> $<?= number_format($book['price'], 2) ?>
                  </p>
                  <div class="d-flex justify-content-between">
                    <a href="bookDetails.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>
                    <a href="borrow.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-primary">Borrow</a>
                    <a href="toggle_favorite.php?book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-heart"></i></a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">❌ No books found<?= $search ? " for '".htmlspecialchars($search)."'" : "" ?>.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="text-white py-4">
    <div class="container text-center">
      <p class="mb-2">&copy; 2025 BookVerse. All rights reserved.</p>
      <div class="social-links">
        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
