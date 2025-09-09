<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "db.php"; // Database connection

$pdo = Database::getInstance()->getConnection();

$searchTerms = [
    "Harry Potter",
    "Game of Thrones",
    "Lord of the Rings",
    "Science",
    "Mathematics",
    "Programming",
    "AI"
];

foreach ($searchTerms as $search) {
    $url = "https://openlibrary.org/search.json?q=" . urlencode($search);
    $response = @file_get_contents($url);
    if ($response === false) {
        echo "Error fetching data for: $search<br>";
        continue;
    }

    $data = json_decode($response, true);
    if (!$data || !isset($data['docs'])) {
        echo "Invalid response for: $search<br>";
        continue;
    }

    foreach ($data['docs'] as $book) {
        $title = $book['title'] ?? '';
        $author = $book['author_name'][0] ?? 'Unknown';
        $year = $book['first_publish_year'] ?? null;
        $isbn = $book['isbn'][0] ?? null;
        $cover = isset($book['cover_i']) 
            ? "https://covers.openlibrary.org/b/id/".$book['cover_i']."-M.jpg" 
            : null;

        // Check if the book already exists
        $stmtCheck = $pdo->prepare("SELECT 1 FROM books WHERE title = ? AND author = ?");
        $stmtCheck->execute([$title, $author]);
        if ($stmtCheck->rowCount() > 0) {
            echo "Skipped (already exists): $title by $author<br>";
            continue;
        }

        // Generate random price as float
        $price = round(mt_rand(1000, 10000) / 100, 2);
        echo "PRICE TO INSERT: $price<br>";

        // Insert the book
        try {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, published_year, isbn, cover, price)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $author, $year, $isbn, $cover, $price]);
            echo "Added: $title by $author (Price: $price)<br>";
        } catch (PDOException $e) {
            echo "Error adding $title by $author: " . $e->getMessage() . "<br>";
        }
    }
}

echo "<br>Done!";
?>
