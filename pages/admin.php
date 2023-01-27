<!-- admin page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not admin
if (isset($_SESSION['uid']) && isset($_SESSION['isAdmin'])) {
    if (!$_SESSION['isAdmin']) {
        header('Location : ./error.php?error=Page no found');
    }
} else {
    header('Location : ./error.php?error=Page no found');
}


?>

This is admin page 
<br>

<?php

// importing footer component
include './../inc/footer.php';
?>