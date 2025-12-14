<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_name'])) {
    $taskName = trim($_POST['task_name']);
    $userId = $_SESSION['user_id'];

    if ($taskName !== '') {
        $status = (stripos($taskName, 'done') !== false) ? 'done' : 'pending';

        $stmt = $conn_users->prepare("
            INSERT INTO task_logs (user_id, task_name, status, scanned_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("iss", $userId, $taskName, $status);

        if ($stmt->execute()) {
            echo "✅Task saved and logged successfully!";
        } else {
            echo " Database error: " . $conn_users->error;
        }
    } else {
        echo " Invalid task name.";
    }
} else {
    echo " No task received.";
}
?>