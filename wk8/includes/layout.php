<?php


function layout_header(string $title = '') {
    $user = current_user();   
    $page_title = $title ? "$title – MKU Hostel" : "MKU Hostel System";
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($page_title) ?></title>
        <link rel="stylesheet" href="/UNISTAY/wk7/assets/js/css/main.css">
        
    </head>
    <body>
    <header class="main-header">
        <div class="logo">🏛 MKU Hostels</div>
        <nav class="main-nav">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/UNISTAY/wk7/admin/dashboard.php">Dashboard</a>
                <a href="/UNISTAY/wk7/admin/rooms.php">Rooms</a>
                <a href="/UNISTAY/wk7/admin/bookings.php">Bookings</a>
            <?php else: ?>
                <a href="/UNISTAY/wk7/student/dashboard.php">Dashboard</a>
                <a href="/UNISTAY/wk7/student/rooms.php">Browse Rooms</a>
                <a href="/UNISTAY/wk7/student/bookings.php">My Bookings</a>
            <?php endif; ?>
            <span class="user-badge"><?= htmlspecialchars($user['name']) ?></span>
            <a href="/UNISTAY/wk7/logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>
    <main class="main-content">
    <?php
}

function layout_footer() {
    ?>
    </main>
    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> MKU Hostel Management System. All rights reserved.</p>
    </footer>
    </body>
    </html>
    <?php
}
?>