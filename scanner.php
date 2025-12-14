<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn_users->prepare("SELECT username, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$pageTitle = "Task Scanner";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
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
      <li><a href="account_info.php">Profile</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="qrcode.php">QrCode</a></li>
      <li><a href="scanner.php" class="active">Task Scanner</a></li>
      <li><a href="status.php">Status</a></li>
      <li><a href="report.php">Report</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>

  <main class="main-content">
    <h2>Task Scanner</h2>

    <div class="card">
      <p style="text-align:center; margin-bottom:20px;">Align the QR code within the frame below.</p>

      <div class="scanner-container">
        <video id="video" autoplay playsinline hidden></video>
        <canvas id="canvas" hidden></canvas>
        <div id="result"
          style="margin-top:20px; font-weight:bold; font-size:1.1rem; padding:10px; background:#043645; border-radius:4px;">
          Initializing camera...</div>
      </div>
    </div>
  </main>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
    }

    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const context = canvas.getContext("2d");
    const resultDiv = document.getElementById("result");

    let scanningLocked = false;
    let lastScanned = "";

    navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: "environment"
      }
    })
      .then(stream => {
        video.srcObject = stream;
        video.setAttribute("playsinline", true);
        video.play();
        requestAnimationFrame(scanFrame);
      })
      .catch(err => {
        console.error(err);
        resultDiv.innerText = " Camera access denied.";
        resultDiv.style.color = "#ff5252";
      });

    function scanFrame() {
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.hidden = false;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        if (canvas.width > 400) {
          canvas.style.width = "100%";
          canvas.style.maxWidth = "400px";
        }

        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        if (scanningLocked) {
          requestAnimationFrame(scanFrame);
          return;
        }

        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert"
        });

        if (code && code.data !== lastScanned) {
          lastScanned = code.data;
          scanningLocked = true;
          resultDiv.style.color = "#00bcd4";
          resultDiv.innerText = "✅ Scanned: " + code.data;
          playBeep();

          fetch("save_task.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "task_name=" + encodeURIComponent(code.data)
          })
            .then(res => res.text())
            .then(text => {
              resultDiv.style.color = text.includes("already") ? "#ffab40" : "#00e676";
              resultDiv.innerText = text;
              setTimeout(() => {
                scanningLocked = false;
                resultDiv.style.color = "#fff";
                resultDiv.innerText = "Ready to scan...";
              }, 3000);
            })
            .catch(err => {
              console.error(err);
              resultDiv.style.color = "#ff5252";
              resultDiv.innerText = "❌ Failed to save task.";
              scanningLocked = false;
            });
        }
      }
      requestAnimationFrame(scanFrame);
    }

    function playBeep() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = "sine";
        osc.frequency.value = 800;
        gain.gain.value = 0.1;
        osc.start();
        osc.stop(ctx.currentTime + 0.1);
      } catch (e) {
        console.log("Audio not supported");
      }
    }
  </script>
</body>

</html>