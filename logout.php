<?php
include 'navbar.php';
session_start();
session_destroy();
header("Location: login.php");
?>

<?php show_source(__FILE__); ?>