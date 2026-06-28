<?php
session_start();
require_once __DIR__ . '/config/connection.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'admin')
           ? '/UNISTAY/wk7/admin/dashboard.php'
           : '/UNISTAY/wk7/student/dashboard.php';
    header('Location: ' . $dest);
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitise and validate input
    $full_name  = htmlspecialchars(trim($_POST['full_name'] ?? ''));
    $student_id = htmlspecialchars(trim($_POST['student_id'] ?? ''));
    $email      = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($full_name) || empty($student_id) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check for duplicate student_id or email
        $check = $pdo->prepare('SELECT id FROM users WHERE student_id = :sid OR email = :email LIMIT 1');
        $check->execute([':sid' => $student_id, ':email' => $email]);
        if ($check->fetch()) {
            $error = 'Student ID or email is already registered.';
        } else {
            // Hash password and insert
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, student_id, email, password, role) VALUES (:name, :sid, :email, :pw, :role)'
            );
            $stmt->execute([
                ':name'  => $full_name,
                ':sid'   => $student_id,
                ':email' => $email,
                ':pw'    => $hashed,
                ':role'  => 'student'      // role is always 'student' for self-registration
            ]);
            $success = 'Registration successful! You can now <a href="/UNISTAY/wk5/login.php">log in</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register – MKU Hostel</title>
  <link rel="stylesheet" href="/UNISTAY/wk7/assets/js/css/main.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-top">
      <div class="auth-logo">🏛</div>
      <div class="auth-title">MKU Hostel System</div>
      <div class="auth-subtitle">Create a Student Account</div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert" style="background:#D4EDDA; color:#155724; border:1px solid #C3E6CB;">
        <?= $success ?>
      </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <div class="auth-body">
      <form method="POST" data-validate>
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name"
                 class="form-control" placeholder="e.g. Jane Doe" required>
        </div>

        <div class="form-group">
          <label for="student_id">Student ID</label>
          <input type="text" id="student_id" name="student_id"
                 class="form-control" placeholder="e.g. BSCCS/2024/12345" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email"
                 class="form-control" placeholder="e.g. jane@mku.ac.ke" required>
        </div>

        <div class="form-group">
          <label for="pw">Password</label>
          <input type="password" id="pw" name="password"
                 class="form-control" data-pw-strength="pw-str"
                 required data-min-len="6">
          <div class="pw-strength-bar">
            <div class="pw-strength-fill" id="pw-str-fill"></div>
          </div>
          <div id="pw-str-label" class="pw-strength-label"></div>
          <span class="form-error" id="pw-error"></span>
        </div>

        <div class="form-group">
          <label for="confirm_pw">Confirm Password</label>
          <input type="password" id="confirm_pw" name="confirm_password"
                 class="form-control" data-match="pw" required>
          <span class="form-error" id="confirm_pw-error"></span>
        </div>

        <button type="submit" class="btn btn-primary w-full">Register</button>
      </form>

      <p style="text-align:center; margin-top:1rem;">
        Already have an account? <a href="/UNISTAY/wk7/login.php">Log in</a>
      </p>
    </div>
    <?php endif; ?>

  </div>
</div>
<script src="/UNISTAY/wk7/assets/js/main.js"></script>
</body>
</html>