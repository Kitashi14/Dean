<!-- set status form page  -->
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
$sql = "SELECT e.name, c.id, c.employee_id, c.courseName, c.program , c.isTheory FROM course c, employee e WHERE c.isSubmitted='0' AND c.semester = '$currentSemester' AND e.id=c.employee_id  ORDER BY e.name ASC";
$result = mysqli_query($conn, $sql);
$notSubmittedCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>

<!-- set status form  -->
<div class="flex flex-col justify-between p-2 bg-orange-200  container mx-auto w-1/3  mt-10 rounded-lg border ">
    <form action="<?php echo rootUrl . '/controllers/setStatus-inc.php' ?>" method="POST"
        class="px-8 pt-6 pb-8 mb-0 bg-white rounded">
        <div class="flex flex-col justify-around  p-3 space-y-4 items-center">

            <div class="w-full space-y-1">
                <label for="semester" class="block mb-2 text-sm font-bold text-center text-gray-700">
                    Semester
                </label>
                <input id="semester"
                    class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline"
                    type="number" name="setSemester" min="<?php echo $currentSemester ?>"
                    max="<?php echo empty($notSubmittedCourses) ? '8' : $currentSemester ?>"
                    value="<?php echo $currentSemester ?>" placeholder="Enter  semester" required>
            </div>
            <br>
            <div class="w-full flex flex-row justify-around items-center">
                <label class="w-1/3 block text-sm font-bold text-gray-700 ml-6" for="">Allowed to enter : </label>
                <div class="w-2/3 flex flex-row justify-evenly">
                    <input type="radio" name="allowed" value="courseEntry" required> Course <br>
                    <input type="radio" name="allowed" value="gradeEntry" required> Grade <br>
                    <input type="radio" name="allowed" value="none" required> none
                </div>

            </div>



            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit"
                    value="Set">
            </div>

        </div>
    </form>
</div>

<?php

// importing footer component
include './../inc/footer.php';
?>