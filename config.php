<!-- config.php -->
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');//change this (db username)
define('DB_PASS', ''); //change this (db password)
define('DB_NAME', 'todo_list_db'); //change this (optional) (db name)

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
?>