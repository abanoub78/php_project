<?php
session_start();
require "db.php";

$pdo = Database::getInstance()->getConnection();

// لو مش عامل لوجين
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// هات بيانات المستخدم الحالي
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// لو مفيش يوزر
if (!$user) {
    echo "User not found.";
    exit;
}

// تحديث البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'] ?? $user['fname'];
    $email = $_POST['email'] ?? $user['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];
    $imgPath = $user['imgname']; // الافتراضي الصورة القديمة

    // لو فيه صورة جديدة
if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
    $targetDir = __DIR__ . "/uploads/"; // المسار على السيرفر
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["profile_img"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
        $imgPath = "uploads/" . $fileName; // مسار بالنسبة للويب
        $_SESSION['imgname'] = $imgPath;
        echo "<div class='alert alert-success'>✅ Imgae Uploded Succssfully</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ فشل في move_uploaded_file</div>";
    }
}


    // تحديث البيانات في الداتا بيز
    $stmt = $pdo->prepare("UPDATE users SET fname = ?, email = ?, password = ?, imgname = ? WHERE id = ?");
    $stmt->execute([$fname, $email, $password, $imgPath, $_SESSION['user_id']]);

    // تحديث السيشن
    $_SESSION['fname'] = $fname;
    $_SESSION['email'] = $email;
    $_SESSION['imgname'] = $imgPath;

    $success = "Profile updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">

    <h2>Edit Profile</h2>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($user['fname']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Image</label><br>
            <?php if (!empty($user['imgname'])): ?>
                <img src="<?= htmlspecialchars($user['imgname']) ?>" alt="Profile" style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="profile_img" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
