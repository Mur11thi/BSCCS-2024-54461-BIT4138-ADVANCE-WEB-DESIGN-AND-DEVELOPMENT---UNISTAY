<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Test</title>
</head>
<body>
    <h1>Hello World!</h1>
    <p>Local environment test page.</p>

    <?php
        
        echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>PHP version: " . phpversion() . "</p>";
    ?>
</body>
</html>