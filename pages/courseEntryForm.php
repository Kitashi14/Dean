<!-- set status form page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not employee or if course entry no allowed
if (isset($_SESSION['uid']) && isset($_SESSION['category']) && isset($_SESSION['isCourseEntryAllowed'])) {
    if ($_SESSION['category'] != 'employee' || $_SESSION['isCourseEntryAllowed'] == '0') {
        header('Location: ./error.php?error=Bad Request');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}

$programs = ['B.Tech', 'M.Tech', 'MCA'];
$formType = 'Enter';

//checking if update form requested
if (isset($_GET['course_id'])) {

    if (empty($_GET['course_id'])) {
        header('Location: ./error.php?error=Details Not Found');
    } else {

        //storing required variables
        $requestedCourseId = $_GET['course_id'];
        $eid = $_SESSION['eid'];
        $currentSemester = $_SESSION['currentSemester'];
        $fetchSemester = (int)$currentSemester + 1;
        //is case of update form checking authencity
        $sql = "SELECT * FROM course WHERE id = '$requestedCourseId' AND employee_id='$eid' AND semester='$fetchSemester'";
        $result = mysqli_query($conn, $sql);
        $courseDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (empty($courseDetails)) {
            //if no course found
            header('Location: ./error.php?error=Not Found&message=The requested course was not found in database.');
        } else {
            //saving course details
            $courseDetails = $courseDetails[0];
            $formType = 'Update';
        }
    }
}
?>

<!-- set status form  -->
<div class="flex flex-col justify-between p-2 bg-orange-200  container mx-auto w-1/3 mb-15  mt-4 mb-4 rounded-lg border ">
    <form action="<?php echo rootUrl . '/controllers/courseEntry-inc.php' ?>" method="POST" class="px-4 pt-1 pb-2 mb-0 bg-white rounded">
        <input type="text" style="display: none;" name="course_id" value="<?php echo isset($_GET['course_id']) ? $courseDetails['id'] : '' ?>">
        <div class="flex flex-col justify-around  px-3 space-y-2 items-center">
            <h1 for="email" class="block mb-2 text-xl font-bold text-center text-blue-600">
                Semester : <?php echo ((int)$_SESSION['currentSemester'] + 1); ?>
            </h1>
            <div class="block text-xl font-bold text-orange-500">
                <?php echo $formType; ?> Grade Form
            </div>
            <hr class="h-0.5 bg-orange-500 mx-4 w-full">
            <div class="w-full space-y-1">
                <label for="courseName" class="block mb-1 text-sm font-bold text-gray-700">
                    Course name
                </label>
                <input id="courseName" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-3 focus:outline-none focus:shadow-outline" type="text" name="courseName" placeholder="Enter course name" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['courseName'] . '"' : '' ?> required>
            </div>
            <div class="w-full space-y-1">
                <label for="courseCode" class="block mb-1 text-sm font-bold text-gray-700">
                    Course code
                </label>
                <input id="courseCode" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="courseCode" placeholder="Enter course code" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['courseCode'] . '"' : '' ?> required>
            </div>
            <div class="w-full space-y-1">
                <label for="credit" class="block mb-2 text-sm font-bold text-gray-700">
                    Credit
                </label>
                <input id="credit" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" min="0" max="4" name="credit" placeholder="Enter credit for the course" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['credit'] . '"' : '' ?> required>
            </div>
            <div class="w-full space-y-1">
                <label for="program" class="block mt-2 mb-2 text-sm font-bold text-gray-700">
                    Select Program
                </label>
                <select class="w-full border-solid border-2 bg-gray-200" id="program" name="program">
                    <?php
                    array_map(function ($program) {
                        global $formType, $courseDetails;
                        echo '<option class=" text-center" value="', $program, '"', $formType == 'Update' && $courseDetails['program'] == $program ? 'selected' : '', '>', $program, '</option>';
                        return 1;
                    }, $programs);
                    ?>
                </select>
            </div>
            <div class="w-full flex flex-row justify-around">
                <label class="w-1/2 block text-sm font-bold text-gray-700 ml-6" for="">Select type </label>
                <div class="w-1/2">
                    <input id="selectedTheory" type="radio" name="type" value="theory" <?php echo $formType == 'Update' && (bool)$courseDetails['isTheory'] == true ? 'checked' : '' ?> required> Theory <br>
                    <input id="selectedPractical" type="radio" name="type" value="practical" <?php echo $formType == 'Update' && (bool)$courseDetails['isTheory'] == false ? 'checked' : '' ?> required> Practical
                </div>

            </div>
            <div class="w-full space-y-2 ">
                <label for="internal" class="block  text-sm font-bold text-gray-700">
                    Max. marks for internal
                </label>
                <input id="internal" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="internal" min="0" max="100" placeholder="Enter internal marks" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['internal'] . '"' : 'value="0"' ?> required>
            </div>
            <div id="midsemInput" class="w-full space-y-2 ">
                <label for="midSem" class="block  text-sm font-bold text-gray-700">
                    Max. marks for mid sem
                </label>

                <input id="midSem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="midSem" min="0" max="100" placeholder="Enter midsem marks" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['midsem'] . '"' : 'value="0"'  ?> required>
            </div>
            <div class="w-full space-y-2 ">
                <label for="endSem" class="block  text-sm font-bold text-gray-700">
                    Max. marks for end sem
                </label>

                <input id="endSem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="endSem" min="0" max="100" placeholder="Enter endsem marks" <?php echo $formType == 'Update' ? 'value="' . $courseDetails['endsem'] . '"' : 'value="0"' ?> required>
            </div>

            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit" <?php echo $formType == 'Update' ? 'value="UPDATE"' : 'value="ADD"' ?>>
            </div>

        </div>
    </form>
</div>

<script>
    var theoryButton = document.querySelector('#selectedTheory');
    var practicalButton = document.querySelector('#selectedPractical');

    if (practicalButton.checked) {
        document.querySelector('#midsemInput').style.display = "none";
        document.querySelector('#midSem').value = "0";
    }

    theoryButton.addEventListener('click', (e) => {
        document.querySelector('#midsemInput').style.display = "";
    })
    practicalButton.addEventListener('click', (e) => {
        document.querySelector('#midsemInput').style.display = "none";
        document.querySelector('#midSem').value = "0";
    })
</script>

<?php

// importing footer component
include './../inc/footer.php';
?>