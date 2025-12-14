<?php
session_start();


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $user = null;


    $stmt = $conn_admin->prepare("SELECT id, username, email, password, role, is_active 
                                  FROM users WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }


    if (!$user) {
        $stmt = $conn_users->prepare("SELECT id, username, email, password, role, is_active 
                                      FROM users WHERE email = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        }
    }


    if ($user && $user['is_active'] == 1 && password_verify($password, $user['password'])) {


        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: Admin_System/dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password, or account deactivated.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UtiliLog</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .password-wrapper {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #555;
    }
    </style>
</head>

<body>

    <header>
        <div class="top-links">
            <a href="about.php">About</a> | <a href="contact.php">Contact</a>
        </div>
    </header>

    <section class="main-layout">
        <div class="logo-title">
            <img src="uploads/um-logo.png" alt="UM Logo">
            <h1>UtiliLog</h1>
            <p>Ready to Work?</p>
        </div>

        <main class="container">
            <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="email">Employee ID</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fa fa-eye-slash"></i>
                    </span>
                </div>

                <button type="submit" class="btn-primary">Log In</button>
            </form>

            <div class="divider"><span>OR</span></div>

            <button type="button" class="btn-secondary">
                <a href="loginscanner.php">Scan QR Code to Login</a>
            </button>
        </main>
    </section>

    <footer>
        <p>© <?= date("Y") ?> UtiliLog. All Rights Reserved.</p>
        <p>Developed for the University of Mindanao, Visayan Campus, Tagum City</p>
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