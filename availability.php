<?php
require_once __DIR__ . '/layout.php';

$conn = $conn_users;
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['availability'])) {
    $userId = (int)$_POST['user_id'];
    $availability = $_POST['availability'] === 'available' ? 'available' : 'not available';

    $stmt = $conn->prepare("UPDATE users SET availability = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $availability, $userId);
        $stmt->execute();
        $stmt->close();
        $notice = "✅ Availability updated for user ID: $userId";
    } else {
        $notice = "❌ Update failed: " . $conn->error;
    }
}

$usersRes = $conn->query("
    SELECT id, username, email, availability 
    FROM users 
    WHERE role = 'user' 
    ORDER BY id ASC
");

if (!$usersRes) {
    die("❌ Query failed: " . $conn->error);
}
?>

<div class="container py-4">
  <h3 class="mb-4"><i class="bi bi-person-check"></i> User Availability</h3>

  <?php if ($notice): ?>
    <div class="alert <?= str_contains($notice, '✅') ? 'alert-success' : 'alert-danger' ?>">
      <?= htmlspecialchars($notice) ?>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="mb-3"><i class="bi bi-list-ul"></i> Availability Status</h5>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Email</th>
              <th>Availability</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($usersRes->num_rows > 0): ?>
              <?php while ($user = $usersRes->fetch_assoc()): ?>
                <?php
                  $isAvailable = ($user['availability'] === 'available');
                  $badgeClass = $isAvailable ? 'success' : 'secondary';
                  $label = $isAvailable ? 'Available' : 'Not Available';
                ?>
                <tr>
                  <td><?= (int)$user['id'] ?></td>
                  <td><?= htmlspecialchars($user['username']) ?></td>
                  <td><?= htmlspecialchars($user['email']) ?></td>
                  <td><span class="badge bg-<?= $badgeClass ?>"><?= $label ?></span></td>
                  <td>
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                      <input type="hidden" name="availability" value="<?= $isAvailable ? 'not available' : 'available' ?>">
                      <button class="btn btn-sm <?= $isAvailable ? 'btn-outline-secondary' : 'btn-outline-success' ?>" type="submit">
                        <?= $isAvailable ? '<i class="bi bi-x-circle"></i> Set Not Available' : '<i class="bi bi-check-circle"></i> Set Available' ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_end.php'; ?>
