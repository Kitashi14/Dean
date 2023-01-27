<!-- set status form page  -->
<?php

// importing div component
include './../inc/header.php';

// redirecting if user is not set or not admin
if (isset($_SESSION['uid']) && isset($_SESSION['isAdmin'])) {
    if (!$_SESSION['isAdmin']) {
        header('Location : ./error.php?error=Page no found');
    }
} else {
    header('Location : ./error.php?error=Page no found');
}


?>

<!-- set status form  -->
<div class="flex flex-col justify-between p-2  container mx-auto w-1/3  mt-10 rounded-lg border ">
    <form action="<?php echo rootUrl . '/controllers/setStatus-inc.php' ?>" method="POST" class="px-8 pt-6 pb-8 mb-4 bg-white rounded">
        <div class="flex flex-col justify-around  p-3 space-y-4 items-center">

            <div class="w-full space-y-1">
                <label for="email" class="block mb-2 text-sm font-bold text-center text-gray-700">
                    Semester
                </label>
                <input id="email" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="number" name="currentSemester" min="1" max="8" placeholder="Enter current semester" required>
            </div>
            <br>
            <div class="w-full flex flex-row justify-between">
                <label class="w-1/2 block text-sm font-bold text-gray-700 ml-6" for="">Course entry allowed ?</label>
                <div class="w-1/2">
                    <input type="radio" name="isCourseEntryAllowed" value="yes" required> YES <br>
                    <input type="radio" name="isCourseEntryAllowed" value="no" required> NO
                </div>

            </div>
            <br>
            <div class="w-full flex flex-row justify-between">
                <label class="w-1/2 block text-sm font-bold text-gray-700 ml-6" for="">Grade entry allowed ?</label>
                <div class="w-1/2">
                    <input type="radio" name="isGradeEntryAllowed" value="yes" required> YES <br>
                    <input type="radio" name="isGradeEntryAllowed" value="no" required> NO
                </div>

            </div>



            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit" value="Set">
            </div>

        </div>
    </form>
</div>

<?php

// importing footer component
include './../inc/footer.php';
?>