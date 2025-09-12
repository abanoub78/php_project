<?php
require "db.php";
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ فقط الأدمن يقدر يشوف الصفحة
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ هات بيانات الاستعارة مع اسم المستخدم واسم الكتاب
$sql = "
    SELECT 
        br.borrow_id,
        u.id AS user_id,
        CONCAT(u.fname, ' ', u.lname) AS full_name,
        b.title AS book_title,
        br.borrow_date,
        br.return_date
    FROM borrowings br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.book_id
    ORDER BY br.borrow_date DESC
";
$stmt = $pdo->query($sql);
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Borrowers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

    <h2>All Book Borrowings</h2>

    <?php if ($borrowings): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Borrow ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowings as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['borrow_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['book_title']) ?></td>
                        <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                        <td>
                            <?= $row['return_date'] 
                                ? htmlspecialchars($row['return_date']) 
                                : '<span class="text-danger">Not Returned</span>' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No borrowings found.</div>
    <?php endif; ?>


</body>
</html>
