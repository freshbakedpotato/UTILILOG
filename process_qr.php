<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["qr_code"])) {
    $qr = trim($_POST["qr_code"]);

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE employee_id = ?");
    $stmt->bind_param("s", $qr);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        echo "✅ Welcome " . htmlspecialchars($user['username']) . "! Attendance recorded.";
    } else {
        echo "❌ Invalid QR Code!";
    }
} else {
    echo "No QR code received.";
}
