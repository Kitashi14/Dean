<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

// Creating connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Checking connection
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
} else{
    echo '<script>console.log("connected to database")</script>';
}

?>

