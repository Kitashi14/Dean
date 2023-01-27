<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'kitashi');
define('DB_PASS', 'kitashi');
define('DB_NAME', 'dean_project');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

// echo 'Connected successfully';