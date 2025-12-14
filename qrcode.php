<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn_users->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pageTitle = "My QR Code";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    .card {
      text-align: center;
    }

    #qrcode {
      display: inline-block;
      margin: 10px 0;
    }
  </style>
</head>

<body>

  <header class="navbar">
    <button class="hamburger" onclick="toggleSidebar()">
      <span></span><span></span><span></span>
    </button>
    <h1 class="brand">UtiliLog</h1>
    <img src="uploads/um-logo.png" alt="Logo" class="logo">
  </header>

  <nav class="sidebar" id="sidebar">
    <div class="profile">
      <div class="avatar">
        <img src="<?= htmlspecialchars($user['profile_image'] ?? 'uploads/default.png') ?>" alt="Profile">
      </div>
      <p class="username"><?= htmlspecialchars($user['username']) ?></p>
    </div>
    <ul>
      <li><a href="account_info.php">Profile</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="qrcode.php" class="active">QrCode</a></li>
      <li><a href="scanner.php">Task Scanner</a></li>
      <li><a href="status.php">Status</a></li>
      <li><a href="report.php">Report</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>

  <main class="main-content">
    <h2><?= htmlspecialchars($pageTitle) ?></h2>
    <div class="card">
      <h3>Your QR Code</h3>
      <div id="qrcode"></div>
      <p><?= htmlspecialchars($user['email']) ?></p>
    </div>
  </main>

  <script>
    new QRCode(document.getElementById("qrcode"), {
      text: "<?= addslashes($user['email']) ?>",
      width: 150,
      height: 150
    });

    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
      document.querySelector(".hamburger").classList.toggle("open");
    }
  </script>
</body>

</html>