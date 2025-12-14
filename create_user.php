<?php
require_once __DIR__ . '/layout.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (
        strlen($password_raw) < 8 || strlen($password_raw) > 16 ||
        !preg_match('/[A-Za-z]/', $password_raw) ||
        !preg_match('/[0-9]/', $password_raw) ||
        !preg_match('/[\W]/', $password_raw)
    ) {
        $msg = "❌ Password must be 8–16 characters long and include letters, numbers, and symbols.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $conn = ($role === 'admin') ? $conn_admin : $conn_users;

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        if ($stmt) {
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            if ($stmt->execute()) {
               
                header("Location: dashboard.php?created=1");
                exit();
            } else {
                $msg = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "❌ Database error: " . $conn->error;
        }
    }
}
?>

<div class="container py-4">
  <h3>Create User</h3>

  <?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'✅')!==false ? 'alert-success' : 'alert-danger' ?>">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <form method="POST" onsubmit="return validatePassword()" class="w-100" style="max-width:720px;">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input class="form-control" type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <input class="form-control" type="password" id="password" name="password" minlength="8" maxlength="16" placeholder="8–16 chars with letters, numbers & symbols" required>
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
          <i id="pwIcon" class="bi bi-eye-slash"></i>
        </button>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Role</label>
      <select class="form-select" name="role">
        <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>

    <button class="btn btn-primary" type="submit">Create</button>
    <a class="btn btn-secondary ms-2" href="dashboard.php">Back</a>
  </form>
</div>

<script>
function validatePassword(){
  const pwd = document.getElementById('password').value;
  const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,16}$/;
  if (!regex.test(pwd)) {
    alert('Password must be 8–16 characters long and include letters, numbers, and symbols.');
    return false;
  }
  return true;
}
function togglePassword(){
  const inp = document.getElementById('password');
  const icon = document.getElementById('pwIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.classList.replace('bi-eye-slash','bi-eye');
  } else {
    inp.type = 'password';
    icon.classList.replace('bi-eye','bi-eye-slash');
  }
}
</script>

<?php require_once __DIR__ . '/layout_end.php'; ?>
