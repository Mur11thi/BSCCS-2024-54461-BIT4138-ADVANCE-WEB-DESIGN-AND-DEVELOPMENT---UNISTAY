<!DOCTYPE html>
<html lang="en">
<head>
  <title>Hostel Login</title>
  <!-- <link rel="stylesheet" href="/hostel/assets/css/main.css"> -->
  <!-- <link rel="stylesheet" href="/wk3/assets/css/main.css"> -->
  <link rel="stylesheet" href="/UNISTAY/wk3/assets/css/main.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-top">
      <div class="auth-logo">🏛</div>
      <div class="auth-title">MKU Hostel System</div>
      <div class="auth-subtitle">Campus Accommodation Portal</div>
    </div>

    <div class="auth-body">
      
      <form method="POST" data-validate>

        <div class="form-group">
          <label for="sid">Student ID</label>
          <input type="text"
                 id="sid"
                 name="student_id"
                 class="form-control"
                 placeholder="e.g. BSCCS/2024/50482"
                 required>
          <span class="form-error" id="sid-error"></span>
        </div>

        <div class="form-group">
          <label for="pw">Password</label>
          <input type="password"
                 id="pw"
                 name="password"
                 class="form-control"
                 data-pw-strength="pw-str"
                 required
                 data-min-len="6">
          <div class="pw-strength-bar">
            <div class="pw-strength-fill" id="pw-str-fill"></div>
          </div>
          <div id="pw-str-label" class="pw-strength-label"></div>
          <span class="form-error" id="pw-error"></span>
        </div>

        <button type="submit" class="btn btn-primary w-full">
          Authenticate Secure Access
        </button>

      </form>
    </div>
  </div>
</div>
<!-- <script src="/hostel/assets/js/main.js"></script> -->
<script src="/UNISTAY/wk3/assets/js/main.js"></script>
</body>
</html>