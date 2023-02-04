<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) {
    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting form-input of POST request
    $setCurrentSemester = htmlspecialchars($_POST['setSemester']);
    $setAllowed = $_POST['allowed'];

    $setCurrentSemester = (int)$setCurrentSemester;
    //checking if all form inputs are present or not
    if (empty($setAllowed) || empty($setCurrentSemester)) {
        //if not present then redirecting to form page
        echo '<script>alert("Status details not found");';
        echo 'window.location= "./../pages/statusForm.php"; </script>';
    } else {


        $currentSemester = $_SESSION['currentSemester'];

        //fetching employee who have not submitted their courses yet
        $sql = "SELECT e.name, c.id, c.employee_id, c.courseName, c.program , c.isTheory FROM course c, employee e WHERE c.isSubmitted='0' AND c.semester = '$currentSemester' AND e.id=c.employee_id  ORDER BY e.name ASC";
        $result = mysqli_query($conn, $sql);
        $notSubmittedCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //checking semester value is in range or not
        if (($setCurrentSemester > (empty($notSubmittedCourses) ? 8 : $currentSemester)) || ($setCurrentSemester < $_SESSION['currentSemester'])) {

            //for invalid entry redirecting to form page
            echo '<script>alert("Invalid semester");';
            echo 'window.location= "./../pages/statusForm.php"; </script>';
        } else {
            //storing values in variables for easy usage
            $user_id = $_SESSION['uid'];
            $setCourseEntryStatus = $setAllowed == 'courseEntry' ? 1 : 0;
            $setGradeEntryStatus = $setAllowed == 'gradeEntry' ? 1 : 0;


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
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found');
}
