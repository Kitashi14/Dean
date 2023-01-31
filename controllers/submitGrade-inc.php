<!-- final grade submission controller  -->
<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');


//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_POST['course_id']) && isset($_SESSION['isGradeEntryAllowed']) && ($_SESSION['isGradeEntryAllowed'] == '1')) {
    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting form-input of POST request
    $requestedCourseId = htmlspecialchars($_POST['course_id']);

    //storing required variables for this page
    $currentSemester = $_SESSION['currentSemester'];
    $employeeCoursesId = $_SESSION['employeeCoursesId'];

    //checking if employee has the access of this course or not
    if (!in_array($requestedCourseId, $employeeCoursesId)) {
        //if not redirecting to necessary error page
        header('Location: ./error.php?error=Page not found');
    } else {

        //sql for updating course
        $sql = "UPDATE course SET isSubmitted = '1' WHERE id='$requestedCourseId'";

        //updating data
        if (mysqli_query($conn, $sql)) {
            // success
            echo '<script>';
            echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $requestedCourseId, '"; </script>';
        } else {
            // error
            header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
        }
    }
} else {
    echo 'sdjls';
    // handling directing access of this file 
    // header('Location: ./../pages/error.php?error=Page not found');
}
