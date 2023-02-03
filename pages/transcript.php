<!-- view transcript page  -->
<?php

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


//calculating cpi
$gradeTable = [
    'A+' => 10,
    'A' => 9,
    'B+' => 8,
    'B' => 7,
    'C' => 6,
    'D' => 4,
    'E' => 2,
    'F' => 0
];
$cpiTillSemester = $isCurSemResultReleased ? $currentSemester : (int)$currentSemester - 1;
$CPIcreditObtained = 0;
$CPItotalCreditAvailable = 0;

//practical grades
array_map(function ($grade) {
    global $cpiTillSemester;
    if ($grade['semester'] <= $cpiTillSemester) {
        global $CPIcreditObtained, $CPItotalCreditAvailable, $gradeTable;
        $CPIcreditObtained = $CPIcreditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
        $CPItotalCreditAvailable = $CPItotalCreditAvailable + (int)$grade['credit'];
    }
}, $practicalGrades);

//theory grades
array_map(function ($grade) {
    global $cpiTillSemester;
    if ($grade['semester'] <= $cpiTillSemester) {
        global $CPIcreditObtained, $CPItotalCreditAvailable, $gradeTable;
        $CPIcreditObtained = $CPIcreditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
        $CPItotalCreditAvailable = $CPItotalCreditAvailable + (int)$grade['credit'];
    }
}, $theoryGrades);

$cpi = 'NA';
if ($CPItotalCreditAvailable > 0) {
    $cpi = round($CPIcreditObtained / $CPItotalCreditAvailable, 2);
}


?>
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

<body class="relative min-h-screen ">

    <div class="pb-16">
        <div class="w-full  flex justify-start pl-5"><a href="./../index.php" class="border-solid border-2 rounded-sm px-3 py-0 border-black font-serif underline">Home</a></div>

        <div class="flex justify-center items-center w-full h-10 text-4xl font-medium font-serif underline">Transcript</div>
        <div class=" p-5 w-full">
            <div class="flex flex-row px-5">
                <h2 class="block mb-2 pr-2 text-m font-bold font-serif ">Name: <span class="font-sans"><?php echo $_SESSION['name'] ?></span></h2>
                <h2 class="block mb-2 px-2 text-m font-bold font-serif ">Reg No: <span class="font-mono"><?php echo $_SESSION['regNo'] ?></span></h2>
                <h2 class="block mb-2 px-2 text-m font-bold font-serif ">Program: <span class="font-sans"><?php echo $_SESSION['program'] ?></span></h2>
            </div>

            <h2 class="block mb-2 px-5 text-m font-bold font-serif underline">CPI: <span class="font-mono"><?php echo $cpi ?></span></h2>
            <div class=" w-full h-full flex flex-wrap justify-between">

                <?php
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
            <h2 class="text-xl font-normal underline px-3 font-serif">Semester: <span class="font-mono">', $semesterNo, '</span> (', $semResult, ')</h2>
            <table class="w-full text-center ">
                <thead class="py-4 border-solid border-4 border-black border-b-2">
                    <tr>
                        <th class="py-2 border-solid border-r-2 border-black">Course Code</th>
                        <th class="py-2 border-solid border-r-2 border-black">Course Name</th>
                        <th class="py-2 border-solid border-r-2 border-black">Credits</th>
                        <th class="py-2 border-solid border-r-0 border-black">Grades</th>
                    </tr>
                </thead>

                <tbody class="py-4 border-solid border-4 border-black border-t-2">
                    ';
                    $isEmpty = false;

                    if (empty($semGrades)) {
                        $isEmpty = true;
                    } else {
                        array_map(function ($course) {
                            echo '<tr class="border-solid border-b-2 border-black"><td class="border-solid border-r-2 border-black">', $course['courseCode'], '</td><td class="border-solid border-r-2 border-black">', $course['courseName'], '</td><td class="border-solid border-r-2 border-black">', $course['credit'], '</td><td>', $course['grade'], '</td></tr>';
                        }, $semGrades);
                        echo '<tr class="bg-white mt-2 font-bold"><td></td><td></td><td>SPI</td><td>', $spi, '</td></tr>';
                    }
                    echo '</tbody>


            </table>';
                    echo $isEmpty ? '<h3 class="w-full  py-2 text-center border-4 border-t-0 border-black font-sans">No courses available</h3>' : '';

                    echo '</div>';
                }
                ?>

            </div>

        </div>


    </div>
</body>

</html>