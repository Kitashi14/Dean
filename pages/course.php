<!-- course grade entry status page  -->
<?php
// importing header component
include './../inc/header.php';

try {
    // checking if required query variables is present in the request
    if (!isset($_GET['course_id'])) {
        //if not redirecting to necessary error page
        header('Location: ./error.php?error=Page not found');
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), ' ';
}


// redirecting if user is not set or not employee 
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'employee') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}

//storing required variables for this page
$currentSemester = $_SESSION['currentSemester'];
$employeeCoursesId = $_SESSION['employeeCoursesId'];

//checking if employee has the access of this course or not
$pageCourseId = $_GET['course_id'];
if (!in_array($pageCourseId, $employeeCoursesId)) {
    //if not redirecting to necessary error page
    header('Location: ./error.php?error=Page not found');
}

//fetching course details
$sql = "SELECT * FROM course WHERE id = '$pageCourseId'";
$result = mysqli_query($conn, $sql);
$courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];



//saving required variables
$program = $courseDetails['program'];
$gradeTable = $courseDetails['isTheory'] == '1' ? 'theorygrade' : 'practicalgrade';
$courseType = $courseDetails['isTheory'] == '1' ? 'Theory' : 'Practical';


//fetching students who are already graded
$sql = "SELECT * FROM student s, $gradeTable g WHERE s.regNo = g.regNo AND g.course_id = '$pageCourseId' AND s.program='$program'";

$result = mysqli_query($conn, $sql);
$studentsAlreadyGraded = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo '<br>';

//fetching students who are not graded
$sql = "SELECT * FROM student S WHERE S.regNo NOT IN (SELECT s.regNo FROM student s, $gradeTable g WHERE s.regNo = g.regNo AND g.course_id = '$pageCourseId' AND s.program='$program') AND S.program = '$program'";


$result = mysqli_query($conn, $sql);
$studentsNotGraded = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<div>
    <h2 class="w-full text-center text-3xl font-semibold text-green-700 p-3"> <?php echo ($courseDetails['isSubmitted'] == '1') ? 'Grade submitted for this course!!' : '' ?> </h2>
    <div class=" bg-gray-300 flex flex-col items-center px-4 pb-4 space-x-4">
        <!-- course details  -->
        <div class=" mt-2 w-full flex flex-row justify-evenly px-3 ">
            <h2 class="block mb-2 text-m font-bold text-blue-900">Course name : <?php echo $courseDetails['courseName'] ?></h2>
            <h2 class="block mb-2 text-m font-bold text-blue-900"> Course code : <?php echo $courseDetails['courseCode'] ?></h2>
            <h2 class="block mb-2 text-m font-bold text-blue-900"> Credit : <?php echo $courseDetails['credit'] ?></h2>
            <h2 class="block mb-2 text-m font-bold text-blue-900"> Program : <?php echo $courseDetails['program'] ?></h2>
            <h2 class="block mb-2 text-m font-bold text-blue-900"> Type : <?php echo  $courseType ?></h2>

        </div>
        <!-- mark distribution of course -->
        <div class="px-0 mt-2 w-full">
            <h2 class="text-xl font-normal text-green-700 px-3 text-center">Marks distribution: </h2>
            <table class="w-full text-center mt-3 ">
                <thead class="bg-green-600 py-4">
                    <tr>
                        <th class="py-2">Internals</th>
                        <?php
                        echo $courseDetails['isTheory'] == '1' ? '<th class="py-2">Mid Sem</th>' : '';
                        ?>
                        <th class="py-2">End Sem</th>

                    </tr>
                </thead>


                <tbody>
                    <tr class="bg-green-100 p-0 odd:bg-green-300">
                        <td class="py-2"><?php echo $courseDetails['internal'] ?></td>
                        <?php
                        echo $courseDetails['isTheory'] ? '<th class="py-2">' . $courseDetails['midsem'] . '</th>' : '';
                        ?>
                        <td class="py-2"><?php echo $courseDetails['endsem'] ?></td>
                    </tr>
                </tbody>


            </table>

        </div>
        <!-- buttons for editing and deleting  -->
        <div class="w-full flex flex-row justify-end items-center p-2 space-x-4" <?php echo ($_SESSION['isCourseEntryAllowed'] == '1') ? '' : 'style="display: none;"' ?>>
            <a class="bg-sky-600 h-10 w-40 justify-center flex items-center text-white py-1 px-3 " href="<?php echo rootUrl, '/pages/courseEntryForm.php?course_id=', $courseDetails['id']; ?>">EDIT</a>
            <a class="bg-red-600 h-10 w-40 justify-center flex items-center text-white py-1 px-3 " href="<?php echo rootUrl, '/controllers/deleteCourse-inc.php?course_id=', $courseDetails['id']; ?>">DELETE</a>
        </div>
    </div>

    <hr class="mt-4  bg-black">
    <!-- students who are already graded -->
    <div class="p-4 mt-5 flex flex-col items-center">
        <h2 class="w-2/3 text-xl font-normal text-sky-700 px-3"> <?php echo ($courseDetails['isSubmitted'] == '1') ? 'Students Grade :' : 'Graded Students : ' ?> </h2>
        <table class="w-2/3 text-center mt-3 ">
            <thead class="bg-sky-600 py-4 te">
                <tr>
                    <th class="py-2">Name</th>
                    <th class="py-2">Reg No</th>
                    <th class="py-2">Internal Marks</th>
                    <?php echo $courseDetails['isTheory'] ==
                        '1' ? '<th class="py-2">Mid Sem Marks</th>' : ''; ?>

                    <th class="py-2">End Sem Marks</th>
                    <th class="py-2">Grade</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $isEmpty = false;

                if (empty($studentsAlreadyGraded)) {
                    $isEmpty = true;
                } else {
                    array_map(function ($student) {
                        global $courseDetails;

                        echo '<tr class="bg-sky-100 p-0 odd:bg-sky-300"><td>', $student['name'], '</td><td>', $student['regNo'], '</td><td>', $student['internal'], '</td>', $courseDetails['isTheory'] == '1' ? '<td>' . $student['midsem'] . '</td>' : '', '<td>', $student['endsem'], '</td><td>', $student['grade'], '</td>', ($_SESSION['isGradeEntryAllowed'] == '1') && ($courseDetails['isSubmitted'] == '0') ? '<td class="py-1 bg-white px-2 w-1/6"><a class=" w-full bg-green-700 text-center flex items-center justify-center text-white py-1 px-3 " href="' . rootUrl . '/pages/gradeEntryForm.php?course_id=' . $courseDetails['id'] . '&regNo=' . $student['regNo'] . '">Change Grade</a></td>' : '', '</tr>';
                    }, $studentsAlreadyGraded);
                }


                ?>
            </tbody>


        </table>

        <?php
        echo $isEmpty ? '<h3 class="mb-4 w-2/3 bg-sky-200 py-2 text-center">No grade added</h3>' : '';
        ?>

    </div>
    <hr class="mt-4 h-0.5 bg-red-800">
    <!-- students who are not graded -->
    <div class="p-4  mb-5" <?php echo ($courseDetails['isSubmitted'] == '1') ? 'style="display: none;"' : '' ?>>

        <div class="flex justify-center flex-col items-center">
            <h2 class="text-xl w-1/2 font-normal text-red-700 px-3">Students to grade : </h2>
            <table class="w-1/2 text-center mt-3">
                <thead class="bg-red-600 py-4">
                    <tr>
                        <th class="py-2">Name</th>
                        <th class="py-2">Reg No</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $isEmpty = false;

                    if (empty($studentsNotGraded)) {
                        $isEmpty = true;
                    } else {
                        array_map(function ($student) {
                            global $courseDetails;
                            echo '<tr class="bg-red-100 p-0 odd:bg-red-300 my-5"><td class="py-2">', $student['name'], '</td><td>', $student['regNo'], '</td>', ($_SESSION['isGradeEntryAllowed'] == '1') && ($courseDetails['isSubmitted'] == '0') ? '<td class="py-1 bg-white px-2 w-1/4"><a class=" w-full bg-green-700 text-center flex items-center justify-center text-white py-1 px-3 " href="' . rootUrl . '/pages/gradeEntryForm.php?course_id=' . $courseDetails['id'] . '&regNo=' . $student['regNo'] . '">Enter Grade</a></td>' : '', '</tr>';
                        }, $studentsNotGraded);
                    }


                    ?>
                </tbody>


            </table>
            <?php
            echo $isEmpty ? '<h3 class=" w-1/2 bg-red-200 py-2 text-center">No students left for grading</h3>' : '';
            ?>
            <form action="<?php echo rootUrl . '/controllers/submitGrade-inc.php' ?>" class=" mt-3 text-center text-red-500 flex flex-col items-center" method="POST" <?php echo ($_SESSION['isGradeEntryAllowed'] == '1') && ($courseDetails['isSubmitted'] == '0') && (empty($studentsNotGraded)) ? '' : 'style="display: none;"' ?>>
                <input type="text" style="display: none;" name="course_id" value="<?php echo $courseDetails['id']; ?>">

                <input type="submit" class=" px-10 my-0 bg-red-500 text-center flex items-center justify-center text-white py-1 px-3 hover:bg-red-700 " name="submit" value="Submit Grade" />
                (Can't change grade after submission)
            </form>
        </div>
    </div>
</div>


<?php

//importing footer component
include './../inc/footer.php';
?>