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
            echo $_SESSION['isCourseEntryAllowed'] == 1 ? '<a class="bg-sky-600 h-10 text-center flex items-center text-white py-1 px-3 " href="'. rootUrl . '/pages/courseEntryForm.php">Enter Course</a>' : '';

            ?>

            <?php
            // echo $_SESSION['isGradeEntryAllowed'] == 1 ? '<a class="bg-sky-600 h-10 text-center flex items-center text-white py-1 px-3 " href="'. rootUrl . '/pages/gradeEntryForm.php">Enter Grade </a>' : '';

            ?>

        </div>
    </div>

</div>


<?php

//importing footer component
include './../inc/footer.php';
?>