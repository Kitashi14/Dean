
<?php   
session_start(); //to ensure you are using same session
session_unset();

session_destroy(); //destroy the session 
echo '<script>alert("You have been logged out");';
echo 'window.location= "./../index.php"; </script>'; //to redirect back to "index.php" after logging out
exit();
?>
