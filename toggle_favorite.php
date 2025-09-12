<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];
$book_id = $_GET['book_id'] ?? null;

if ($book_id) {
    // check if already in favorites
    $sql = "SELECT * FROM favorites WHERE user_id = :user_id AND book_id = :book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);

    if ($stmt->rowCount() > 0) {
        // remove favorite
        $sql = "DELETE FROM favorites WHERE user_id = :user_id AND book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
    } else {
        // add favorite
        $sql = "INSERT INTO favorites (user_id, book_id) VALUES (:user_id, :book_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
    }
}

// رجّع المستخدم لنفس الصفحة
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
