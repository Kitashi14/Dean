<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_SESSION['isCourseEntryAllowed']) && ($_SESSION['isCourseEntryAllowed'] == '1') && isset($_SESSION['category']) && $_SESSION['category'] == 'employee') {
    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting form-input of POST request
    $courseName = htmlspecialchars($_POST['courseName']);
    $courseCode =  htmlspecialchars($_POST['courseCode']);
    $credit = htmlspecialchars($_POST['credit']);
    $program = htmlspecialchars($_POST['program']);
    $type = htmlspecialchars($_POST['type']);
    $internal = htmlspecialchars($_POST['internal']);
    $midSem = htmlspecialchars($_POST['midSem']);
    $endSem = htmlspecialchars($_POST['endSem']);

    // checking if all form inputs are present or not
    if (empty($courseCode) || empty($courseName) || empty($credit) || empty($program) || empty($type)) {
        //if not present then redirecting to form page
        echo '<script>alert("Course details not found");';
        echo 'window.location= "./../pages/courseEntryForm.php"; </script>';
    } else {

        //change to integer from string
        $internal = (int)$internal;
        $midSem = (int)$midSem;
        $endSem = (int)$endSem;

        //checking for midsem marks
        if (($type == 'practical') && ($midSem != 0)) {
            echo '<script>alert("Can\'t add mid sem marks for practical course.");';
            echo 'window.location= "./../pages/courseEntryForm.php"; </script>';
        } else {
            // check if marks distribution is according to the type defined in request 
            if (($internal + $midSem + $endSem) != 100) {
                echo '<script>alert("Marks distribution not correct. Your total marks entered for this ' . $type . ' course is ' . ($endSem + $internal + $midSem) . '. Please enter again");';
                echo 'window.location= "./../pages/courseEntryForm.php"; </script>';
            } else {
                //storing values in variables for easy usage
                $user_id = $_SESSION['uid'];
                $isTheory = $type == 'theory' ? 1 : 0;
                $semester = (int)$_SESSION['currentSemester'] + 1;
                $midSem = empty($midSem) ? NULL : $midSem;
                $eid = $_SESSION['eid'];

                //check request type 
                $requestType = $_POST['submit'] == 'ADD' ? 'insert' : 'update';

                if ($requestType == 'insert') {
                    //sql to searching for duplicate entry
                    $sql = "SELECT * FROM course WHERE semester = '$semester' AND courseCode = '$courseCode' AND program = '$program' AND isTheory = '$isTheory'";

                    //fetching for dulplicate entry 
                    $result = mysqli_query($conn, $sql);
                    $presence = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    // handling error if duplicate present
                    if (!empty($presence)) {
                        echo '<script>alert("This course has been already added to ' . $program . ' students for next semester. Enter another course.");';
                        echo 'window.location= "./../pages/courseEntryForm.php"; </script>';
                    } else {
                        //sql for inserting course
                        $sql = "INSERT INTO course (id, employee_id, semester, courseName, courseCode, credit, isTheory, program, isSubmitted, internal, midsem, endsem, createdAt) VALUES (NULL, '$eid', '$semester', '$courseName', '$courseCode', '$credit', '$isTheory', '$program', '0', '$internal', '$midSem', '$endSem', current_timestamp())";

                        //inserting to database
                        if (mysqli_query($conn, $sql)) {
                            // success
                            header('Location: ./../pages/employee.php');
                        } else {
                            // error
                            header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                        }
                    }
                } else {
                    //update course 

                    if (empty($_POST['course_id'])) {
                        header('Location: ./../pages/error.php?error=Details Not Found');
                    } else {
                        $requestedCourseId = $_POST['course_id'];
                        $eid = $_SESSION['eid'];
                        //is case of update form checking authencity
                        $sql = "SELECT * FROM course WHERE id = '$requestedCourseId' AND employee_id='$eid' AND semester='$semester'";
                        $result = mysqli_query($conn, $sql);
                        $courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        if (empty($courseDetails)) {
                            //if no course found
                            header('Location: ./../pages/error.php?error=Not Found&message=The requested course was not found in database.');
                        } else {
                            //updating course details

                            //sql for updating
                            $sql = "UPDATE course SET courseName='$courseName', courseCode='$courseCode', credit='$credit', isTheory='$isTheory', program='$program', internal='$internal', midsem='$midSem', endsem='$endSem' 
                                 WHERE id='$requestedCourseId'";

                            //updating data
                            if (mysqli_query($conn, $sql)) {
                                // success
                                echo '<script>';
                                echo 'window.location= "', rootUrl, '/pages/employee.php"; </script>';
                            } else {
                                // error
                                header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found');
}
