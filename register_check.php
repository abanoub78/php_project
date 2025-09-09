<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    // Validate inputs
    $errors = [];
    if (empty($_POST['fname']) || strlen($_POST['fname']) < 3) {
        $errors["name"] = "First name must be at least 3 characters";
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email";
    }
    if (strlen($_POST['password']) < 6) {
        $errors["pass"] = "Password must be at least 6 chars";
    }

    // Check for duplicate email
    try {
        if ($user->emailExists($_POST['email'])) {
            $errors["email"] = "Email already registered";
        }
    } catch (PDOException $e) {
        $errors["db"] = "Database error during email check: " . $e->getMessage();
    }

    if (!empty($errors)) {
        header("Location: register.php?errors=" . urlencode(json_encode($errors)));
        exit;
    }

    try {
        $id = $user->create($_POST, $_FILES);
        header("Location: login.php?id=" . $id);
        exit;
    } catch (Exception $e) {
        $errors["db"] = "Failed to register: " . $e->getMessage();
        header("Location: register.php?errors=" . urlencode(json_encode($errors)));
        exit;
    }
}
?>