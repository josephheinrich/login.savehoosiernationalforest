<?php 
session_save_path('/home2/savehoos/php_sessions');
session_start();

session_unset();

session_destroy();

header("Location: index.php");

?>