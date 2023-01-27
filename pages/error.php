<!-- error page  -->
<?php


//importing env variables
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

try {
    // checking if required quiery variables is present in the request
    if (isset($_GET['error'])) {
        // if present then showing requested error
        $err = $_GET['error'];
        echo "<h1>$err</h1>";
    } else {
        
        //if not redirecting to necessary error page
        header('Location : ./error.php?error=Page no found');
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), ' ';
}