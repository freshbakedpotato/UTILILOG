<?php
require_once __DIR__ . '/layout.php';

$pageTitle = "Attendance Overview";


$query = "
    SELECT u.id, u.username, u.email,
           COALESCE(
               (SELECT status FROM user_logs 
                WHERE user_id = u.id 
                AND DATE(log_time) = CURDATE() 
                ORDER BY log_time DESC LIMIT 1),
               'Absent'
           ) AS current_status
    FROM users u
";

$users = $conn_users->query($query);
if (!$users) {
    die("Query failed: " . $conn_users->error);
}

$totalUsers = $users->num_rows;
$presentCount = 0;
$todayHours = 0; 
$weekHours = 0; 

$usersData = [];
while ($row = $users->fetch_assoc()) {
    $usersData[] = $row;
    if (in_array($row['current_status'], ['Present', 'On Duty'])) {
        $presentCount++;
    }
}

$attendanceRate = $totalUsers > 0 ? round(($presentCount / $totalUsers) * 100, 1) : 0;
?>

<div class="container-fluid py-4">
  <h3 class="mb-4"><i class="bi bi-graph-up"></i> <?= $pageTitle ?></h3>

 
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-2">Today's Active</h6>
          <h4 class="fw-bold text-success"><?= $presentCount ?></h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-2">Hours Today</h6>
          <h4 class="fw-bold"><?= $todayHours ?> hrs</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-2">Hours This Week</h6>
          <h4 class="fw-bold"><?= $weekHours ?> hrs</h4>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-2">Attendance Rate</h6>
          <h4 class="fw-bold text-primary"><?= $attendanceRate ?>%</h4>
        </div>
      </div>
    </div>
  </div>


  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3"><i class="bi bi-person-lines-fill"></i> Today's User Status</h5>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Email</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($usersData as $user): ?>
              <?php
                $status = $user['current_status'];
                $badgeClass = match($status) {
                    'Present' => 'success',
                    'On Duty' => 'info',
                    'On Break' => 'warning text-dark',
                    'Excuse'  => 'secondary',
                    default   => 'danger'
                };
              ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_end.php'; ?>
