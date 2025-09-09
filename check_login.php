<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $connection = new PDO("mysql:host=127.0.0.1;dbname=Library_db", "root", "qw12QW!@");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stm = $connection->prepare("SELECT * FROM users WHERE email=?");
    $stm->execute([$_POST['email']]);

    if ($data = $stm->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($_POST['password'], $data['password'])) {
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['fname'] = $data['fname'];
            $_SESSION['lname'] = $data['lname'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['imgname'] = $data['imgname']; 
            header("Location: index.php");
            exit;
        } else {
            header("Location: login.php?error=1");
            exit;
        }
    } else {
        header("Location: login.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>