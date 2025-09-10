<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "db.php";  

$pdo = Database::getInstance()->getConnection(); // Database connection

$search = $_GET['search'] ?? '';

// Fetch books for "Popular Books" (ordered by book_id DESC)
if (!empty($search)) {
    $sql = "SELECT * FROM books 
            WHERE (title LIKE :search OR author LIKE :search OR category LIKE :search)
            AND price > 10 
            ORDER BY book_id DESC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
} else {
    $sql = "SELECT * FROM books WHERE price > 70 ORDER BY book_id DESC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch books for "Featured Books" (e.g., high availability or specific category)
if (!empty($search)) {
    $sql = "SELECT * FROM books 
            WHERE (title LIKE :search OR author LIKE :search OR category LIKE :search)
            AND copies_available > 0 
            ORDER BY copies_available LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
} else {
    $sql = "SELECT * FROM books WHERE copies_available > 0 ORDER BY copies_available DESC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
$featured_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookVerse - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <!-- Header -->

  <header class="bg-primary text-white py-3">
        <div class="container hcc d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">📚 BookVerse</h1>
            <div class="d-flex align-items-center">
             
                <nav class="navbar navbar-expand-lg navbar-dark ms-3">
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
                                <a class="nav-link" href="mybooks.php">My Books</a>
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
                
            </div>
       <div class="div d-flex align-items-center gap-4">
<div class="profile">
        <?php if (isset($_SESSION['user_id'])): ?>
        <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['fname']) ?></span>
        <a href="profile.php">
        <img src="./<?= htmlspecialchars($_SESSION['imgname']) ?>"  class="user-avatar">
        </a>

    <?php endif; ?>
</div>
        <li class="nav-item">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a class="nav-link" href="logout.php">Logout</a>
                                <?php else: ?>
                                    <a class="nav-link" href="register.php">Register</a>
                                <?php endif; ?>
                            </li>
</div>

    </header>
    <!-- Carousel Slider -->
    <section class="cover-carousel">
        <div id="coverCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./pics/slide1.jpg" class="d-block w-100 carousel-img" alt="Library Banner 1">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Welcome to Our Library</h2>
                        <p>Discover a world of knowledge.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="./pics/slide2.jpg" class="d-block w-100 carousel-img" alt="Library Banner 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Explore New Arrivals</h2>
                        <p>Find your next favorite book.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="./pics/slide3.jpg" class="d-block w-100 carousel-img" alt="Library Banner 3">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Join Our Community</h2>
                        <p>Connect with fellow book lovers.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#coverCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#coverCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    <!-- Main Content -->
    <main class="container py-4">
        <!-- Search Form -->
        <section class="search-section mb-4">
            <form method="get" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by title, author, or category" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary">Search</button>
            </form>
        </section>
        <!-- Popular Books Section -->
        <section class="books-section mb-5">
            <?php if (!empty($search)): ?>
                <h3 class="mb-3">Search Results for "<?= htmlspecialchars($search) ?>" (Featured)</h3>
            <?php else: ?>
                <h3 class="mb-3">Popular Books</h3>
            <?php endif; ?>
            <div class="row">
                <?php if ($books): ?>
                    <?php foreach ($books as $book): ?>
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
                                        <strong>Available:</strong> <?= htmlspecialchars($book['copies_available']) ?><br>
                                        <strong>Price:</strong> $<?= number_format($book['price'], 2) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="bookDetails.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <a href="borrow.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-primary">Borrow</a>
                                        <a href="toggle_favorite.php?book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-heart"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No books found<?= !empty($search) ? " for '".htmlspecialchars($search)."'" : "" ?>.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Featured Books Section -->
        <section class="books-section mb-5">
            <?php if (!empty($search)): ?>
                <h3 class="mb-3">Search Results for "<?= htmlspecialchars($search) ?>" (Recent)</h3>
            <?php else: ?>
                <h3 class="mb-3">Featured Books</h3>
            <?php endif; ?>
            <div class="row">
                <?php if ($featured_books): ?>
                    <?php foreach ($featured_books as $book): ?>
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
                                        <strong>Available:</strong> <?= htmlspecialchars($book['copies_available']) ?><br>
                                        <strong>Price:</strong> $<?= number_format($book['price'], 2) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="bookDetails.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <a href="borrow.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-primary">Borrow</a>
                                        <a href="toggle_favorite.php?book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-heart"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No books found<?= !empty($search) ? " for '".htmlspecialchars($search)."'" : "" ?>.</p>
                <?php endif; ?>
            </div>
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