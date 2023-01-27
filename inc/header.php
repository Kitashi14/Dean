<?php
    $rootDir = $_SERVER['DOCUMENT_ROOT'].'/deanproject';
    $path = $rootDir. '/config.php';
    include $path;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean Project</title>

    <!-- setting favicon  -->
    <link rel="shortcut icon" href="./../src/images/mnnit_logo.png">

    <!-- loading fonts  -->
    <link href="https://fonts.googleapis.com/css2?family=Acme&family=Kurale&family=Laila:wght@300;500&family=Lalezar&family=Lato:ital,wght@0,400;0,700;1,300&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header class=" w-full bg-gray-300 p-2 flex flex-row justify-start space-x-2">
        <a class="bg-blue-600 text-white py-1 px-3" href="<?php echo rootUrl ?>">Home</a>
        <a class="bg-blue-600 text-white py-1 px-3" href="<?php
                    echo rootUrl , !isset($_SESSION['uid']) ? '/pages/login.php' : '/pages/logout.php';
                    ?>"><?php echo !isset($_SESSION['uid']) ? 'Log In' : 'Log Out'; ?></a>
    </header>