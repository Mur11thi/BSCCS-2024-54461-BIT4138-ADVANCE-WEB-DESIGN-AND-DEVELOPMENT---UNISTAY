<?php

require_once __DIR__ . '/../config/connection.php';      
require_once __DIR__ . '/../includes/auth_check.php';     
require_once __DIR__ . '/../includes/layout.php';         

auth_require('admin');

$msg    = '';
$action = $_GET['action'] ?? 'list';

//  DELETE 
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Prevent deleting yourself or the last admin 
    if ($id === $_SESSION['user_id']) {
        $msg = 'You cannot delete your own account.';
    } else {
        // Check for active bookings
        $check = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE student_id = ? AND status IN ('pending','confirmed')");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            $msg = 'Cannot delete: user has active bookings. Cancel them first.';
        } else {
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            $msg = 'User deleted.';
        }
    }
    // Redirect back to list view
    header('Location: users.php?msg=' . urlencode($msg));
    exit;
}

//  UPDATE (via POST) 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id         = (int)$_POST['id'];
    $full_name  = htmlspecialchars(trim($_POST['full_name'] ?? ''));
    $student_id = htmlspecialchars(trim($_POST['student_id'] ?? ''));
    $email      = htmlspecialchars(trim($_POST['email'] ?? ''));

    if (empty($full_name) || empty($student_id) || empty($email)) {
        $msg = 'All fields are required.';
    } else {
        // Check for duplicate student_id or email on *other* users
        $dup = $pdo->prepare('SELECT id FROM users WHERE (student_id = ? OR email = ?) AND id != ?');
        $dup->execute([$student_id, $email, $id]);
        if ($dup->fetch()) {
            $msg = 'Student ID or email already belongs to another user.';
        } else {
            $pdo->prepare('UPDATE users SET full_name = ?, student_id = ?, email = ? WHERE id = ?')
                ->execute([$full_name, $student_id, $email, $id]);
            $msg = 'User updated successfully.';
        }
    }
    header('Location: users.php?msg=' . urlencode($msg));
    exit;
}

//  CREATE (via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $full_name  = htmlspecialchars(trim($_POST['full_name'] ?? ''));
    $student_id = htmlspecialchars(trim($_POST['student_id'] ?? ''));
    $email      = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password   = $_POST['password'] ?? '';
    $role       = $_POST['role'] ?? 'student';   // default safety

    if (empty($full_name) || empty($student_id) || empty($email) || empty($password)) {
        $msg = 'All fields are required.';
    } elseif (strlen($password) < 6) {
        $msg = 'Password must be at least 6 characters.';
    } else {
        // Check for existing student_id or email
        $dup = $pdo->prepare('SELECT id FROM users WHERE student_id = ? OR email = ?');
        $dup->execute([$student_id, $email]);
        if ($dup->fetch()) {
            $msg = 'A user with that Student ID or email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (full_name, student_id, email, password, role) VALUES (?, ?, ?, ?, ?)')
                ->execute([$full_name, $student_id, $email, $hashed, $role]);
            $msg = 'User created successfully.';
        }
    }
    header('Location: users.php?msg=' . urlencode($msg));
    exit;
}

//  READ (fetch all users)
$users = $pdo->query('SELECT id, full_name, student_id, email, role FROM users ORDER BY role, full_name')->fetchAll();

//  Start output 
layout_header('Manage Users');
?>

<?php if (!empty($_GET['msg'])): ?>
  <div class="alert"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<h2>All Users</h2>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Student ID</th>
      <th>Email</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['full_name']) ?></td>
        <td><?= htmlspecialchars($u['student_id']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= ucfirst($u['role']) ?></td>
        <td>
          <a href="?action=edit&id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
          <a href="?action=delete&id=<?= $u['id'] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete this user?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php
//  Show edit form if ?action=edit&id=… 
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();
    if ($editUser):
?>
<h2>Edit User</h2>
<form method="POST">
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="id" value="<?= $editUser['id'] ?>">

  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control"
           value="<?= htmlspecialchars($editUser['full_name']) ?>" required>
  </div>
  <div class="form-group">
    <label>Student ID</label>
    <input type="text" name="student_id" class="form-control"
           value="<?= htmlspecialchars($editUser['student_id']) ?>" required>
  </div>
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="<?= htmlspecialchars($editUser['email']) ?>" required>
  </div>
  <button type="submit" class="btn btn-primary">Update User</button>
  <a href="users.php" class="btn">Cancel</a>
</form>
<?php endif; } ?>

<hr>

<h2>Add New User</h2>
<form method="POST">
  <input type="hidden" name="action" value="add">

  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Student ID</label>
    <input type="text" name="student_id" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Password (min 6 characters)</label>
    <input type="password" name="password" class="form-control" required minlength="6">
  </div>
  <div class="form-group">
    <label>Role</label>
    <select name="role" class="form-control">
      <option value="student">Student</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Create User</button>
</form>

<?php
layout_footer();