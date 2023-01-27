<!-- student page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not student
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'student') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}





?>

This is student page
<br>

<?php

// importing footer component
include './../inc/footer.php';
?>