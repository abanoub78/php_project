<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


$tablename = 'users';
$id =`{$_REQUEST['id']}`;
$delete =new user();
$delete ->delete($tablename ,$id);
echo "user deleted sccass by id = ". $_REQUEST['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <a href="mangeUser.php" class="btn btn-sm btn-primary">go back</a>
</body>
</html>