<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$new_password = 'BePros2025!';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
echo $hashed_password;
?>