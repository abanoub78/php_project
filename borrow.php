<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $bookId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];

    $pdo = Database::getInstance()->getConnection();

    // 1- تأكد إن الكتاب موجود ولديه نسخ متاحة
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = :book_id AND copies_available > 0 LIMIT 1");
    $stmt->execute([':book_id' => $bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // 2- سجل عملية الاستعارة + return_date بعد 3 أيام
        $stmt = $pdo->prepare("
            INSERT INTO borrowings (user_id, book_id, borrow_date, return_date) 
            VALUES (:user_id, :book_id, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY))
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);

        // 3- قلل نسخة واحدة من المتاح
        $stmt = $pdo->prepare("
            UPDATE books 
            SET copies_available = copies_available - 1 
            WHERE book_id = :book_id
        ");
        $stmt->execute([':book_id' => $bookId]);

        // 4- رجّع المستخدم لصفحة My Books
        header("Location: mybooks.php");
        exit;
    } else {
        echo " Book not available for borrowing.";
    }
} else {
    echo "Invalid request.";
}
