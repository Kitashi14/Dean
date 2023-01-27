<?php

include './../inc/header.php';

if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'student') {
        header('Location : ./error.php?error=Page no found');
    }
} else {
    header('Location : ./error.php?error=Page no found');
}





?>

This is student page
<br>

<?php
include './../inc/footer.php';
?>