<!-- add user form page  -->
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

$programs = ['B.Tech', 'M.Tech', 'MCA'];

?>

<!-- add user form  -->
<div class="flex flex-col justify-between p-2 bg-orange-200 container mx-auto w-1/3  mt-10 rounded-lg border ">

    <div class="px-8 pt-6 pb-8 mb-0 bg-white rounded">
        <div class="flex flex-col justify-around   p-3 space-y-4 items-center">

            <div class="block text-xl font-bold text-orange-500">
                Add User Form
            </div>
            <hr class="h-0.5 bg-orange-500 mx-4 w-full">

            <!-- select user type radio button  -->
            <div class="w-full flex flex-row justify-around">
                <label class="w-1/2 block text-sm font-bold text-gray-700 ml-6" for="">Select User type : </label>
                <div class="w-1/2">
                    <input id="selectStudent" type="radio" name="type" value="student" required> Student <br>
                    <input id="selectEmployee" type="radio" name="type" value="employee" required> Employee
                </div>
            </div>

            <!-- add student form  -->
            <form id="studentForm" action="<?php echo rootUrl . '/controllers/addUser-inc.php' ?>" method="POST" class="w-full flex flex-col items-center space-y-1" style="display: none;">

                <input type="text" name="userType" value="student" style="display: none;">


                <div class="w-full">
                    <label for="sEmail" class="block mb-0 text-sm font-bold text-gray-700">
                        Email :
                    </label>
                    <input id="sEmail" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="email" placeholder="Enter student email" required>
                </div>
                <div class="w-full ">
                    <label for="sName" class="block mb-0 text-sm font-bold text-gray-700">
                        Name :
                    </label>
                    <input id="sName" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="name" placeholder="Enter student name" required>
                </div>
                <div class="w-full ">
                    <label for="regNo" class="block mb-0 text-sm font-bold text-gray-700">
                        Reg No :
                    </label>
                    <input id="regNo" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="regNo" placeholder="Enter registration number" required>
                </div>

                <div class="w-full">
                    <label for="program" class="block text-sm font-bold text-gray-700">
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
                <div class="pt-5 w-1/3 ">
                    <input type="submit" name="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " value="Add Student">
                </div>

            </form>


            <!-- add employee form  -->
            <form id="employeeForm" action="<?php echo rootUrl . '/controllers/addUser-inc.php' ?>" method="POST" class="w-full flex flex-col items-center space-y-1" style="display: none;">

                <input type="text" name="userType" value="employee" style="display: none;">


                <div class="w-full">
                    <label for="eEmail" class="block mb-0 text-sm font-bold text-gray-700">
                        Email :
                    </label>
                    <input id="eEmail" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="email" placeholder="Enter emlpoyee email" required>
                </div>
                <div class="w-full ">
                    <label for="eName" class="block mb-0 text-sm font-bold text-gray-700">
                        Name :
                    </label>
                    <input id="eName" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="name" placeholder="Enter employee name" required>
                </div>
                <div class="w-full ">
                    <label for="phoneNo" class="block mb-0 text-sm font-bold text-gray-700">
                        Phone No :
                    </label>
                    <input id="phoneNo" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="phoneNo" placeholder="Enter employee phone number" required>
                </div>

                <div class="w-full ">
                    <label for="position" class="block mb-0 text-sm font-bold text-gray-700">
                        Position :
                    </label>
                    <input id="position" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="text" name="position" placeholder="Enter employee position" required>
                </div>
                <div class="pt-5 w-1/3 ">
                    <input type="submit" name="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " value="Add Employee">
                </div>
            </form>

        </div>
    </div>

</div>

<script>
    var studentButton = document.querySelector('#selectStudent');
    var employeeButton = document.querySelector('#selectEmployee');

    studentButton.addEventListener('click', (e) => {
        document.querySelector('#employeeForm').style.display = "none";
        document.querySelector('#studentForm').style.display = "flex";
    })
    employeeButton.addEventListener('click', (e) => {
        document.querySelector('#studentForm').style.display = "none";
        document.querySelector('#employeeForm').style.display = "flex";
    })
</script>


<?php

// importing footer component
include './../inc/footer.php';
?>