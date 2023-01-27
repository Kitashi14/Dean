<?php
include './../inc/header.php';

if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'employee') {
        header('Location : ./error.php?error=Page no found');
    }
} else {
    header('Location : ./error.php?error=Page no found');
}   



?>

This is employee page


<?php
include './../inc/footer.php';
?>