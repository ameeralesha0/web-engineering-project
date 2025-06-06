<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'mypetakom');
date_default_timezone_set('Asia/Kuala_Lumpur');

?>
