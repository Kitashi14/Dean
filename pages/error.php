<?php
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

try {
    if (isset($_GET['error'])) {
        $err = $_GET['error'];
        echo "<h1>$err</h1>";
    } else {
        echo 'jflsdkf';
        header('Location : ./error.php?error=Page no found');
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), ' ';
}
