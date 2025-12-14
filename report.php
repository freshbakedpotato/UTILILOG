<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user_id'];


$stmt = $conn_users->prepare("SELECT username, profile_image FROM users WHERE id = ?");
if (!$stmt) {
  die("Prepare failed for user fetch: " . $conn_users->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$sidebarUser = $stmt->get_result()->fetch_assoc();

$pageTitle = "Report";
$success = $error = "";


if (isset($_GET['delete'])) {
  $deleteId = intval($_GET['delete']);
  $res = $conn_users->query("SELECT photo FROM reports WHERE id = $deleteId AND user_id = $userId");
  if ($res && $res->num_rows > 0) {
    $photo = $res->fetch_assoc()['photo'];
    if ($photo && file_exists($photo)) {
      unlink($photo);
    }
    $conn_users->query("DELETE FROM reports WHERE id = $deleteId");
    header("Location: report.php?deleted=1");
    exit;
  }
}

if (isset($_GET['success'])) {
  $success = "✅ Report submitted successfully!";
}
if (isset($_GET['deleted'])) {
  $success = "🗑 Report deleted successfully!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $comment = trim($_POST['comment']);
  $imageData = $_POST['image_data'] ?? "";

  if (!empty($comment)) {
    $conn_users->query("
            CREATE TABLE IF NOT EXISTS reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                photo VARCHAR(255),
                comment TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

    $photoPath = null;

    if (!empty($imageData)) {
      $imageDir = "uploads/reports/";
      if (!is_dir($imageDir)) {
        mkdir($imageDir, 0777, true);
      }

      $imageName = "report_" . time() . "_user" . $userId . ".png";
      $photoPath = $imageDir . $imageName;

      $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
      $imageDecoded = base64_decode($imageData);

      if (!file_put_contents($photoPath, $imageDecoded)) {
        $error = "❌ Failed to save the image file.";
      }
    }

    if (empty($error)) {
      $stmt = $conn_users->prepare("INSERT INTO reports (user_id, photo, comment) VALUES (?, ?, ?)");
      if (!$stmt) {
        die("Prepare failed for report insert: " . $conn_users->error);
      }
      $stmt->bind_param("iss", $userId, $photoPath, $comment);
      if ($stmt->execute()) {
        echo "<script>
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?success=1';
                    }, 800);
                </script>";
        exit;
      } else {
        $error = "❌ Error submitting report: " . $stmt->error;
      }
      $stmt->close();
    }
  } else {
    $error = "❌ Comment is required.";
  }
}


$reports = [];
$result = $conn_users->query("SELECT id, photo, comment, created_at FROM reports WHERE user_id = $userId ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
  $reports = $result->fetch_all(MYSQLI_ASSOC);
}
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
        <button class="hamburger" onclick="toggleSidebar()">
            <span></span><span></span><span></span>
        </button>
        <h1 class="brand">UtiliLog</h1>
        <img src="uploads/um-logo.png" alt="Logo" class="logo">
    </header>

    <nav class="sidebar" id="sidebar">
        <div class="profile">
            <div class="avatar">
                <img src="<?= htmlspecialchars($sidebarUser['profile_image'] ?? 'uploads/default.png') ?>"
                    alt="Profile">
            </div>
            <p class="username"><?= htmlspecialchars($sidebarUser['username']) ?></p>
        </div>
        <ul>
            <li><a href="account_info.php">Profile</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="qrcode.php">My QR Code</a></li>
            <li><a href="scanner.php">Task Scanner</a></li>
            <li><a href="status.php">Status</a></li>
            <li><a href="report.php" class="active">Report</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h2 class="page-title"><?= htmlspecialchars($pageTitle) ?></h2>

        <?php if ($success): ?>
        <p class="message success"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?>
        <p class="message error"><?= $error ?></p><?php endif; ?>

        <form method="POST" onsubmit="capturePhoto()" class="report-form">
            <div class="camera-section">
                <video id="camera" autoplay playsinline></video>
                <button type="button" class="btn-primary" onclick="takePhoto()">📸 Take Photo</button>

                <div id="preview-container" style="display:none;">
                    <img id="preview" src="" alt="Preview">
                    <button type="button" class="btn-danger" onclick="deletePhoto()">🗑 Delete Photo</button>
                </div>
            </div>

            <textarea name="comment" placeholder="Write your comment..." required></textarea>
            <button type="submit" class="btn-submit"> Submit Report</button>
            <input type="hidden" name="image_data" id="image_data">
        </form>


        <?php if (!empty($reports)): ?>
        <section class="report-overview">
            <h3>📋 Your Submitted Reports</h3>
            <?php foreach ($reports as $r): ?>
            <div class="report-card">
                <?php if (!empty($r['photo'])): ?>
                <img src="<?= htmlspecialchars($r['photo']) ?>" alt="Report Photo">
                <?php endif; ?>
                <p><strong>Comment:</strong> <?= htmlspecialchars($r['comment']) ?></p>
                <small><em><?= htmlspecialchars($r['created_at']) ?></em></small>
                <form method="GET" onsubmit="return confirm('Are you sure you want to delete this report?')"
                    style="margin-top:10px;">
                    <input type="hidden" name="delete" value="<?= $r['id'] ?>">
                    <button type="submit" class="btn-danger">🗑 Delete Report</button>
                </form>
            </div>
            <?php endforeach; ?>
        </section>
        <?php else: ?>
        <p class="no-reports">No reports submitted yet.</p>
        <?php endif; ?>
    </main>

    <style>
    .main-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        min-height: 100vh;
        background-color: #032733;
        color: #b5d3d8ff;
    }

    .page-title {
        font-size: 26px;
        margin-bottom: 20px;
    }

    .report-form {
        background: #5499c2ff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 100%;
        max-width: 420px;
        color: #000;
        margin-bottom: 30px;
    }

    .report-form textarea {
        width: 100%;
        height: 100px;
        margin-top: 15px;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ccc;
        resize: none;
    }

    .camera-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #camera,
    #preview {
        border-radius: 12px;
        border: 2px solid #032733;
        margin: 10px 0;
        max-width: 100%;
    }

    .message.success {
        color: limegreen;
        font-weight: bold;
    }

    .message.error {
        color: red;
        font-weight: bold;
    }

    .btn-primary,
    .btn-danger,
    .btn-submit {
        margin-top: 10px;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-primary {
        background: #0072ff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056cc;
    }

    .btn-danger {
        background: #ff3b3b;
        color: white;
    }

    .btn-danger:hover {
        background: #cc2a2a;
    }

    .btn-submit {
        background: #28a745;
        color: white;
        width: 100%;
        margin-top: 15px;
    }

    .btn-submit:hover {
        background: #1f7a33;
    }

    .report-overview {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        padding: 20px;
        width: 100%;
        max-width: 600px;
        color: #000;
    }

    .report-card {
        border-bottom: 1px solid #ccc;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .report-card img {
        max-width: 100%;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .no-reports {
        color: #fff;
        font-style: italic;
    }
    </style>

    <script>
    const video = document.getElementById("camera");
    const preview = document.getElementById("preview");
    const previewContainer = document.getElementById("preview-container");
    const imageDataInput = document.getElementById("image_data");

    navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user"
            }
        })
        .then(stream => video.srcObject = stream)
        .catch(err => alert("Camera access failed: " + err));

    function takePhoto() {
        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL("image/png");
        preview.src = dataUrl;
        previewContainer.style.display = "block";
        imageDataInput.value = dataUrl;
    }

    function deletePhoto() {
        preview.src = "";
        previewContainer.style.display = "none";
        imageDataInput.value = "";
    }

    function capturePhoto() {
        if (!imageDataInput.value) {
            alert("Please take a photo before submitting.");
        }
    }

    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
        document.querySelector(".hamburger").classList.toggle("open");
    }
    </script>

</body>

</html>