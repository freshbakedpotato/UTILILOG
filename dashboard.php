<?php
require_once __DIR__ . '/layout.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); 
    exit();
}
function safe_prepare($conn, $sql, $context = '') {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("❌ SQL Prepare failed in $context: " . $conn->error . "<br>Query: $sql");
    }
    return $stmt;
}

$totalUsers = 0;

$stmt = safe_prepare($conn_admin, "SELECT COUNT(*) AS total FROM users", "count admins");
$stmt->execute();
$totalUsers += (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = safe_prepare($conn_users, "SELECT COUNT(*) AS total FROM users", "count users");
$stmt->execute();
$totalUsers += (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$allUsers = [];
$admins = $conn_admin->query("SELECT id, username, email, role, created_at FROM users");
$users  = $conn_users->query("SELECT id, username, email, role, created_at FROM users");

foreach ([$admins, $users] as $rs) {
    while ($row = $rs->fetch_assoc()) {
        $allUsers[] = $row;
    }
}
$admins->free();
$users->free();


usort($allUsers, function($a, $b) {
    return $a['id'] - $b['id'];
});
?>

<div class="container py-4">
  <h3>Dashboard Overview</h3>

  <div class="row g-3 mb-4">
    <div class="col-md-12">
      <div class="small-card bg-primary text-white p-3 rounded shadow-sm">
        <div>Total Users</div>
        <div class="h3"><?= number_format($totalUsers) ?></div>
        <small>All registered users</small>
      </div>
    </div>
  </div>

  <h5>All Users</h5>
  <?php if (!empty($allUsers)): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-sm mt-2">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Date Registered</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allUsers as $user): ?>
            <tr>
              <td><?= (int)$user['id'] ?></td>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
              <td><?= date("M d, Y", strtotime($user['created_at'] ?? date('Y-m-d'))) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-muted">No users found.</p>
  <?php endif; ?>
</div>

<script>
  setInterval(() => {
    location.reload();
  }, 5000);
</script>

<?php require_once __DIR__ . '/layout_end.php'; ?>
