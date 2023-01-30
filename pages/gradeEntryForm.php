<!-- grade entry form page  -->
<?php

// importing header component
include './../inc/header.php';

try {
    // checking if required query variables is present in the request
    if (!isset($_GET['course_id']) || !isset($_GET['regNo'])) {
        //if not redirecting to necessary error page
        header('Location: ./error.php?error=Not Found');
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), ' ';
}

// redirecting if user is not set or not employee or if grade entry no allowed
if (isset($_SESSION['uid']) && isset($_SESSION['category']) && isset($_SESSION['isGradeEntryAllowed'])) {
    if ($_SESSION['category'] != 'employee' || $_SESSION['isGradeEntryAllowed'] == '0') {
        header('Location: ./error.php?error=Not Found');
    }
} else {
    header('Location: ./error.php?error=Not found');
}

//storing required variables for this page
$currentSemester = $_SESSION['currentSemester'];
$employeeCoursesId = $_SESSION['employeeCoursesId'];
$requestedRegNo = $_GET['regNo'];

//checking if employee has the access of this course or not
$pageCourseId = $_GET['course_id'];
if (!in_array($pageCourseId, $employeeCoursesId)) {
    //if not redirecting to necessary error page
    header('Location: ./error.php?error=Not Found');
}

//fetching course details
$sql = "SELECT * FROM course WHERE id = '$pageCourseId'";
$result = mysqli_query($conn, $sql);
$courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
if (empty($courseDetails)) {
    //if no course found
    header('Location: ./error.php?error=Not Found&message=The requested course was not found in database.');
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
    header('Location: ./error.php?error=Not Found&message=The requested student was not found in database.');
} else {
    $studentDetails = $studentDetails[0];
}

//checking course and student compatibility
if ($studentDetails['program'] != $courseDetails['program']) {
    //if student does not belongs to the course program
    header('Location: ./error.php?error=Bad Request&message=The requested student does not belongs to this course.');
}

//checking if this student is already graded in this course 
$sql = "SELECT * FROM $gradeTable WHERE regNo = '$requestedRegNo' AND course_id='$pageCourseId'";
$result = mysqli_query($conn, $sql);
$gradeDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

//setting type of form
$formType = empty($gradeDetails) ? 'Enter' : 'Update';


?>


<!-- grade entry form  -->
<div class="flex flex-col justify-between p-2 bg-orange-200 container mx-auto w-1/3  mt-10 rounded-lg border ">

    <form action="<?php echo rootUrl . '/controllers/gradeEntry-inc.php' ?>" method="POST" class="px-8 pt-6 pb-8 mb-0 bg-white rounded">

        <input type="text" style="display: none;" name="course_id" value="<?php echo $courseDetails['id']; ?>">

        <input type="text" style="display: none;" name="regNo" value="<?php echo $studentDetails['regNo']; ?>">


        <div class="flex flex-col justify-around   p-3 space-y-4 items-center">

            <div class="block text-xl font-bold text-orange-500">
                <?php echo $formType; ?> Grade Form
            </div>
            <hr class="h-0.5 bg-orange-500 mx-4 w-full">
            <div class="block text-m font-bold text-sky-600">
                <?php echo ucwords($studentDetails['name']), ' (', $requestedRegNo, ')'; ?>
            </div>
            <div class="block mb-2 text-m font-bold text-sky-600">
                Course : <?php echo ucwords($courseDetails['courseName']), ' (', $courseDetails['courseCode'], ')'; ?>
            </div>

            <div class="mb-3 w-1/3 flex flex-col items-center text-red-600">
                (if absent)
                <input type="submit" class="w-full h-8 bg-orange-600 rounded-lg hover:bg-orange-800  text-white " name="submit" value="Absent">

            </div>

            <div class="w-full space-y-1">
                <label for="internal" class="block mb-2 text-sm font-bold text-gray-700">
                    Internal marks :
                </label>
                <input id="internal" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="internal" value="0" min="0" max="<?php echo $courseDetails['internal'] ?>" placeholder="Enter internal marks" required>
            </div>

            <div class="w-full space-y-1" <?php echo  $courseDetails['isTheory'] == '0' ? 'style="display: none;"' : '' ?>>
                <label for="midsem" class="block mb-2 text-sm font-bold text-gray-700">
                    Midsem marks :
                </label>
                <input id="midsem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="midSem" min="0" max="<?php echo $courseDetails['midsem'] ?>" placeholder="Enter midsem marks" value="0" required>
            </div>

            <div class="w-full space-y-1">
                <label for="endsem" class="block mb-2 text-sm font-bold text-gray-700">
                    Endsem marks :
                </label>
                <input id="endsem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="endSem" value="0" min="0" max="<?php echo $courseDetails['endsem'] ?>" placeholder="Enter endsem marks" required>
            </div>





            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit" value="<?php echo $formType ?>">
            </div>

        </div>
    </form>
</div>

<?php

//importing footer component
include './../inc/footer.php';
?>