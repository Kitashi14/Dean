<!-- set status form page  -->
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

$programs = ['B.Tech', 'M.Tech', 'MCA'];

?>

<!-- set status form  -->
<div class="flex flex-col justify-between p-2  container mx-auto w-1/3 mb-15  mt-4 rounded-lg border ">
    <form action="<?php echo rootUrl . '/controllers/enterCourse-inc.php' ?>" method="POST" class="px-4 pt-1 pb-2 mb-10 bg-white rounded">
        <div class="flex flex-col justify-around  px-3 space-y-2 items-center">

            <div class="w-full space-y-2 ">
                <h1 for="email" class="block mb-2 text-xl font-bold text-center text-blue-600">
                    Semester : <?php echo $_SESSION['currentSemester']; ?>
                </h1>
                <label for="courseName" class="block mb-1 text-sm font-bold text-gray-700">
                    Course name
                </label>
                <input id="courseName" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-3 focus:outline-none focus:shadow-outline" type="text" name="courseName" placeholder="Enter course name" required>
                <label for="courseCode" class="block mb-1 text-sm font-bold text-gray-700">
                    Course code
                </label>
                <input id="courseCode" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="courseCode" placeholder="Enter course code" required>
                <label for="credit" class="block mb-2 text-sm font-bold text-gray-700">
                    Credit
                </label>
                <input id="credit" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="credit" placeholder="Enter credit for the course" required>
                <br>
                <label for="program" class="block mt-2 mb-2 text-sm font-bold text-gray-700">
                    Select Program
                </label>
                <select class="w-full border-solid border-2 bg-gray-200" id="program" name="program">
                    <?php
                    array_map(function ($program) {
                        echo '<option class=" text-center" value="' . $program . '">' . $program . '</option>';
                        return 1;
                    }, $programs);
                    ?>
                </select>
            </div>
            <div class="w-full flex flex-row justify-around">
                <label class="w-1/2 block text-sm font-bold text-gray-700 ml-6" for="">Select type </label>
                <div class="w-1/2">
                    <input type="radio" name="type" value="theory" required> Theory <br>
                    <input type="radio" name="type" value="practical" required> Practical
                </div>

            </div>
            <div class="w-full space-y-2 ">
                <label for="internal" class="block mb-2 text-sm font-bold text-gray-700">
                    Max. marks for internal
                </label>
                <input id="internal" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="internal" min="0" max="100" placeholder="Enter internal marks" required>
                <label for="midSem" class="block mb-2 text-sm font-bold text-gray-700">
                    Max. marks for mid sem
                </label>
                <span class="text-xs text-red-500"><i># leave it empty if practical course</i></span>
                <input id="midSem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="midSem" min="0" max="100" placeholder="Enter midsem marks">
                <label for="endSem" class="block mb-2 text-sm font-bold text-gray-700">
                    Max. marks for end sem
                </label>

                <input id="endSem" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="courseCode" min="0" max="100" placeholder="Enter endsem marks" required>
            </div>

            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit" value="Add">
            </div>

        </div>
    </form>
</div>

<?php

// importing footer component
include './../inc/footer.php';
?>