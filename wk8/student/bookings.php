<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/layout.php';

auth_require('student');
$user = current_user();

$msg = '';


if (($_GET['action'] ?? '') === 'cancel' && isset($_GET['id'])) {
    $check = $pdo->prepare(
        "SELECT id FROM bookings WHERE id = ? AND student_id = ? AND status = 'pending'"
    );
    $check->execute([(int)$_GET['id'], $user['id']]);
    if ($check->fetch()) {
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")
            ->execute([(int)$_GET['id']]);
        $msg = 'Booking cancelled.';
    } else {
        $msg = 'Cannot cancel — not found or already processed.';
    }
}


$stmt = $pdo->prepare("
    SELECT b.*, r.room_number, r.room_type, bl.block_name
    FROM   bookings b
    JOIN   rooms  r  ON r.id  = b.room_id
    JOIN   blocks bl ON bl.id = r.block_id
    WHERE  b.student_id = :uid
    ORDER  BY b.booked_at DESC
");
$stmt->execute([':uid' => $user['id']]);
$bookings = $stmt->fetchAll();

layout_header('My Bookings');
?>

<?php if ($msg): ?>
  <div class="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>My Booking History</h2>

<?php if (empty($bookings)): ?>
  <p>You have no bookings yet. <a href="/UNISTAY/wk7/student/rooms.php">Browse rooms</a> to book.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Room</th>
        <th>Block</th>
        <th>Semester</th>
        <th>Fee (KSH)</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['room_number']) ?> (<?= $b['room_type'] ?>)</td>
          <td><?= htmlspecialchars($b['block_name']) ?></td>
          <td><?= htmlspecialchars($b['semester']) ?></td>
          <td><?= number_format($b['total_fee'], 2) ?></td>
          <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
          <td>
            <?php if ($b['status'] === 'pending'): ?>
              <a href="?action=cancel&id=<?= $b['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Cancel this booking?');">Cancel</a>
            <?php else: echo '—'; endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php
layout_footer();