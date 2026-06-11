<?php

session_start();
session_destroy();
// header('Location: /hostel/login.php?msg=logged_out');
header('Location: /UNISTAY/wk4/login.php?msg=logged_out');
exit;