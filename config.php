<!-- config.php -->
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');//change this
define('DB_PASS', '1mperman3nt#'); //change this
define('DB_NAME', 'todo_list_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
?>