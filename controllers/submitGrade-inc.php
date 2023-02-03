<!-- final grade submission controller  -->
<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');


//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_SESSION['currentSemester']) && isset($_POST['course_id']) && isset($_SESSION['isGradeEntryAllowed']) && ($_SESSION['isGradeEntryAllowed'] == '1') && isset($_SESSION['category']) && $_SESSION['category'] == 'employee') {
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
        header('Location: ./../pages/error.php?error=Page not found');
    } else {

        //fetching course details
        $sql = "SELECT * FROM course WHERE id = '$requestedCourseId'";
        $result = mysqli_query($conn, $sql);
        $courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];

        //saving required variables
        $program = $courseDetails['program'];
        $gradeTable = $courseDetails['isTheory'] == '1' ? 'theorygrade' : 'practicalgrade';

        //fetching students who are not graded
        $sql = "SELECT S.regNo FROM student S WHERE S.regNo NOT IN (SELECT s.regNo FROM student s, $gradeTable g WHERE s.regNo = g.regNo AND g.course_id = '$requestedCourseId' AND s.program='$program') AND S.program = '$program'";

        $result = mysqli_query($conn, $sql);
        $studentsNotGraded = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (empty($studentsNotGraded) && ($courseDetails['isSubmitted']=='0')) {
            //sql for updating course
            $sql = "UPDATE course SET isSubmitted = '1' WHERE id='$requestedCourseId'";

            //updating data
            if (mysqli_query($conn, $sql)) {
                // success

                //adding failed students to respective suppliTables
                $suppliTable = $courseDetails['isTheory'] == '1' ? 'theorySuppli' : 'practicalSuppli';

                //sql for inserting content of one table content to another table
                $sql = "INSERT INTO $suppliTable (id,regNo,course_id,grade_id, createdAt)  (SELECT NULL, g.regNo, g.course_id, g.id, current_timestamp() FROM $gradeTable g WHERE g.course_id=$requestedCourseId AND (g.grade='E' OR g.grade='F'))";

                if (mysqli_query($conn, $sql)) {
                    //success
                    echo '<script>';
                    echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $requestedCourseId, '"; </script>';
                } else {
                    // error
                    header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                }
            } else {
                // error
                header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
            }
        } else {
            //in case of students left to grade redirecting to course page
            echo '<script>alert("Students left to grade. Please grade all students first.");';
            echo 'window.location= "', rootUrl, '/pages/course.php?course_id=', $_POST['course_id'], '"; </script>';
        }
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found');
}
