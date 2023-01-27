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
echo $_SESSION['uid'];
echo '<br>';
echo $_SESSION['category'];
echo '<br>';
echo $_SESSION['isAdmin'];
echo '<br>';
echo $_SESSION['regNo'];
echo '<br>';
echo $_SESSION['name'];
echo '<br>';
echo $_SESSION['program'];
echo '<br>';
?>
<?php
include './../inc/footer.php';
?>