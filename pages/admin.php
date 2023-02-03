<!-- admin page  -->
<?php

// importing header component
include './../inc/header.php';

// redirecting if user is not set or not admin
if (isset($_SESSION['uid']) && isset($_SESSION['isAdmin'])) {
    if (!$_SESSION['isAdmin']) {
        header('Location: ./error.php?error=Page not found');
    }
} else {
    header('Location: ./error.php?error=Page not found');
}

$currentSemester = $_SESSION['currentSemester'];

//fetching employee who have not submitted their courses yet
$sql = "SELECT e.name, c.id, c.employee_id, c.courseName, c.program , c.isTheory FROM course c, employee e WHERE c.isSubmitted='0' AND c.semester = '$currentSemester' AND e.id=c.employee_id";
$result = mysqli_query($conn, $sql);
$notSubmittedCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>
<div>

    <div class="flex justify-center items-center w-full h-10 text-4xl font-medium">Admin Page</div>
    <div class="h-1/3 flex flex-row">

        <!-- Admin details  -->
        <div class="bg-gray-200 mt-4 w-1/2 flex flex-col justify-around px-3 pl-16 ">
            <h2> Name : <?php echo $_SESSION['name'] ?></h2>
            <h2> Email : <?php echo $_SESSION['email'] ?></h2>
            <h2> Category : <?php echo $_SESSION['category'] ?></h2>

        </div>

        <!--current status and  button to set status form page  -->
        <div class=" w-1/2 mt-4 bg-gray-200 py-10 px-4 flex flex-col justify-evenly items-center space-y-2">

            <div class="bg-gray-200 mt-1 w-1/2 flex flex-col justify-around items-center ">
                <h2 class="block mb-2 text-m font-bold text-blue-900"> Current Semester : <?php echo $_SESSION['currentSemester'] ?></h2>
                <h2 class="block mb-2 text-m font-bold text-<?php echo $_SESSION['isCourseEntryAllowed'] ? 'green' : 'red'; ?>-600"> <?php echo $_SESSION['isCourseEntryAllowed'] ? 'Course entry allowed' : 'Course entry blocked'; ?></h2>
                <h2 class="block mb-2 text-m font-bold text-<?php echo $_SESSION['isGradeEntryAllowed'] ? 'green' : 'red'; ?>-600"> <?php echo $_SESSION['isGradeEntryAllowed'] ? 'Grade entry allowed' : 'Grade entry blocked'; ?></h2>

            </div>
            <a class="bg-red-600 h-10 text-center flex items-center text-white py-1 px-3 " href="<?php echo rootUrl, '/pages/statusForm.php'; ?>">Change Status</a>
            <a class="bg-sky-600 h-10 text-center flex items-center text-white py-1 px-3 " href="<?php echo rootUrl, '/pages/addUserForm.php'; ?>">Add User</a>

        </div>
    </div>
    <!-- courses not submitted yet  -->
    <div class="px-5 mt-5" <?php echo ($_SESSION['isGradeEntryAllowed'] == '0') ? 'style="display: none;"' : '' ?>>
        <h2 class="text-xl font-normal text-red-700 px-3">Courses not submitted: </h2>
        <table class="w-full text-center mt-3 ">
            <thead class="bg-red-600 py-4">
                <tr>
                    <th class="py-2">Sr No</th>
                    <th class="py-2">Course ID</th>
                    <th class="py-2">Course Name</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Program</th>
                    <th class="py-2">Employee Name</th>
                    <th class="py-2">Employee Id</th>

                </tr>
            </thead>

            <tbody>
                <?php
                $isEmpty = false;

                if (empty($notSubmittedCourses)) {
                    $isEmpty = true;
                } else {
                    $count = 0;
                    array_map(function ($course) {
                        global $count;
                        $count++;
                        echo '<tr class="bg-red-100 p-0 odd:bg-red-300"><td>', $count, '</td><td>', $course['id'], '</td><td>', $course['courseName'], '</td><td>', $course['isTheory'] == 1 ? 'Theory' : 'Practical', '</td><td>', $course['program'], '</td><td>', $course['name'], '</td><td>', $course['employee_id'], '</td></tr>';
                    }, $notSubmittedCourses);
                }


                ?>
            </tbody>


        </table>
        <?php
        echo $isEmpty ? '<h3 class="my-4 bg-red-200 py-2 text-center">All courses submitted</h3>' : '';
        ?>

    </div>
</div>





<?php

// importing footer component
include './../inc/footer.php';
?>