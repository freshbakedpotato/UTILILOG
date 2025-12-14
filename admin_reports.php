<?php
require_once __DIR__ . '/layout.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$pageTitle = "Reports Overview";


if (isset($_GET['delete'])) {
  $deleteId = intval($_GET['delete']);


  $photoQuery = $conn_users->prepare("SELECT photo FROM reports WHERE id = ?");
  $photoQuery->bind_param("i", $deleteId);
  $photoQuery->execute();
  $photoResult = $photoQuery->get_result()->fetch_assoc();
  if (!empty($photoResult['photo']) && file_exists(__DIR__ . '/../' . $photoResult['photo'])) {
    unlink(__DIR__ . '/../' . $photoResult['photo']);
  }


  $deleteStmt = $conn_users->prepare("DELETE FROM reports WHERE id = ?");
  $deleteStmt->bind_param("i", $deleteId);
  $deleteStmt->execute();

  echo "<script>
        alert('✅ Report deleted successfully.');
        window.location.href = window.location.pathname;
    </script>";
  exit();
}

$sql = "
    SELECT 
        r.id, r.photo, r.comment, r.created_at, 
        u.username
    FROM reports r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
";
$result = $conn_users->query($sql);
$reports = ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<div class="container-fluid py-4">
  <h3 class="mb-4"><i class="bi bi-file-earmark-bar-graph"></i> <?= $pageTitle ?></h3>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3"><i class="bi bi-person-lines-fill"></i> Submitted Reports</h5>

      <?php if (!empty($reports)): ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Comment</th>
                <th>Photo</th>
                <th>Date Submitted</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $index => $r): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><strong><?= htmlspecialchars($r['username']) ?></strong></td>
                  <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                  <td>
                    <?php if (!empty($r['photo']) && file_exists(__DIR__ . '/../' . $r['photo'])): ?>
                      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="Report Photo" width="80" class="rounded">
                    <?php else: ?>
                      <span class="text-muted fst-italic">No photo</span>
                    <?php endif; ?>
                  </td>
                  <td><?= date("M d, Y h:i A", strtotime($r['created_at'])) ?></td>
                  <td>
                    <?php if (!empty($r['photo']) && file_exists(__DIR__ . '/../' . $r['photo'])): ?>
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal"
                        data-username="<?= htmlspecialchars($r['username']) ?>"
                        data-comment="<?= htmlspecialchars($r['comment']) ?>"
                        data-date="<?= date("M d, Y h:i A", strtotime($r['created_at'])) ?>"
                        data-photo="../<?= htmlspecialchars($r['photo']) ?>">
                        <i class="bi bi-eye"></i> View
                      </button>
                    <?php else: ?>
                      <button class="btn btn-sm btn-secondary" disabled><i class="bi bi-eye-slash"></i></button>
                    <?php endif; ?>


                    <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this report?');">
                      <i class="bi bi-trash"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center text-muted fst-italic">No reports submitted yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>


<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewModalLabel"><i class="bi bi-person-bounding-box"></i> Report Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalPhoto" src="" alt="Report Photo" class="img-fluid rounded mb-3" style="max-height:400px;">
        <h5 id="modalUsername"></h5>
        <p id="modalComment" class="text-muted mt-2"></p>
        <small id="modalDate" class="text-secondary"></small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const viewModal = document.getElementById("viewModal");
    viewModal.addEventListener("show.bs.modal", event => {
      const button = event.relatedTarget;
      const username = button.getAttribute("data-username");
      const comment = button.getAttribute("data-comment");
      const date = button.getAttribute("data-date");
      const photo = button.getAttribute("data-photo");

      document.getElementById("modalUsername").textContent = username;
      document.getElementById("modalComment").textContent = comment;
      document.getElementById("modalDate").textContent = date;
      document.getElementById("modalPhoto").src = photo;
    });
  });
</script>

<?php require_once __DIR__ . '/layout_end.php'; ?>