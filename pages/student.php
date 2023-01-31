<!-- student page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not student
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'student') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}


$regNo = $_SESSION['regNo'];
$currentSemester = $_SESSION['currentSemester'];
$program = $_SESSION['program'];

//fetching student courses from db for current semester
$sql = "SELECT * FROM course WHERE program = '$program' AND semester = '$currentSemester'";
$result = mysqli_query($conn, $sql);
$studentCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);

//determining if result released
//      checking if all courses submitted 
$cousesNotSubmitted = array_filter($studentCourses, fn ($course) => $course['isSubmitted'] == '0');
echo '<br>';
$viewResult = (empty($cousesNotSubmitted)) && ($_SESSION['isGradeEntryAllowed'] == '0') ? true : false;

if ($viewResult) {
    //determining result summary
    $creditObtained = 0;
    $totalCreditAvailable = 0;
    $subjectFailed = 0;
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

    //fetching practical grades
    $sql = "SELECT c.credit, g.grade, c.courseName, c.id FROM course c, practicalGrade g WHERE g.regNo = '$regNo' AND g.course_id = c.id AND c.semester = '$currentSemester'";
    $result = mysqli_query($conn, $sql);
    $practicalGrades = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // print_r($practicalGrades);

    array_map(function ($grade) {
        global $creditObtained, $totalCreditAvailable, $subjectFailed, $gradeTable;
        $creditObtained = $creditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
        $totalCreditAvailable = $totalCreditAvailable + (int)$grade['credit'];
        $subjectFailed = $subjectFailed + ($gradeTable[$grade['grade']] < 4 ? 1 : 0);
    }, $practicalGrades);

    //fetching theory grades
    $sql = "SELECT c.credit, g.grade , c.courseName, c.id FROM course c, theoryGrade g WHERE g.regNo = '$regNo' AND g.course_id = c.id AND c.semester = '$currentSemester'";
    $result = mysqli_query($conn, $sql);
    $theoryGrades = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // print_r($theoryGrades);

    array_map(function ($grade) {
        global $creditObtained, $totalCreditAvailable, $subjectFailed, $gradeTable;
        $creditObtained = $creditObtained + ($gradeTable[$grade['grade']] * (int)$grade['credit']);
        $totalCreditAvailable = $totalCreditAvailable + (int)$grade['credit'];
        $subjectFailed = $subjectFailed + ($gradeTable[$grade['grade']] < 4 ? 1 : 0);
    }, $theoryGrades);

    $spi = round($creditObtained / $totalCreditAvailable, 2);
    $isFailed = ($spi < 5) || ($subjectFailed > 4) ? true : false;

    // echo '<br>', $creditObtained, '<br>', $totalCreditAvailable, '<br>', $spi, '<br>', $subjectFailed, '<br>', (int)$isFailed;
}


?>

<div>
    <div class="flex justify-center items-center w-full h-10 text-4xl font-medium">Student Page</div>
    <div class="h-1/3 flex flex-row">
        <!-- Student details  -->
        <div class="bg-gray-200 mt-4 w-1/2 flex flex-col justify-around px-3 pl-16 ">
            <h2> Name : <?php echo $_SESSION['name'] ?></h2>
            <h2> Reg No : <?php echo $_SESSION['regNo'] ?></h2>
            <h2> Email : <?php echo $_SESSION['email'] ?></h2>
            <h2> Program : <?php echo $_SESSION['program'] ?></h2>

        </div>

        <!-- result status and buttons -->
        <div class=" w-1/2 mt-4 bg-gray-200 py-5 px-4 flex flex-col justify-evenly items-center space-y-2">
            <div class="bg-gray-200 mt-1 w-1/2 flex flex-col justify-around items-center ">
                <h2 class="block mb-2 text-lg font-bold text-blue-900"> Current Semester : <?php echo $_SESSION['currentSemester'] ?></h2>

                <h2 class="block mb-2 text-xl font-bold text-blue-600" <?php echo $viewResult ? 'style="display: none;"' : '' ?>> Result not released</h2>

                <?php echo $viewResult ? '<h2 class="block mb-2 text-m font-bold text-gray-600">SPI: ' . $spi . '</h2>' : '' ?>
                <?php echo $viewResult ? '<h2 class="block mb-2 text-m font-bold text-' . ($isFailed ? 'red' : 'green') . '-600">' . ($isFailed ? 'Failed' : 'Passed') . '</h2>' : '' ?>


            </div>

            <a class="bg-sky-600 h-10 text-center flex items-center text-white py-1 px-3 " href="<?php echo rootUrl . '/pages/transcript.php?regNo=' . $regNo; ?>">View Transcript</a>


        </div>
        <br>

    </div>

    <!-- course details of current semester  -->
    <div class="p-4 mt-5">
        <h2 class="text-xl font-normal text-green-700 px-3">Courses for this semester: </h2>
        <table class="w-full text-center mt-3">
            <thead class="bg-green-600 py-4">
                <tr>
                    <th class="py-2">Name</th>
                    <th class="py-2">Code</th>
                    <th class="py-2">Credit</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Internal Marks</th>
                    <th class="py-2">Mid Sem Marks</th>
                    <th class="py-2">End Sem Marks</th>
                </tr>
            </thead>

            <tbody class="py-4">
                <?php
                $isEmpty = false;

                if (empty($studentCourses)) {
                    $isEmpty = true;
                } else {
                    $totalCredits = 0;
                    array_map(function ($course) {
                        global $totalCredits;
                        $totalCredits = (int)$course['credit'] + $totalCredits;
                        echo '<tr class="bg-green-100 odd:bg-green-300"><td>', $course['courseName'], '</td><td>', $course['courseCode'], '</td><td>', $course['credit'], '</td><td>', $course['isTheory'] == 1 ? 'Theory' : 'Practical', '</td><td>', $course['internal'], '</td><td>', $course['isTheory'] == 0 ? '--' : $course['midsem'], '</td><td>', $course['endsem'], '</td></tr>';
                    }, $studentCourses);
                    echo '<tr class="bg-green-800 text-white mt-2 font-bold"><td>Total Credits</td><td></td><td>', $totalCredits, '</td><td></td><td></td><td></td><td></td></tr>';
                }


                ?>
            </tbody>


        </table>
        <?php
        echo $isEmpty ? '<h3 class="my-4 bg-sky-200 py-2 text-center">No courses available</h3>' : '';
        ?>

    </div>


</div>

<?php

// importing footer component
include './../inc/footer.php';
?>