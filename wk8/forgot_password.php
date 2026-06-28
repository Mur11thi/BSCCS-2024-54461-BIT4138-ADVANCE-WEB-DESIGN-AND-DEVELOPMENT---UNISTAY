<?php
session_start();
require_once __DIR__ . '/config/connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'Please enter your email.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a random token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token
            $stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$email, $token, $expires]);

            // In a real system, you'd email this link. Here we just display it.
            $resetLink = "http://localhost/UNISTAY/wk7/reset_password.php?token=" . $token; 
            // not done yet 


            $success = "Password reset link (simulated):<br><a href='$resetLink'>$resetLink</a>";
        } else {
            // Don't reveal that email doesn't exist; just show generic message
            $success = 'If that email is registered, a reset link has been sent.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Forgot Password – MKU Hostel</title>
  <link rel="stylesheet" href="/UNISTAY/wk7/assets/css/main.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-card-top">
      <div class="auth-logo">🏛</div>
      <div class="auth-title">MKU Hostel System</div>
      <div class="auth-subtitle">Reset Your Password</div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert" style="background:#D4EDDA;color:#155724;border:1px solid #C3E6CB;">
        <?= $success ?>
      </div>
    <?php endif; ?>

    <div class="auth-body">
      <form method="POST">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="e.g. student@mku.ac.ke" required>
        </div>
        <button type="submit" class="btn btn-primary w-full">Send Reset Link</button>
      </form>
      <p style="text-align:center; margin-top:1rem;">
        <a href="/UNISTAY/wk5/login.php">Back to Login</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>