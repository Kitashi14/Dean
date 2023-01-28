<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

// handling login POST request
if (isset($_POST['submit'])) {
    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting form-input of POST request
    $setCurrentSemester = htmlspecialchars($_POST['currentSemester']);
    $setCourseEntryStatus = $_POST['isCourseEntryAllowed'];
    $setGradeEntryStatus =  $_POST['isGradeEntryAllowed'];

    $setCurrentSemester = (int)$setCurrentSemester;
    //checking if all form inputs are present or not
    if (empty($setCourseEntryStatus) || empty($setCurrentSemester) || empty($setGradeEntryStatus)) {
        //if not present then redirecting to form page
        echo '<script>alert("Status details not found");';
        echo 'window.location= "./../pages/statusForm.php"; </script>';
    } else {

        //checking semester value is in range or not
        if ($setCurrentSemester > 8 || $setCurrentSemester < 1) {

            //for invalid entry redirecting to form page
            echo '<script>alert("Invalid semester");';
            echo 'window.location= "./../pages/statusForm.php"; </script>';
        }

        //storing values in variables for easy usage
        $user_id = $_SESSION['uid'];
        $setCourseEntryStatus = $setCourseEntryStatus == 'yes' ? 1 : 0;
        $setGradeEntryStatus = $setGradeEntryStatus == 'yes' ? 1 : 0;


        //sql for inserting status as per provided details
        $sql = "INSERT INTO status (id, user_id, currentSemester, isCourseEntryAllowed, isGradeEntryAllowed, createdAt) VALUES (NULL, '$user_id', '$setCurrentSemester', '$setCourseEntryStatus', '$setGradeEntryStatus', current_timestamp())";

        //inserting to database
        if (mysqli_query($conn, $sql)) {
            // success
            header('Location: ./../pages/admin.php');
        } else {
            // error
            header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
        }
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found');
}
