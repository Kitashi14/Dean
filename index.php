<?php 
    include './inc/header.php';

    echo isset($_SESSION['uid'])? $_SESSION['uid'] : '';
?>
    This is home page

<?php
    include './inc/footer.php';

