<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['profile_image'])) {
  $file = $_FILES['profile_image'];

  if ($file['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $detectedType = mime_content_type($file['tmp_name']);

    if (!in_array($detectedType, $allowedTypes)) {
      $uploadError = "Invalid file type. Only JPG, PNG, GIF, or WEBP allowed.";
    } elseif ($file['size'] > 2 * 1024 * 1024) {
      $uploadError = "File too large. Max 2MB.";
    } else {
      if (!is_dir('uploads'))
        mkdir('uploads', 0755, true);

      $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      $newFileName = "uploads/profile_" . $userId . "_" . time() . "." . $ext;

      if (move_uploaded_file($file['tmp_name'], $newFileName)) {
        $stmt = $conn_users->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $newFileName, $userId);
        if ($stmt->execute()) {
          $uploadSuccess = "Profile picture updated!";
        } else {
          $uploadError = "Database error.";
        }
      } else {
        $uploadError = "Failed to move uploaded file.";
      }
    }
  } else {
    $uploadError = "Upload error code: " . $file['error'];
  }
}

$stmt = $conn_users->prepare("SELECT username, email, profile_image, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$pageTitle = "Profile";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

    <header class="navbar">
        <div class="brand-section">
            <button class="hamburger" onclick="toggleSidebar()">☰</button>
            <img src="uploads/um-logo.png" alt="Logo" class="logo">
            <span class="brand">UtiliLog</span>
        </div>
    </header>

    <nav class="sidebar" id="sidebar">
        <div class="profile">
            <div class="avatar">
                <img src="<?= htmlspecialchars($user['profile_image'] ?? 'uploads/default.png') ?>" alt="Profile">
            </div>
            <p class="username"><?= htmlspecialchars($user['username']) ?></p>
        </div>
        <ul>
            <li><a href="account_info.php" class="active">Profile</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="qrcode.php">QrCode</a></li>
            <li><a href="scanner.php">Task Scanner</a></li>
            <li><a href="status.php">Status</a></li>
            <li><a href="report.php">Report</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h2><?= htmlspecialchars($pageTitle) ?></h2>

        <div class="card">
            <h3>Account Details</h3>
            <div style="margin-bottom: 20px;">
                <label style="display:block; color:#00bcd4; font-weight:bold; margin-bottom:5px;">Username</label>
                <div
                    style="font-size:1.1rem; padding:10px; background:#02202b; border-radius:4px; border:1px solid #005662;">
                    <?= htmlspecialchars($user['username']) ?>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display:block; color:#00bcd4; font-weight:bold; margin-bottom:5px;">Email</label>
                <div
                    style="font-size:1.1rem; padding:10px; background:#02202b; border-radius:4px; border:1px solid #005662;">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display:block; color:#00bcd4; font-weight:bold; margin-bottom:5px;">Joined</label>
                <div
                    style="font-size:1.1rem; padding:10px; background:#02202b; border-radius:4px; border:1px solid #005662;">
                    <?= date("F j, Y", strtotime($user['created_at'])) ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Change Profile Picture</h3>

            <?php if (!empty($uploadSuccess)): ?>
            <div style="background:#2e7d32; color:white; padding:10px; border-radius:4px; margin-bottom:15px;">
                <?= htmlspecialchars($uploadSuccess) ?>
            </div>
            <?php elseif (!empty($uploadError)): ?>
            <div style="background:#c62828; color:white; padding:10px; border-radius:4px; margin-bottom:15px;">
                <?= htmlspecialchars($uploadError) ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_image" accept="image/*" required onchange="previewImage(event)">

                <div style="margin:20px 0; text-align:center;">
                    <img id="preview" src="#" alt="Preview"
                        style="display:none; width:120px; height:120px; border-radius:50%; border:3px solid #00bcd4; object-fit:cover; margin:0 auto;">
                </div>

                <button type="submit">Upload Photo</button>
            </form>
        </div>
    </main>

    <script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
    }

    function previewImage(event) {
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.style.display = 'block';
    }
    </script>
</body>

</html>