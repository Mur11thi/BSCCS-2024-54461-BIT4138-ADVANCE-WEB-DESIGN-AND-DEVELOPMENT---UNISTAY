<?php

require_once __DIR__ . '/../config/connection.php';   
require_once __DIR__ . '/../includes/auth_check.php';  
require_once __DIR__ . '/../includes/layout.php';      


if (!function_exists('layout_header')) {
    die("Error: layout_header() not defined. Check includes/layout.php exists and is correct.");
}


auth_require('admin');


layout_header('Admin Dashboard');


$total_rooms   = $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
$available     = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='available'")->fetchColumn();
$total_bookings= $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$pending       = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();


$recent = $pdo->query("
    SELECT b.*, u.full_name, u.student_id AS s_id,
           r.room_number, r.room_type
    FROM   bookings b
    JOIN   users  u ON u.id = b.student_id
    JOIN   rooms  r ON r.id = b.room_id
    ORDER  BY b.booked_at DESC
    LIMIT  10
")->fetchAll();
?>


<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Total Rooms</div>
    <div class="stat-value"><?= $total_rooms ?></div>
  </div>
  <div class="stat-card gold">
    <div class="stat-label">Available</div>
    <div class="stat-value"><?= $available ?></div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Pending Review</div>
    <div class="stat-value"><?= $pending ?></div>
  </div>
</div>

<h2>Recent Bookings</h2>
<table>
  <thead><tr><th>Student</th><th>Room</th><th>Status</th><th>Action</th></tr></thead>
  <tbody>
  <?php foreach ($recent as $b): ?>
    <tr>
      <td><?= htmlspecialchars($b['full_name']) ?></td>
      <td><?= $b['room_number'] ?> (<?= $b['room_type'] ?>)</td>
      <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
      <td>
        <?php if ($b['status'] === 'pending'): ?>
          <a href="?action=confirm&id=<?= $b['id'] ?>" class="btn btn-primary btn-sm">Confirm</a>
          <a href="?action=cancel&id=<?= $b['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
        <?php else: echo '—'; endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'confirm') {
        $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?")->execute([$id]);
    } elseif ($_GET['action'] === 'cancel') {
        $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$id]);
    }
    
    header('Location: dashboard.php');
    exit;
}


layout_footer();
?>