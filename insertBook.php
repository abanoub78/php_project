<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";
$pdo = Database::getInstance()->getConnection();

// Handle book addition

//$id = trim($_POST['id'] ?? '');
$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$category = trim($_POST['category'] ?? '');
$published_year = trim($_POST['published_year'] ?? '');
$copies_available = (int)($_POST['copies_available'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$cover = trim($_POST['cover'] ?? '');
$description = trim($_POST['description'] ?? '');

       
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
?>