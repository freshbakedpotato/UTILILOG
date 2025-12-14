<?php
session_start();
require "db.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];


    $stmt = $conn_users->prepare("
        UPDATE status_logs 
        SET end_time = NOW() 
        WHERE user_id = ? AND end_time IS NULL
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn_users->prepare("
        INSERT INTO status_logs (user_id, status, start_time) VALUES (?, ?, NOW())
    ");
    $stmt->bind_param("is", $userId, $newStatus);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn_users->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);
    $stmt->execute();
    $stmt->close();


    header("Location: status.php");
    exit();
}

// 📥 Fetch User Info
$stmt = $conn_users->prepare("SELECT username, profile_image, status FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();


$stmt = $conn_users->prepare("
    SELECT status, start_time, end_time, 
           TIMESTAMPDIFF(SECOND, start_time, COALESCE(end_time, NOW())) as duration_sec
    FROM status_logs 
    WHERE user_id = ? AND DATE(start_time) = CURDATE()
    ORDER BY start_time DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$logs = $stmt->get_result();

$logsData = [];
$totalSeconds = 0;
while ($row = $logs->fetch_assoc()) {
    $totalSeconds += $row['duration_sec'];
    $logsData[] = $row;
}
$totalHoursDecimal = number_format($totalSeconds / 3600, 2);
$pageTitle = "My Status";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .status-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .status-btn {
        padding: 20px;
        border: none;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .status-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(86, 115, 177, 0.3);
    }


    .status-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }


    .status-desc {
        font-size: 0.85rem;
        opacity: 0.9;
        font-weight: normal;
    }


    .btn-present {
        background-color: #023618ff;
    }


    .btn-onduty {
        background-color: #04434bff;
    }


    .btn-break {
        background-color: #4b3005ff;
    }


    .btn-excused {
        background-color: #4b3005ff;
    }


    .btn-absent {
        background-color: #530f08ff;
    }

    .active-status {
        border: 3px solid #fff;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .log-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .log-table th,
    .log-table td {
        padding: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: left;
    }

    .log-table th {
        color: #00bcd4;
    }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="brand-section">
            <button class="hamburger" onclick="toggleSidebar()">
                <span></span><span></span><span></span>
            </button>
            <h1 class="brand">UtiliLog</h1>
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
            <li><a href="scanner.php">Task Scanner</a></li>
            <li><a href="status.php" class="active">Status</a></li>
            <li><a href="report.php">Report</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h2>Update Your Status</h2>

        <div class="card" style="text-align:center; padding: 15px;">
            <h3>Current Status: <span style="color:#00bcd4"><?= htmlspecialchars($user['status'] ?? 'Unknown') ?></span>
            </h3>
        </div>

        <div class="status-container">

            <button onclick="confirmUpdate('Present', 'I am currently working', '#06a348ff')"
                class="status-btn btn-present">
                <span class="status-title">Present</span>
                <span class="status-desc">I am currently working</span>
            </button>

            <button onclick="confirmUpdate('On Duty', 'Currently on active duty', '#088697ff')"
                class="status-btn btn-onduty">
                <span class="status-title">On Duty</span>
                <span class="status-desc">Currently on active duty</span>
            </button>

            <button onclick="confirmUpdate('On Break', 'Taking a break', '#945e07ff')" class="status-btn btn-break">
                <span class="status-title">On Break</span>
                <span class="status-desc">Taking a break</span>
            </button>

            <button onclick="confirmUpdate('Excused', 'Excused absence', '#945e07ff')" class="status-btn btn-excused">
                <span class="status-title">Excused</span>
                <span class="status-desc">Excused absence</span>
            </button>

            <button onclick="confirmUpdate('Absent', 'Not available today', '8b1a0dff')" class="status-btn btn-absent">
                <span class="status-title">Absent</span>
                <span class="status-desc">Not available today</span>
            </button>

        </div>

        <div class="card">
            <h3>Today's Activity (<?= $totalHoursDecimal ?> hrs)</h3>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logsData) > 0): ?>
                    <?php foreach ($logsData as $row):
                            $start = date("h:i A", strtotime($row['start_time']));
                            $end = $row['end_time'] ? date("h:i A", strtotime($row['end_time'])) : 'Now';

                            $seconds = $row['duration_sec'];
                            $hours = floor($seconds / 3600);
                            $mins = floor(($seconds % 3600) / 60);
                            $duration = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                            ?>
                    <tr>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $start ?> - <?= $end ?></td>
                        <td><?= $duration ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">No status changes recorded today.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" id="statusForm" style="display:none;">
            <input type="hidden" name="status" id="statusInput">
        </form>

    </main>

    <script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
        document.querySelector(".hamburger").classList.toggle("open");
    }

    function confirmUpdate(status, description, color) {
        Swal.fire({
            title: status,
            text: description,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#444',
            background: '#022c36',
            color: '#fff',
            confirmButtonText: 'Confirm Status'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('statusInput').value = status;
                document.getElementById('statusForm').submit();
            }
        })
    }
    </script>
</body>

</html>