<?php
require_once __DIR__ . '/layout.php';
$conn = $conn_users;
$points_per_task = 10;

$query = "
    SELECT 
        u.id, 
        u.username,
        COUNT(t.id) * $points_per_task AS total_points
    FROM users u
    LEFT JOIN task_logs t ON u.id = t.user_id
    WHERE u.role = 'user'   -- ✅ only regular users
    GROUP BY u.id, u.username
    ORDER BY total_points DESC, u.username ASC
";

$usersPoints = $conn->query($query);
?>

<div class="container py-4">
  <h3>User Points Table</h3>
  <p class="text-muted mb-3">Each scanned QR task is worth <?= $points_per_task ?> points.</p>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Total Points</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $rank = 1;
            if ($usersPoints && $usersPoints->num_rows > 0):
              while ($row = $usersPoints->fetch_assoc()):
                ?>
                <tr>
                  <td><?= $rank++ ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><strong><?= (int) $row['total_points'] ?></strong></td>
                </tr>
              <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="3" class="text-muted">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_end.php'; ?>