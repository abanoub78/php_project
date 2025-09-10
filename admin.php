<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$logged_in = isset($_SESSION['user_id']);
$is_admin = $logged_in && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Handle book addition
$book_error = $book_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    if (!$is_admin) {
        $book_error = "You must be an admin to add books.";
    } else {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $published_year = trim($_POST['published_year'] ?? '');
        $copies_available = (int)($_POST['copies_available'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $cover = trim($_POST['cover'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($author) || empty($category)) {
            $book_error = "Title, author, and category are required.";
        } else {
            try {
                $sql = "INSERT INTO books (title, author, category, published_year, copies_available, price, cover, description) 
                        VALUES (:title, :author, :category, :published_year, :copies_available, :price, :cover, :description)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([
                    ':title' => $title,
                    ':author' => $author,
                    ':category' => $category,
                    ':published_year' => $published_year,
                    ':copies_available' => $copies_available,
                    ':price' => $price,
                    ':cover' => $cover,
                    ':description' => $description
                ])) {
                    $book_success = "Book added successfully.";
                } else {
                    $book_error = "Failed to add book.";
                }
            } catch (PDOException $e) {
                $book_error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Handle book deletion
if (isset($_GET['delete_book']) && $is_admin) {
    $book_id = (int)$_GET['delete_book'];
    try {
        $sql = "DELETE FROM books WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        $book_error = "Failed to delete book: " . $e->getMessage();
    }
}

// Handle user deletion
if (isset($_GET['delete_user']) && $is_admin) {
    $user_id = (int)$_GET['delete_user'];
    try {
        $sql = "DELETE FROM users WHERE id = :user_id AND role != 'admin'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        $book_error = "Failed to delete user: " . $e->getMessage();
    }
}

// Fetch all books
try {
    $sql = "SELECT * FROM books ORDER BY book_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $book_error = "Failed to fetch books: " . $e->getMessage();
    $books = [];
}

// Fetch all users
try {
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $book_error = "Failed to fetch users: " . $e->getMessage();
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Our Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">📚 Our Library</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="books.php">Browse Books</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth.php">Login/Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin.php">Admin Panel</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
        <h2 class="mb-4">Admin Panel</h2>

        <?php if (!$logged_in): ?>
            <div class="alert alert-warning">You are not logged in. Please <a href="auth.php">login</a> to access admin features.</div>
        <?php elseif (!$is_admin): ?>
            <div class="alert alert-danger">You do not have admin privileges.</div>
        <?php else: ?>
            <!-- Add Book Form -->
            <section class="admin-section mb-5">
                <h3>Add New Book</h3>
                <?php if ($book_error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($book_error) ?></div>
                <?php endif; ?>
                <?php if ($book_success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($book_success) ?></div>
                <?php endif; ?>
                <form method="post" class="admin-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" name="author" id="author" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" name="category" id="category" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="published_year" class="form-label">Published Year</label>
                            <input type="number" name="published_year" id="published_year" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="copies_available" class="form-label">Copies Available</label>
                            <input type="number" name="copies_available" id="copies_available" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cover" class="form-label">Cover Image URL</label>
                            <input type="url" name="cover" id="cover" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
                </form>
            </section>

            <!-- Books List -->
            <section class="admin-section mb-5">
                <h3>Manage Books</h3>
                <?php if (empty($books)): ?>
                    <p class="text-center">No books found.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['book_id']) ?></td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($book['category'] ?? 'Unknown') ?></td>
                                    <td>$<?= number_format($book['price'], 2) ?></td>
                                    <td>
                                        <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="admin.php?delete_book=<?= $book['book_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- Users List -->
            <section class="admin-section">
                <h3>Manage Users</h3>
                <?php if (empty($users)): ?>
                    <p class="text-center">No users found.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="admin.php?delete_user=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                        <?php else: ?>
                                            <span class="text-muted">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        <?php endif; ?>
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