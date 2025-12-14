<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php'); // <-- redirects to admin login
    exit();
}

$error = '';
$msg = '';


if (!isset($_GET['id'], $_GET['role'])) {
    die("Missing parameters.");
}

$userId = (int) $_GET['id'];
$role = $_GET['role'];


$conn = $role === 'admin' ? $conn_admin : $conn_users;


$stmt = $conn->prepare("SELECT username, email, role, is_active FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    
    if (!empty($_POST['password'])) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, is_active=? WHERE id=?");
        $stmt->bind_param("sssii", $username, $email, $passwordHash, $is_active, $userId);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, is_active=? WHERE id=?");
        $stmt->bind_param("ssii", $username, $email, $is_active, $userId);
    }

    if ($stmt->execute()) {
        $msg = "✅ User updated successfully!";
    } else {
        $error = "❌ Update failed: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<header>
    <h1>Edit User</h1>
    <div class="top-links">
        <a href="manage_users.php">← Manage Users</a>
        <a href="../dashboard.php">Back</a>
    </div>
</header>

<div class="container">
    <?php if ($msg): ?>
        <p style="color: lightgreen;"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: #ff7070;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password"><br><br>

        <label>
            <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>> Active
        </label><br><br>

        <button type="submit" class="action-btn">💾 Update</button>
    </form>
</div>

<footer>
    <p>&copy; <?= date("Y") ?> Admin System</p>
</footer>
</body>
</html>
