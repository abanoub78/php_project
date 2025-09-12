<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manageBooks.php?error=invalid_id");
    exit;
}

$book_id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM books WHERE book_id = :id");
if ($stmt->execute([':id' => $book_id])) {
    header("Location: manageBooks.php?success=deleted");
    exit;
} else {
    header("Location: manageBooks.php?error=delete_failed");
    exit;
}
