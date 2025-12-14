<?php
session_start();
require "db.php"; 

$db_error = null;


if (!isset($conn) || !($conn instanceof mysqli)) {
    if (isset($conn_users) && ($conn_users instanceof mysqli)) {
        $conn = $conn_users;
    } elseif (isset($conn_admin) && ($conn_admin instanceof mysqli)) {
        $conn = $conn_admin;
    } else {
        $conn = null;
        $db_error = "Database connection not found. Please check db.php.";
    }
}

function safe_prepare($conn, $sql) {
    if (!($conn instanceof mysqli)) return false;
    $stmt = @$conn->prepare($sql);
    return $stmt ?: false;
}


function recordAttendance($conn, $userId) {
    if (!($conn instanceof mysqli)) return false;

    $today = date("Y-m-d");

    $stmt = safe_prepare($conn, "SELECT id FROM attendance WHERE user_id = ? AND `DATE` = ?");
    if (!$stmt) {
        error_log("recordAttendance SELECT prepare failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $insert = safe_prepare($conn, "INSERT INTO attendance (user_id, `DATE`, time_in) VALUES (?, ?, CURTIME())");
        if ($insert) {
            $insert->bind_param("is", $userId, $today);
            $insert->execute();
            $insert->close();
        } else {
            error_log("recordAttendance INSERT prepare failed: " . $conn->error);
        }
    }

    $stmt->close();
    return true;
}

$scan_message = null;


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["qr_code"])) {
    if (!($conn instanceof mysqli)) {
        $scan_message = "Database connection unavailable; cannot validate QR.";
    } else {
        $qr_code = trim($_POST["qr_code"]);

       
        $stmt = safe_prepare($conn, "SELECT id, username, email, role FROM users WHERE email = ? LIMIT 1");

        if (!$stmt) {
            $scan_message = "Database error during user lookup.";
            error_log("User lookup prepare failed: " . $conn->error);
        } else {
            $stmt->bind_param("s", $qr_code);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $username, $email, $role);
                $stmt->fetch();

                
                recordAttendance($conn, $id);

                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;
                $_SESSION["role"] = $role;

                echo "<script>
                    alert('✅ Welcome {$username}! Login successful.');
                    window.location.href='dashboard.php';
                </script>";
                exit();
            } else {
                $scan_message = "❌ Invalid QR Code — no matching user found!";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>QR Code Login</title>
  <link rel="stylesheet" href="loginscanner.css">
  <script src="https://unpkg.com/html5-qrcode"></script>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
</head>
<body>
  <div class="scanner-container">
    <div class="left-panel">
      <h2>QR Code Login</h2>

      <?php if ($db_error): ?>
        <div class="notice error">
          <?php echo htmlspecialchars($db_error); ?>
        </div>
      <?php endif; ?>

      <div class="tip-box">
         Point your camera at the QR Code that contains your <b>email address</b>.
      </div>

      <div id="reader" class="scanner-box" aria-label="QR scanner preview"></div>
    </div>

    <div class="right-panel">
      <h3>How to Scan</h3>
      <div class="steps">
        <div class="step"><span>1</span> Hold your printed QR code steady</div>
        <div class="step"><span>2</span> Align it within the frame</div>
        <div class="step"><span>3</span> Wait for automatic detection and login</div>
      </div>

      <a href="login.php" class="btn-return">Return</a>

      <?php if (!empty($scan_message)): ?>
        <div class="notice error"><?php echo htmlspecialchars($scan_message); ?></div>
      <?php endif; ?>
    </div>
  </div>

  <form id="qrForm" method="POST" style="display:none;">
    <input type="hidden" name="qr_code" id="qrInput">
  </form>

  <script>
    function onScanSuccess(decodedText) {
      console.log("✅ Scanned QR content:", decodedText);
      document.getElementById('qrInput').value = decodedText.trim();
      document.getElementById('qrForm').submit();
    }

    function onScanFailure(error) {
      
    }

  
    (function startScanner() {
      const html5QrCode = new Html5Qrcode("reader");

      function startCamera() {
        html5QrCode.start(
          { facingMode: "environment" }, 
          { fps: 10, qrbox: 250 },
          onScanSuccess,
          onScanFailure
        ).catch(err => {
          console.error("Camera start failed:", err);
          alert("⚠️ Unable to access your camera. Please allow camera permissions and refresh the page.");
        });
      }

      startCamera();

     
      document.addEventListener("visibilitychange", () => {
        if (!document.hidden) startCamera();
      });
    })();
  </script>
</body>
</html>
