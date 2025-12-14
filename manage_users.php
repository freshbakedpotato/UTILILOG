<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['ajax'] ?? '') === 'delete') {
  header('Content-Type: application/json');
  $id = (int) ($_POST['id'] ?? 0);
  $deleted = false;

  if ($id > 0) {
    foreach ([$conn_admin, $conn_users] as $connTarget) {
      $stmt = $connTarget->prepare("DELETE FROM users WHERE id = ?");
      if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0)
          $deleted = true;
        $stmt->close();
      }
    }
  }

  echo json_encode(["success" => $deleted, "message" => $deleted ? "✅ User deleted permanently." : "❌ User not found."]);
  exit;
}

require_once __DIR__ . '/layout.php';

$error = '';
$msg = '';
$editUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['ajax'])) {
  $action = $_POST['action'] ?? '';
  $role = $_POST['role'] ?? 'user';
  $connTarget = ($role === 'admin') ? $conn_admin : $conn_users;

  if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($id && $username && $email) {
      if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $connTarget->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $password, $id);
      } else {
        $stmt = $connTarget->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $email, $id);
      }
      if ($stmt->execute())
        $msg = "✅ User updated successfully!";
      else
        $error = "❌ " . $stmt->error;
      $stmt->close();
    } else {
      $error = '❌ Missing fields for update.';
    }
  }
}

if (isset($_GET['edit_id'], $_GET['edit_role'])) {
  $editId = (int) $_GET['edit_id'];
  $editRole = $_GET['edit_role'];
  $connSel = ($editRole === 'admin') ? $conn_admin : $conn_users;
  $stmt = $connSel->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
  $stmt->bind_param("i", $editId);
  $stmt->execute();
  $editUser = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}

$admins = $conn_admin->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
$users = $conn_users->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
?>
<div class="container py-4">
  <h3>Manage Users</h3>

  <div id="msgBox">
    <?php if ($msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  </div>

  <?php if ($editUser): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Edit User #<?= (int) $editUser['id'] ?></h5>
        <form method="POST">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int) $editUser['id'] ?>">
          <input type="hidden" name="role" value="<?= htmlspecialchars($editUser['role']) ?>">

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" type="text" name="username" value="<?= htmlspecialchars($editUser['username']) ?>"
              required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>"
              required>
          </div>

          <div class="mb-3">
            <label class="form-label">New Password (optional)</label>
            <input class="form-control" type="password" name="password">
          </div>

          <button class="btn btn-primary" type="submit">Update</button>
          <a href="manage_users.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">Search Users:</label>
    <input id="search" class="form-control" placeholder="Search by username or email..." onkeyup="filterUsers()">
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-sm" id="usersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ([$admins, $users] as $rs): ?>
          <?php while ($row = $rs->fetch_assoc()): ?>
            <tr id="row-<?= (int) $row['id'] ?>">
              <td><?= (int) $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <?= $row['role'] === 'admin' ? '<span class="badge bg-primary">Admin</span>' : '<span class="badge bg-success">User</span>' ?>
              </td>
              <td>
                <a href="?edit_id=<?= (int) $row['id'] ?>&edit_role=<?= urlencode($row['role']) ?>"
                  class="btn btn-sm btn-info">Edit</a>
                <button class="btn btn-sm btn-danger"
                  onclick="deleteUser(<?= (int) $row['id'] ?>, '<?= $row['role'] ?>')">Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  function filterUsers() {
    const q = document.getElementById('search').value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(r => {
      const username = r.cells[1].innerText.toLowerCase();
      const email = r.cells[2].innerText.toLowerCase();
      r.style.display = (username.includes(q) || email.includes(q)) ? '' : 'none';
    });
  }

  function deleteUser(id, role) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    fetch('', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        ajax: 'delete',
        id,
        role
      })
    })
      .then(res => res.json())
      .then(data => {
        const msgBox = document.getElementById('msgBox');
        msgBox.innerHTML =
          `<div class="alert ${data.success ? 'alert-success' : 'alert-danger'}">${data.message}</div>`;
        if (data.success) document.getElementById('row-' + id)?.remove();
      })
      .catch(err => alert('Error deleting user: ' + err));
  }
</script>

<?php
$admins->free();
$users->free();
require_once __DIR__ . '/layout_end.php';
?>