<?php

//importing env variables 
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

//connecting to database
require_once($rootDir . '/database.php');


//fetching current status of the site so that at every reload of any page it fetches latest status
$sql = 'SELECT * FROM status ORDER BY id DESC LIMIT 1';
$result = mysqli_query($conn, $sql);
$status = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];

// storing current status in session superglobals
$_SESSION['currentSemester'] = $status['currentSemester'];
$_SESSION['isCourseEntryAllowed'] = $status['isCourseEntryAllowed'];
$_SESSION['isGradeEntryAllowed'] = $status['isGradeEntryAllowed'];


?>

<!-- header of every page  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean Project</title>

    <!-- setting favicon  -->
    <link rel="shortcut icon" href="<?php echo rootUrl ?>/src/images/mnnit_logo.png">

    <!-- loading fonts  -->
    <link href="https://fonts.googleapis.com/css2?family=Acme&family=Kurale&family=Laila:wght@300;500&family=Lalezar&family=Lato:ital,wght@0,400;0,700;1,300&display=swap" rel="stylesheet" />

    <!-- tailwind cdn link -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header class=" w-full bg-gray-300 py-4 px-4 flex flex-row justify-between space-x-2 ">
        <a class="bg-blue-600 text-white py-1 px-3 ml-5" href="<?php echo rootUrl ?>">Home</a>
        <div>

            <!-- adding toggling login-logout button  -->
            <a class="bg-blue-600 text-white py-1 px-3 mr-3" href="<?php echo rootUrl, !isset($_SESSION['uid']) ? '/pages/login.php' : '/controllers/logout.php'; ?>"><?php echo !isset($_SESSION['uid']) ? 'Log In' : 'Log Out'; ?></a>

            <!-- adding toggling admin button  -->
            <?php
            // getting current page name
            $currentUrl =  $_SERVER['PHP_SELF'];
            $page = explode('/', $currentUrl);
            $page = end($page);

            //checking if user is admin or not
            if (isset($_SESSION['uid']) && isset($_SESSION['isAdmin'])) {
                if ($_SESSION['isAdmin'] == true) {
                    // adding toggle admin page button if admin
                    echo '<a class="bg-blue-600 text-white py-1 px-3 mr-3" href="', rootUrl, '/pages/', $page == $_SESSION['category'] . '.php' ? 'admin' : $_SESSION['category'], '.php">', $page == $_SESSION['category'] . '.php' ? 'Admin' : ucwords($_SESSION['category']), '</a>';
                } else {
                    //if not admin then add category page button
                    echo  $page == $_SESSION['category'] . '.php' ? '' : '<a class="bg-blue-600 text-white py-1 px-3 mr-3" href="'. rootUrl. '/pages/'. $_SESSION['category'] . '.php">'.ucwords($_SESSION['category']). '</a>';
                }
            }

            ?>
        </div>
    </header>