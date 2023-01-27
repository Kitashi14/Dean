
<?php

session_start(); //to ensure you are using same session

// checking whether someone has logged in or not
if (!isset($_SESSION['uid'])) {

    // if not then redirecting to error page
    header('Location: ./error.php?error=Bad Request');
} else {
    
    // freeing session variables 
    session_unset();

    session_destroy(); //destroy the session 

    // redirecting to home page
    echo '<script>alert("You have been logged out");'; 
    echo 'window.location= "./../index.php"; </script>'; 

}
?>
