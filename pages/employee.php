<!-- employee page  -->
<?php

// importing header component
include './../inc/header.php';


// redirecting if user is not set or not employee
if (isset($_SESSION['uid']) && isset($_SESSION['category'])) {
    if ($_SESSION['category'] != 'employee') {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}

$eid = $_SESSION['eid'];
$currentSemester = $_SESSION['currentSemester'];

//fetching employee courses from db for current semester
$sql = "SELECT * FROM course WHERE employee_id = '$eid' AND semester = '$currentSemester'";
$result = mysqli_query($conn, $sql);
$employeeCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);

$employeeCoursesId= array_map(function($course){
    return $course['id'];
},$employeeCourses);

$_SESSION['employeeCoursesId'] = $employeeCoursesId;

?>

<div>
    <div class="flex justify-center items-center w-full h-10 text-4xl font-medium">Employee Page</div>
    <div class="h-1/3 flex flex-row">
        <!-- Employee details  -->
        <div class="bg-gray-200 mt-4 w-1/2 flex flex-col justify-around px-3 pl-16 ">
            <h2> Name : <?php echo $_SESSION['name'] ?></h2>
            <h2> Email : <?php echo $_SESSION['email'] ?></h2>
            <h2> Position : <?php echo $_SESSION['position'] ?></h2>

        </div>

        <!--course entry button for employee  -->
        <div class=" w-1/2 mt-4 bg-gray-200 py-10 px-4 flex flex-col justify-evenly items-center">
            <div class="bg-gray-200 mt-1 w-1/2 flex flex-col justify-around items-center ">
                <h2 class="block mb-2 text-m font-bold text-blue-900"> Current Semester : <?php echo $_SESSION['currentSemester'] ?></h2>

            </div>

            <?php
            //button will be shown only if its allowed
            echo $_SESSION['isCourseEntryAllowed'] == 1 ? '<a class="bg-sky-600 h-10 text-center flex items-center text-white py-1 px-3 " href="' . rootUrl . '/pages/courseEntryForm.php">Enter Course</a>' : '';

            ?>

        </div>
        <br>

    </div>

    <!-- course details of current semester  -->
    <div class="p-0 mt-5">
        <h2 class="text-xl font-normal text-sky-700 px-3">Courses for this semester: </h2>
        <table class="w-full text-center mt-3 ">
            <thead class="bg-sky-500 py-4">
                <tr>
                    <th class="py-2">Name</th>
                    <th class="py-2">Code</th>
                    <th class="py-2">Credit</th>
                    <th class="py-2">Program</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Internal Marks</th>
                    <th class="py-2">Mid Sem Marks</th>
                    <th class="py-2">End Sem Marks</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $isEmpty = false;

                if (empty($employeeCourses)) {
                    $isEmpty = true;
                } else {
                    array_map(function ($course) {
                        echo '<tr class="bg-white p-0 odd:bg-sky-200"><td>', $course['courseName'], '</td><td>', $course['courseCode'], '</td><td>', $course['credit'], '</td><td>', $course['program'], '</td><td>', $course['isTheory'] == 1 ? 'Theory' : 'Practical', '</td><td>', $course['internal'], '</td><td>', $course['midsem'] == '0' ? '--' : $course['midsem'], '</td><td>', $course['endsem'], '</td>', $_SESSION['isGradeEntryAllowed'] ? '<td class="py-1 bg-white px-2"><a class=" w-full bg-green-700 text-center flex items-center justify-center text-white py-1 px-3 " href="' . rootUrl . '/pages/gradeEntry.php?course_id=' . $course['id'] . '">Enter Grade</a></td>' : '', '</tr>';
                    }, $employeeCourses);
                }


                ?>
            </tbody>


        </table>
        <?php
        echo $isEmpty ? '<h3 class="my-4 bg-sky-200 py-2 text-center">No Courses added yet</h3>' : '';
        ?>

    </div>


</div>


<?php

//importing footer component
include './../inc/footer.php';
?>