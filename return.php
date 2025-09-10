<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['borrow_id']) && isset($_GET['book_id'])) {
    $borrowId = intval($_GET['borrow_id']);
    $bookId   = intval($_GET['book_id']);
    $userId   = $_SESSION['user_id'];

    $pdo = Database::getInstance()->getConnection();

    // 1- زوّد النسخ المتاحة
    $stmt = $pdo->prepare("
        UPDATE books 
        SET copies_available = copies_available + 1 
        WHERE book_id = :book_id
    ");
    $stmt->execute([':book_id' => $bookId]);

    // 2- امسح السطر من جدول الاستعارات
    $stmt = $pdo->prepare("
        DELETE FROM borrowings 
        WHERE borrow_id = :borrow_id AND user_id = :user_id
    ");
    $stmt->execute([
        ':borrow_id' => $borrowId,
        ':user_id'   => $userId
    ]);

    header("Location: mybooks.php");
    exit;
} else {
    echo "❌ Invalid request.";
}
