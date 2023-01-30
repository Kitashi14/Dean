<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');


//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_SESSION['isGradeEntryAllowed']) && ($_SESSION['isGradeEntryAllowed'] == '1')) {
    //connecting to database
    require_once($rootDir . '/database.php');

    // checking if all form inputs are present or not
    if (!isset($_POST['course_id']) || !isset($_POST['regNo']) || !isset($_POST['internal']) || !isset($_POST['midSem']) || !isset($_POST['endSem'])) {
        //if not present then redirecting to form page
        echo '<script>alert("Grade details not found");';
        echo 'window.location= "', rootUrl, '/pages/gradeEntryForm.php?course_id=', $_POST['course_id'], '&regNo=', $_POST['regNo'], '"; </script>';
    } else {

        //extracting form-input of POST request
        $requestedCourseId = htmlspecialchars($_POST['course_id']);
        $requestedRegNo = htmlspecialchars($_POST['regNo']);
        $internal = htmlspecialchars($_POST['internal']);
        $midSem = htmlspecialchars($_POST['midSem']);
        $endSem = htmlspecialchars($_POST['endSem']);

        //changing to integer value
        $internal = (int)$internal;
        $midSem = (int)$midSem;
        $endSem = (int)$endSem;

        //storing required variables for this page
        $currentSemester = $_SESSION['currentSemester'];
        $employeeCoursesId = $_SESSION['employeeCoursesId'];

        //checking if employee has the access of this course or not
        if (!in_array($requestedCourseId, $employeeCoursesId)) {
            //if not redirecting to necessary error page
            header('Location: ./error.php?error=Page not found');
        }


        //fetching course details
        $sql = "SELECT * FROM course WHERE id = '$requestedCourseId'";
        $result = mysqli_query($conn, $sql);
        $courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (empty($courseDetails)) {
            //if no course found
            header('Location: ./../pages/error.php?error=Not Found&message=The requested course was not found in database.');
        } else {
            $courseDetails = $courseDetails[0];
            //saving required variables
            $gradeTable = $courseDetails['isTheory'] == '1' ? 'theorygrade' : 'practicalgrade';
            $courseType = $courseDetails['isTheory'] == '1' ? 'Theory' : 'Practical';
        }

        //fetching student details
        $sql = "SELECT * FROM student WHERE regNo = '$requestedRegNo'";
        $result = mysqli_query($conn, $sql);
        $studentDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (empty($studentDetails)) {
            //if no student found
            header('Location: ./../pages/error.php?error=Not Found&message=The requested student was not found in database.');
        } else {
            $studentDetails = $studentDetails[0];
        }

        //checking course and student compatibility
        if ($studentDetails['program'] != $courseDetails['program']) {
            //if student does not belongs to the course program
            header('Location: ./../pages/error.php?error=Bad Request&message=The requested student does not belongs to this course.');
        }

        //checking if this student is already graded in this course 
        $sql = "SELECT * FROM $gradeTable WHERE regNo = '$requestedRegNo' AND course_id='$requestedCourseId'";
        $result = mysqli_query($conn, $sql);
        $gradeDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //setting type of form
        $formType = empty($gradeDetails) ? 'Enter' : 'Update';

        //check for compatibility of posted marks with course marks distribution 
        if (($internal > $courseDetails['internal']) || ($midSem > $courseDetails['midsem']) || ($endSem > $courseDetails['endsem'])) {
            header('Location: ./../pages/error.php?error=Bad Request&message=Submitted marks not within course marks distribution.');
        }

        //calculating grade
        $total_marks = $internal + $midSem + $endSem;
        if ($_POST['submit'] == 'Absent') {
            $grade = 'Ab';
        } else if ($total_marks >= 85) {
            $grade = 'A+';
        } else if ($total_marks >= 75) {
            $grade = 'A';
        } else if ($total_marks >= 65) {
            $grade = 'B+';
        } else if ($total_marks >= 55) {
            $grade = 'B';
        } else if ($total_marks >= 45) {
            $grade = 'C';
        } else if ($total_marks >= 30) {
            $grade = 'D';
        } else if ($total_marks >= 25) {
            $grade = 'E';
        }

        // for absent student all marks
        $internal = $grade == 'Ab' ? 0 : $internal;
        $midSem = $grade == 'Ab' ? 0 : $midSem;
        $endSem = $grade == 'Ab' ? 0 : $endSem;

        //database request 
        if ($formType == 'Enter') {

            //create operation
            if ($courseType == 'Theory') {
                //for theory course

                //sql for inserting theory grade
                $sql = "INSERT INTO theoryGrade (id, regNo, course_id, internal, midsem, endsem, grade,createdAt) VALUES (NULL, '$requestedRegNo', '$requestedCourseId', '$internal', '$midSem', '$endSem', '$grade', current_timestamp())";

                //inserting to database
                if (mysqli_query($conn, $sql)) {
                    // success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $_POST['course_id'], '"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            } else {
                //for practical course

                //sql for inserting practical grade
                $sql = "INSERT INTO practicalGrade (id, regNo, course_id, internal, endsem, grade,createdAt) VALUES (NULL, '$requestedRegNo', '$requestedCourseId', '$internal', '$endSem', '$grade', current_timestamp())";

                //inserting to database
                if (mysqli_query($conn, $sql)) {
                    // success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $_POST['course_id'], '"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            }
        } else {

            $gradeId = $gradeDetails[0]['id'];

            //update operation
            if ($courseType == 'Theory') {
                //for theory course

                //sql for updating theory grade
                $sql = "UPDATE theoryGrade SET internal= '$internal' , midsem = '$midSem', endsem = '$endSem' , grade = '$grade' WHERE id='$gradeId'";

                //inserting to database
                if (mysqli_query($conn, $sql)) {
                    // success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $_POST['course_id'], '"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            } else {
                //for practical course

                //sql for updating practical grade
                $sql = "UPDATE practicalGrade SET internal= '$internal' , endsem = '$endSem' , grade = '$grade' WHERE id='$gradeId'";

                //inserting to database
                if (mysqli_query($conn, $sql)) {
                    // success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $_POST['course_id'], '"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            }
        }
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found');
}
