<!-- view transcript page  -->
<?php
// importing header component
include './../inc/header.php';

try {
    // checking if required query variables is present in the request
    if (!isset($_GET['regNo'])) {
        //if not redirecting to necessary error page
        header('Location: ./error.php?error=Page not found');
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), ' ';
}


// redirecting if user is not set or not student 
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'student') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}

//storing required variables for this page
$currentSemester = $_SESSION['currentSemester'];
$program = $_SESSION['program'];


//checking whether request reg no and session reg no are same or not
$requestedRegNo = $_GET['regNo'];
if ($requestedRegNo !=  $_SESSION['regNo']) {
    //if not redirecting to necessary error page
    header('Location: ./error.php?error=Page not found');
}


//fetching student courses from db 
$sql = "SELECT * FROM course WHERE program = '$program'";
$result = mysqli_query($conn, $sql);
$studentCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);

//determining if result released for current semester
//      checking if all courses of current semester submitted 
$cousesNotSubmitted = array_filter($studentCourses, fn ($course) => ($course['isSubmitted'] == '0') && ($course['semester'] == $currentSemester));
echo '<br>';
$isCurSemResultReleased = (empty($cousesNotSubmitted)) && ($_SESSION['isGradeEntryAllowed'] == '0') ? true : false;

//fetching practical grades of all subjects for all semester for this student
$sql = "SELECT c.credit, g.grade, c.courseName, c.courseCode, c.semester FROM course c, practicalGrade g WHERE g.regNo = '$requestedRegNo' AND g.course_id = c.id ";
$result = mysqli_query($conn, $sql);
$practicalGrades = mysqli_fetch_all($result, MYSQLI_ASSOC);
// print_r($practicalGrades);

//fetching theory grades of all subjects for all semester for this student
$sql = "SELECT c.credit, g.grade, c.courseName, c.courseCode, c.semester FROM course c, theoryGrade g WHERE g.regNo = '$requestedRegNo' AND g.course_id = c.id ";
$result = mysqli_query($conn, $sql);
$theoryGrades = mysqli_fetch_all($result, MYSQLI_ASSOC);
// print_r($theoryGrades);

// determining max semester to show 
$maxSemester = $isCurSemResultReleased ? (int)$currentSemester : (int)$currentSemester - 1;



?>
<div class="flex justify-center items-center w-full h-10 text-4xl font-medium">Transcript Page</div>
<div class=" p-5 w-full">

    <div class=" w-full h-full flex flex-wrap">

        <?php
        $gradeTable = [
            'A+' => 10,
            'A' => 9,
            'B+' => 8,
            'B' => 7,
            'C' => 6,
            'D' => 4,
            'E' => 3,
            'F' => 1,
            'Ab' => 0
        ];
        for ($semesterNo = 1; $semesterNo <= $maxSemester; $semesterNo++) {
            $creditObtained = 0;
            $totalCreditAvailable = 0;
            $subjectFailed = 0;

            //storing all grades of selected semester
            $semGrades = [];
            array_map(function ($grade) {
                global $semesterNo, $semGrades;
                if ((int)$grade['semester'] == $semesterNo) {
                    global $creditObtained, $totalCreditAvailable, $subjectFailed, $gradeTable;
                    $creditObtained = $creditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
                    $totalCreditAvailable = $totalCreditAvailable + (int)$grade['credit'];
                    $subjectFailed = $subjectFailed + ($gradeTable[$grade['grade']] < 4 ? 1 : 0);
                    array_push($semGrades, $grade);
                }
            }, $practicalGrades);

            array_map(function ($grade) {
                global $semesterNo, $semGrades;
                if ((int)$grade['semester'] == $semesterNo) {
                    global $creditObtained, $totalCreditAvailable, $subjectFailed, $gradeTable;
                    $creditObtained = $creditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
                    $totalCreditAvailable = $totalCreditAvailable + (int)$grade['credit'];
                    $subjectFailed = $subjectFailed + ($gradeTable[$grade['grade']] < 4 ? 1 : 0);
                    array_push($semGrades, $grade);
                }
            }, $theoryGrades);

            $spi = 'NA';
            if ($totalCreditAvailable > 0) {
                $spi = round($creditObtained / $totalCreditAvailable, 2);
                $semResult = ($spi < 5) || ($subjectFailed > 4) ? 'Failed' : 'Passed';
            } else {
                $semResult = 'NA';
            }



            echo '<div class="w-1/2 p-4 mt-5 ">
            <h2 class="text-xl font-normal text-green-700 px-3">Semester: ', $semesterNo, ' (', $semResult, ')</h2>
            <div class="border-2 border-black mt-3 hover:shadow-2xl hover:scale-105 hover:duration-200 hover:transition">
            <table class="w-full text-center ">
                <thead class="bg-green-600 py-4">
                    <tr>
                        <th class="py-2">Course Code</th>
                        <th class="py-2">Course Name</th>
                        <th class="py-2">Credits</th>
                        <th class="py-2">Grades</th>
                    </tr>
                </thead>

                <tbody class="py-4">
                    ';
            $isEmpty = false;

            if (empty($semGrades)) {
                $isEmpty = true;
            } else {
                array_map(function ($course) {
                    echo '<tr class="bg-green-100 odd:bg-green-300"><td>', $course['courseCode'], '</td><td>', $course['courseName'], '</td><td>', $course['credit'], '</td><td>', $course['grade'], '</td></tr>';
                }, $semGrades);
                echo '<tr class="bg-green-800 text-white mt-2 font-bold"><td></td><td></td><td>SPI</td><td>', $spi, '</td></tr>';
            }
            echo '</tbody>


            </table>';
            echo $isEmpty ? '<h3 class="w-full bg-green-200 py-2 text-center">No courses available</h3>' : '';

            echo '</div></div>';
        }
        ?>

    </div>

</div>

<?php

// importing footer component
include './../inc/footer.php';
?>