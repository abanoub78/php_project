<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";
$pdo = Database::getInstance()->getConnection();

// Handle book addition
$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$category = trim($_POST['category'] ?? '');
$published_year = trim($_POST['published_year'] ?? '');
$copies_available = (int)($_POST['copies_available'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$cover = trim($_POST['cover'] ?? ''); 

// validate
$errors = [];
    if (empty($title) || $title < 3) {
        $errors["title"] = "Title must be at least 3 characters";
    }
    if (empty($_POST['author']) || strlen($_POST['author']) < 3) {
        $errors["author"] = "Author must be at least 3 characters";
    }
    if (empty($_POST['category']) || strlen($_POST['category']) < 3) {
        $errors["category"] = "category must be at least 3 characters";
    }
    if (empty($_POST['published_year']) || $_POST['published_year'] < 1900 ||$_POST['published_year'] > 2025 ) {
        $errors["published_year"] = "published_year must be between 1900 to 2025";
    }
     if (empty($_POST['copies_available'])) {
        $errors["copies_available"] = "copies_available must be at least 1 copies_available";
    }
    if (empty($_POST['price'])) {
        $errors["price"] = "price must be found";
    }
     if (empty($_POST['cover'])) {
        $errors["cover"] = "cover must be found";
    }
    session_start();
    if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header("Location: addBook.php");
    }

         
try {
    $sql = "INSERT INTO books (title, author, category, published_year, copies_available, price, cover) 
            VALUES (:title, :author, :category, :published_year, :copies_available, :price, :cover)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':category' => $category,
        ':published_year' => $published_year,
        ':copies_available' => $copies_available,
        ':price' => $price,
        ':cover' => $cover,
    ])){
        $book_success = "Book added successfully.";
        header("Location:manageBooks.php");
    } else {
        echo "Failed to add book.";
    }
} catch (PDOException $e) {
    $book_error = "Database error: " . $e->getMessage();
   
}
?>