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
echo $_SESSION['uid'];
echo '<br>';
echo $_SESSION['category'];
echo '<br>';
echo $_SESSION['isAdmin'];
echo '<br>';
echo $_SESSION['eid'];
echo '<br>';
echo $_SESSION['name'];
echo '<br>';
echo $_SESSION['position'];
echo '<br>';

?>

<?php
include './../inc/footer.php';
?>