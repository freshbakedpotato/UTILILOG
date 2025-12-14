<?php
session_start();


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require "db.php";

$error = "";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $user = null;

    $stmt = $conn_admin->prepare("SELECT id, username, email, password, role, is_active FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $stmt = $conn_users->prepare("SELECT id, username, email, password, role, is_active FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }

    if ($user && $user['is_active'] == 1 && password_verify($password, $user['password'])) {

        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email/password or inactive account.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        width: 100%;
        padding-right: 40px;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #b8d8e0;
    }

    .toggle-password:hover {
        color: #00bcd4;
    }
    </style>
</head>

<body>

    <div class="main-layout">

        <div class="logo-title">
            <img src="uploads/um-logo.png" alt="Logo">
            <h1>Admin Portal</h1>
            <p>Manage users, tasks, and reports in one place.</p>
        </div>

        <div class="container">
            <h2>Admin Login</h2>
            <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>

                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© <?= date("Y") ?> Admin Panel. All rights reserved.</p>
    </footer>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.querySelector(".toggle-password i");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        }
    }
    </script>
</body>

</html>