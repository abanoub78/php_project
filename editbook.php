<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check book ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: books.php");
    exit;
}

$book_id = (int)$_GET['id'];

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $published_year = $_POST['published_year'] !== "" ? (int)$_POST['published_year'] : null;
        $copies_available = (int)($_POST['copies_available'] ?? 0);
        $price = $_POST['price'] !== "" ? (float)$_POST['price'] : null;
        $cover = trim($_POST['cover'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title && $author && $category) {
            $sql = "UPDATE books 
        SET title = :title, author = :author, category = :category, 
            published_year = :published_year, copies_available = :copies, 
            price = :price, cover = :cover
        WHERE book_id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':published_year' => $published_year,
                ':copies' => $copies_available,
                ':price' => $price,
                ':cover' => $cover,
                ':id' => $book_id
            ]);

            // Redirect cleanly (مهم جدًا: لازم exit مباشرة)
            header("Location: manageBooks.php?success=1");
            exit;
        } else {
            header("Location: manageBooks.php?error=missing");
            exit;
        }
    } catch (PDOException $e) {
        // عشان تتأكد فين المشكلة
        die("DB ERROR: " . $e->getMessage());
    }
}

// Fetch book for form
$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = :id");
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die("Book not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Edit Book</h2>

<form method="post">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($book['category']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Published Year</label>
            <input type="number" name="published_year" class="form-control" value="<?= htmlspecialchars($book['published_year']) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Copies Available</label>
            <input type="number" name="copies_available" class="form-control" value="<?= htmlspecialchars($book['copies_available']) ?>" min="0">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($book['price']) ?>" step="0.01" min="0">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Cover URL</label>
            <input type="url" name="cover" class="form-control" value="<?= htmlspecialchars($book['cover']) ?>">
        </div>
   
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="manageBooks.php" class="btn btn-secondary">Back</a>
</form>

</body>
</html>
