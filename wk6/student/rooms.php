<?php

require_once __DIR__ . '/../config/connection.php';      
require_once __DIR__ . '/../includes/auth_check.php';     
require_once __DIR__ . '/../includes/layout.php';         

auth_require('student');
$user = current_user();

$msg = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id   = (int)($_POST['room_id'] ?? 0);
    $semester  = htmlspecialchars(trim($_POST['semester'] ?? ''));
    $check_in  = $_POST['check_in_date'] ?? '';
    $check_out = $_POST['check_out_date'] ?? '';
    $fee       = (float)($_POST['total_fee'] ?? 0);

    // Validate room availability
    $roomCheck = $pdo->prepare("SELECT status FROM rooms WHERE id = ?");
    $roomCheck->execute([$room_id]);
    $room = $roomCheck->fetch();

    // Check for duplicate booking in same semester
    $dup = $pdo->prepare(
        "SELECT id FROM bookings
         WHERE student_id = ? AND semester = ?
         AND status IN ('pending', 'confirmed')"
    );
    $dup->execute([$user['id'], $semester]);

    if (!$room || $room['status'] !== 'available') {
        $msg = 'Error: Room is no longer available.';
    } elseif ($dup->fetch()) {
        $msg = 'Error: You already have a booking for this semester.';
    } else {
        $pdo->prepare(
            "INSERT INTO bookings
             (student_id, room_id, semester, check_in_date, check_out_date, total_fee)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([$user['id'], $room_id, $semester, $check_in, $check_out, $fee]);
        $msg = 'Booking submitted successfully. Awaiting admin confirmation.';
    }
}

// Fetch available rooms
$rooms = $pdo->query("
    SELECT r.*, b.block_name
    FROM   rooms  r
    JOIN   blocks b ON b.id = r.block_id
    WHERE  r.status = 'available'
    ORDER  BY b.block_name, r.room_number
")->fetchAll();

layout_header('Browse Rooms');
?>

<?php if ($msg): ?>
  <div class="alert <?= strpos($msg, 'Error') === 0 ? 'alert-error' : '' ?>"
       style="<?= strpos($msg, 'Error') !== 0 ? 'background:#D4EDDA; color:#155724; border:1px solid #C3E6CB;' : '' ?>">
    <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<h2>Available Rooms</h2>

<?php if (empty($rooms)): ?>
  <p>No rooms are currently available. Please check again later.</p>
<?php else: ?>
  <div class="room-grid">
    <?php foreach ($rooms as $room): ?>
      <div class="room-card">
        <h3><?= htmlspecialchars($room['room_number']) ?></h3>
        <p><strong>Block:</strong> <?= htmlspecialchars($room['block_name']) ?></p>
        <p><strong>Type:</strong> <?= ucfirst($room['room_type']) ?></p>
        <p><strong>Capacity:</strong> <?= $room['capacity'] ?> person(s)</p>
        <p><strong>Price:</strong> KSH <?= number_format($room['price_per_semester'], 2) ?></p>
        <p><strong>Status:</strong> <span class="badge badge-<?= $room['status'] ?>"><?= ucfirst($room['status']) ?></span></p>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Booking Form -->
<h2>Book a Room</h2>
<form method="POST" class="booking-form">
  <div class="form-group">
    <label for="room_id">Select Room</label>
    <select name="room_id" id="room_id" class="form-control" required>
      <option value="">-- Choose a room --</option>
      <?php foreach ($rooms as $room): ?>
        <option value="<?= $room['id'] ?>">
          <?= $room['room_number'] ?> – <?= $room['room_type'] ?>
          (KSH <?= number_format($room['price_per_semester'], 2) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="semester">Semester</label>
    <input type="text" name="semester" id="semester"
           class="form-control" placeholder="e.g. 2026-09" required>
  </div>

  <div class="form-group">
    <label for="check_in_date">Check‑in Date</label>
    <input type="date" name="check_in_date" id="check_in_date"
           class="form-control" required>
  </div>

  <div class="form-group">
    <label for="check_out_date">Check‑out Date</label>
    <input type="date" name="check_out_date" id="check_out_date"
           class="form-control" required>
  </div>

  <div class="form-group">
    <label for="total_fee">Total Fee (KSH)</label>
    <input type="number" name="total_fee" id="total_fee"
           class="form-control" placeholder="e.g. 15000" step="0.01" required>
  </div>

  <button type="submit" class="btn btn-primary w-full">
    Submit Booking
  </button>
</form>

<?php
layout_footer();