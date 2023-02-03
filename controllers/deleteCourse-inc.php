<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

//checking if user is not set or not employee or if course entry no allowed
if (isset($_SESSION['uid']) && isset($_SESSION['category']) && isset($_SESSION['isCourseEntryAllowed'])) {
    if ($_SESSION['category'] == 'employee' && $_SESSION['isCourseEntryAllowed'] == '1') {

        //checking for course id availability
        if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {

            //connecting to database
            require_once($rootDir . '/database.php');

            //storing required variables
            $requestedCourseId = $_GET['course_id'];
            $eid = $_SESSION['eid'];
            $currentSemester = $_SESSION['currentSemester'];

            //checking authencity
            $sql = "SELECT * FROM course WHERE id = '$requestedCourseId' AND employee_id='$eid' AND semester='$currentSemester'";
            $result = mysqli_query($conn, $sql);
            $courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (empty($courseDetails)) {
                //if no course found
                header('Location: ./../pages/error.php?error=Not Found&message=The requested course was not found in database.');
            } else {
                //if course found then delete

                //sql for deleting course
                $sql = "DELETE FROM course WHERE id='$requestedCourseId'";

                //deleting data
                if (mysqli_query($conn, $sql)) {
                    // success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/employee.php?"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            }
        } else { {
                header('Location: ./../pages/error.php?error=Details Not Found');
            }
        }
    } else {
        header('Location: ./../pages/error.php?error=Bad Request');
    }
} else {
    header('Location: ./../pages/error.php?error=Page not found');
}
