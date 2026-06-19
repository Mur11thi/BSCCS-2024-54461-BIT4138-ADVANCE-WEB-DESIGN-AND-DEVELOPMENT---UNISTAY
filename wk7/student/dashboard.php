<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../includes/layout.php';

auth_require('student');
$user = current_user();
layout_header('My Dashboard');

$my_bookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE student_id=?");
$my_bookings->execute([$user['id']]);
$total = $my_bookings->fetchColumn();

$pending = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE student_id=? AND status='pending'");
$pending->execute([$user['id']]);
$pending_count = $pending->fetchColumn();
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">My Bookings</div>
    <div class="stat-value"><?= $total ?></div>
  </div>
  <div class="stat-card gold">
    <div class="stat-label">Pending Approval</div>
    <div class="stat-value"><?= $pending_count ?></div>
  </div>
</div>

<p>Welcome, <?= htmlspecialchars($user['name']) ?>. Use the navigation to browse rooms and manage your bookings.</p>

<?php layout_footer(); ?>