<!-- employee page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not employee
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'employee') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}   



?>

This is employee page


<?php

//importing footer component
include './../inc/footer.php';
?>