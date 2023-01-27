<!-- home page  -->
<?php 

    //importing header component
    include './inc/header.php';

    echo isset($_SESSION['uid'])? $_SESSION['uid'] : '';
?>
    This is home page

<?php

    //importing footer component
    include './inc/footer.php';

