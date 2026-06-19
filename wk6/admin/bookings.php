<?php


require_once __DIR__ . '/../config/connection.php';      
require_once __DIR__ . '/../includes/auth_check.php';     
require_once __DIR__ . '/../includes/layout.php';         

auth_require('admin');


if (isset($_GET['confirm'])) {
    $id = (int)$_GET['confirm'];
    $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?")
        ->execute([$id]);
    header('Location: bookings.php');
    exit;
}
if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")
        ->execute([$id]);
    header('Location: bookings.php');
    exit;
}


$all = $pdo->query("
    SELECT b.*, u.full_name, u.student_id AS s_id,
           r.room_number, r.room_type, bl.block_name
    FROM   bookings b
    JOIN   users  u  ON u.id = b.student_id
    JOIN   rooms  r  ON r.id = b.room_id
    JOIN   blocks bl ON bl.id = r.block_id
    ORDER  BY b.booked_at DESC
")->fetchAll();


layout_header('Manage Bookings');
?>

<h2>All Bookings</h2>

<?php if (empty($all)): ?>
  <p>No bookings found.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Student</th>
        <th>Student ID</th>
        <th>Room</th>
        <th>Block</th>
        <th>Semester</th>
        <th>Fee (KSH)</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($all as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['full_name']) ?></td>
          <td><?= htmlspecialchars($b['s_id']) ?></td>
          <td><?= htmlspecialchars($b['room_number']) ?> (<?= $b['room_type'] ?>)</td>
          <td><?= htmlspecialchars($b['block_name']) ?></td>
          <td><?= htmlspecialchars($b['semester']) ?></td>
          <td><?= number_format($b['total_fee'], 2) ?></td>
          <td>
            <span class="badge badge-<?= $b['status'] ?>">
              <?= ucfirst($b['status']) ?>
            </span>
          </td>
          <td>
            <?php if ($b['status'] === 'pending'): ?>
              <a href="?confirm=<?= $b['id'] ?>" class="btn btn-primary btn-sm">Confirm</a>
              <a href="?cancel=<?= $b['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php
layout_footer();