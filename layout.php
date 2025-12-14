<?php
session_start();
require_once __DIR__ . '/../db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root { --sidebar-width: 250px; }
    body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; overflow-x:hidden; }
    
    .sidebar {
      width: var(--sidebar-width);
      position: fixed;
      top: 0; left: 0; bottom: 0;
      background:#0d6efd; color:#fff; padding:18px 10px;
      transition: all 0.28s ease;
      z-index: 1050;
    }
    .sidebar .brand { font-weight:700; margin-bottom:14px; text-align:center; }
    .sidebar a { color: rgba(255,255,255,0.95); text-decoration:none; display:block; padding:10px 14px; border-radius:6px; margin-bottom:4px; }
    .sidebar a:hover { background: rgba(255,255,255,0.08); color:#fff; }
    .sidebar a.active { background: rgba(255,255,255,0.14); font-weight:600; }

    .content-wrap { margin-left: calc(var(--sidebar-width) + 0px); padding: 18px; transition: margin-left 0.28s ease; }
    .topbar { margin-left: calc(var(--sidebar-width)); transition: margin-left 0.28s ease; }

    .sidebar.collapsed { left: calc(-1 * var(--sidebar-width)); }
    .content-wrap.full { margin-left: 0; }
    .topbar.full { margin-left: 0; }

    #overlay {
      display:none; position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(0,0,0,0.45); z-index:1040;
    }

    @media (max-width: 992px) {
      .sidebar { left: calc(-1 * var(--sidebar-width)); }
      .content-wrap { margin-left: 0; }
      .topbar { margin-left: 0; }
      .sidebar-visible { left: 0; }
    }

    .small-card { border-radius:8px; padding:12px; color:#fff; }
    .table-responsive { overflow-x:auto; }
  </style>
</head>
<body>

 
  <div class="sidebar" id="sidebar">
    <div class="brand"><i class="bi bi-shield-lock-fill"></i> ADMIN</div>

    <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
      <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
    <a href="create_user.php" class="<?= $currentPage === 'create_user.php' ? 'active' : '' ?>">
      <i class="bi bi-person-plus me-2"></i> Create User
    </a>
    <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>">
      <i class="bi bi-people me-2"></i> Manage Users
    </a>
    <a href="attendance.php" class="<?= $currentPage === 'attendance.php' ? 'active' : '' ?>">
      <i class="bi bi-calendar-check me-2"></i> Attendance
    </a>
    <a href="availability.php" class="<?= $currentPage === 'availability.php' ? 'active' : '' ?>">
      <i class="bi bi-clock-history me-2"></i> Availability
    </a>
    <a href="tasks.php" class="<?= $currentPage === 'tasks.php' ? 'active' : '' ?>">
      <i class="bi bi-list-check me-2"></i> Tasks
    </a>
    
    <a href="admin_reports.php" class="<?= $currentPage === 'admin_reports.php' ? 'active' : '' ?>">
      <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
    </a>

    <hr style="border-color: rgba(255,255,255,0.08);">

    <a href="logout.php" class="text-danger">
      <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
  </div>

  <div id="overlay"></div>


  <nav class="navbar navbar-dark bg-dark topbar" id="topbar">
    <div class="container-fluid">
      <div class="d-flex align-items-center">
        <button class="btn btn-outline-light me-2 d-lg-none" id="toggleBtn" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h5">Admin System</span>
      </div>

      <div class="d-flex align-items-center">
        <span class="me-3 text-light">
          Hello, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
        </span>
      </div>
    </div>
  </nav>

  
  <main class="content-wrap" id="content-wrap">
