<?php

require_once __DIR__ . '/../config/connection.php';      
require_once __DIR__ . '/../includes/auth_check.php';     
require_once __DIR__ . '/../includes/layout.php';         
auth_require('admin');

$action = $_GET['action'] ?? 'list';
$msg    = '';

// ── DELETE 
if ($action === 'delete' && isset($_GET['id'])) {
    $check = $pdo->prepare(
        "SELECT COUNT(*) FROM bookings
         WHERE room_id = ? AND status IN ('pending','confirmed')"
    );
    $check->execute([(int)$_GET['id']]);
    if ($check->fetchColumn() > 0) {
        $msg = 'Cannot delete: room has active bookings.';
    } else {
        $pdo->prepare('DELETE FROM rooms WHERE id = ?')
            ->execute([(int)$_GET['id']]);
        $msg = 'Room deleted.';
    }
}

// ── UPDATE STATUS 
if ($action === 'setstatus' && isset($_GET['id'], $_GET['status'])) {
    $allowed = ['available', 'full', 'maintenance'];
    $status  = in_array($_GET['status'], $allowed) ? $_GET['status'] : 'available';
    $pdo->prepare('UPDATE rooms SET status = ? WHERE id = ?')
        ->execute([$status, (int)$_GET['id']]);
}

// ── CREATE (form POST) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $block_id    = (int)$_POST['block_id'];
    $room_number = htmlspecialchars(trim($_POST['room_number']));
    $room_type   = $_POST['room_type'];
    $capacity    = (int)$_POST['capacity'];
    $price       = (float)$_POST['price_per_semester'];

    $pdo->prepare(
        'INSERT INTO rooms (block_id, room_number, room_type, capacity, price_per_semester)
         VALUES (?, ?, ?, ?, ?)'
    )->execute([$block_id, $room_number, $room_type, $capacity, $price]);
    $msg = 'Room added successfully.';
}

// ── READ (ALL)
$rooms = $pdo->query("
    SELECT r.*, b.block_name
    FROM   rooms  r
    JOIN   blocks b ON b.id = r.block_id
    ORDER  BY b.block_name, r.room_number
")->fetchAll();

layout_header('Manage Rooms');
?>

<?php if ($msg): ?>
  <div class="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>Rooms</h2>

<table>
  <thead>
    <tr>
      <th>Room</th>
      <th>Block</th>
      <th>Type</th>
      <th>Price (KSH)</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rooms as $room): ?>
      <tr>
        <td><?= htmlspecialchars($room['room_number']) ?></td>
        <td><?= htmlspecialchars($room['block_name']) ?></td>
        <td><?= ucfirst($room['room_type']) ?></td>
        <td><?= number_format($room['price_per_semester'], 2) ?></td>
        <td>
          <form method="get" style="display:inline;">
            <input type="hidden" name="action" value="setstatus">
            <input type="hidden" name="id" value="<?= $room['id'] ?>">
            <select name="status" onchange="this.form.submit()" class="form-control" style="width:auto; display:inline;">
              <option value="available"   <?= $room['status'] === 'available'   ? 'selected' : '' ?>>Available</option>
              <option value="full"        <?= $room['status'] === 'full'        ? 'selected' : '' ?>>Full</option>
              <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
          </form>
        </td>
        <td>
          <a href="?action=delete&id=<?= $room['id'] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete this room?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Add New Room</h2>
<form method="POST">
  <div class="form-group">
    <label for="block_id">Block</label>
    <select name="block_id" id="block_id" class="form-control" required>
      <option value="">-- Select block --</option>
      <?php
      $blocks = $pdo->query("SELECT id, block_name FROM blocks")->fetchAll();
      foreach ($blocks as $block):
      ?>
        <option value="<?= $block['id'] ?>"><?= htmlspecialchars($block['block_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label for="room_number">Room Number</label>
    <input type="text" name="room_number" id="room_number" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="room_type">Type</label>
    <select name="room_type" id="room_type" class="form-control" required>
      <option value="single">Single</option>
      <option value="double">Double</option>
      <option value="triple">Triple</option>
    </select>
  </div>
  <div class="form-group">
    <label for="capacity">Capacity</label>
    <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="1" required>
  </div>
  <div class="form-group">
    <label for="price_per_semester">Price per Semester (KSH)</label>
    <input type="number" name="price_per_semester" id="price_per_semester" class="form-control" step="0.01" required>
  </div>
  <button type="submit" class="btn btn-primary">Add Room</button>
</form>

<?php
layout_footer();