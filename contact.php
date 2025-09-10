<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - BookVerse</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #9face6);
      font-family: 'Segoe UI', sans-serif;
      padding-top: 90px; /* space for fixed header */
    }
    ul, li {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-left: 10px;
      border: 2px solid #fff;
    }
    .welcome-text {
      color: #fff;
      margin-right: 10px;
      font-weight: 500;
    }
    .contact-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0px 10px 25px rgba(0,0,0,0.2);
      overflow: hidden;
      margin-top: 40px;
    }
    .contact-header {
      background: #87BDDB;
      color: #fff;
      padding: 30px;
      text-align: center;
    }
    .contact-header h2 {
      margin: 0;
      font-weight: bold;
    }
    .contact-form {
      padding: 30px;
    }
    .form-control {
      border-radius: 12px;
    }
    .btn-custom {
      border-radius: 30px;
      background: #87BDDB;
      color: #fff;
      font-weight: bold;
      transition: 0.3s;
    }
    .btn-custom:hover {
      background: #5AA9D6;
    }
    .contact-info {
      background: #f8f9fa;
      padding: 25px;
      border-top: 1px solid #eee;
    }
    .contact-info i {
      color: #87BDDB;
      font-size: 1.2rem;
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <!-- Fixed Header -->
  <header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="h3 mb-0">📚 BookVerse</h1>
      
      <nav class="navbar navbar-expand-lg navbar-dark">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link " href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="books.php">Browse Books</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="contact.php">Contact Us</a>
            </li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                  Admin Panel
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="list.php">Users</a></li>
                  <li><a class="dropdown-item" href="admin.php">Books</a></li>
                </ul>
              </li>
            <?php endif; ?>
      
          </ul>
        </div>
      </nav>

      <div class="d-flex align-items-center gap-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['fname']) ?></span>
          <a href="profile.php">
            <img src="./<?= htmlspecialchars($_SESSION['imgname']) ?>" class="user-avatar">
          </a>
        <?php endif; ?>
              <li class="nav-item">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="logout.php">Logout</a>
              <?php else: ?>
                <a class="nav-link" href="register.php">Register</a>
              <?php endif; ?>
            </li>
      </div>
      
    </div>
  </header>

  <!-- Contact Form Section -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="contact-card">
          <div class="contact-header">
            <h2>📬 Contact Us</h2>
            <p>We’d love to hear from you. Fill out the form below!</p>
          </div>
          <div class="contact-form">
            <form action="send_contact.php" method="post">
              <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Your Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" rows="5" class="form-control" placeholder="Write your message..." required></textarea>
              </div>
              <div class="text-center">
                <button class="btn btn-custom px-4" type="submit">
                  <i class="fa-solid fa-paper-plane"></i> Send Message
                </button>
              </div>
            </form>
          </div>
          <div class="contact-info d-flex justify-content-around flex-wrap text-center">
            <p><i class="fa-solid fa-phone"></i> +20 1113981508</p>
            <p><i class="fa-solid fa-envelope"></i> support@bookverse.com</p>
            <p><i class="fa-solid fa-location-dot"></i> Assuit, Egypt</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
