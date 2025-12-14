<?php
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'No status provided']);
    exit();
}

$status = $_POST['status'];
$allowed = ['available', 'not_available'];

if (!in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn_users->prepare("UPDATE users SET availability = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => $conn_users->error]);
    exit();
}

$stmt->bind_param("si", $status, $userId);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update']);
}
?>
