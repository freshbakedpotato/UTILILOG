<?php
$host = "localhost";
$user = "root";
$pass = "";


$db_users = "register_db";
$conn_users = new mysqli($host, $user, $pass, $db_users);
if ($conn_users->connect_error) {
    die("Connection to register_db failed: " . $conn_users->connect_error);
}


$db_admin = "admin_system";
$conn_admin = new mysqli($host, $user, $pass, $db_admin);
if ($conn_admin->connect_error) {
    die("Connection to admin_system failed: " . $conn_admin->connect_error);
}
?>
