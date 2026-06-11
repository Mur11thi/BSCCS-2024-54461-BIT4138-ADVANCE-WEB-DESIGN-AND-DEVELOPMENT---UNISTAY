<?php
session_start();
require_once __DIR__ . '/config/connection.php';

if (!empty($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'admin')
           ? '/UNISTAY/wk4/admin/dashboard.php'
           : '/UNISTAY/wk4/student/dashboard.php';
    header('Location: ' . $dest);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = htmlspecialchars(trim($_POST['student_id'] ?? ''));
    $password   = trim($_POST['password'] ?? '');

    if (empty($student_id) || empty($password)) {
        $error = 'Security Exception: credential fields cannot be null.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE student_id = :sid LIMIT 1');
        $stmt->execute([':sid' => $student_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['full_name']  = $user['full_name'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['student_id'] = $user['student_id'];

            $dest = ($user['role'] === 'admin')
                  ? '/UNISTAY/wk4/admin/dashboard.php'
                  : '/UNISTAY/wk4/student/dashboard.php';
            header('Location: ' . $dest);
            exit;
        } else {
            $error = 'Invalid Student ID or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hostel Login – MKU</title>
  <link rel="stylesheet" href="/UNISTAY/wk4/assets/js/css/main.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <!-- Logo, title, subtitle –  -->
    <div class="auth-card-top">
      <div class="auth-logo">🏛</div>
      <div class="auth-title">MKU Hostel System</div>
      <div class="auth-subtitle">Campus Accommodation Portal</div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="auth-body">
      <form method="POST" data-validate>

        <div class="form-group">
          <label for="sid">Student ID</label>
          <input type="text" id="sid" name="student_id"
                 class="form-control"
                 placeholder="e.g. BSCCS/2024/50482" required>
          <span class="form-error" id="sid-error"></span>
        </div>

        <div class="form-group">
          <label for="pw">Password</label>
          <input type="password" id="pw" name="password"
                 class="form-control"
                 data-pw-strength="pw-str"
                 required data-min-len="6">
          <div class="pw-strength-bar">
            <div class="pw-strength-fill" id="pw-str-fill"></div>
          </div>
          <div id="pw-str-label" class="pw-strength-label"></div>
          <span class="form-error" id="pw-error"></span>
        </div>

        <button type="submit" class="btn btn-primary w-full">
          Authenticate Secure Access
        </button>
        <p style="text-align:center; margin-top:1rem;">
        Don't have an account? <a href="/UNISTAY/wk4/register.php">Register here</a>
      </p>

      </form>
    </div>

  </div>
</div>
<script src="/UNISTAY/wk4/assets/js/main.js"></script>
</body>
</html>

