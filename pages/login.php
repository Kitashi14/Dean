<?php
include './../inc/header.php';



?>
<div class="flex flex-col justify-between p-2  container mx-auto w-1/3  mt-10 rounded-lg border ">
    <form action="<?php echo rootUrl . '/controllers/login-inc.php' ?>" method="POST" class="px-8 pt-6 pb-8 mb-4 bg-white rounded">
        <div class="flex flex-col justify-around  p-3 space-y-4 items-center">

            <div class="w-full space-y-1">
                <label for="email" class="block mb-2 text-sm font-bold text-gray-700">
                    Email
                </label>
                <input id="email" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none mb-4 focus:outline-none focus:shadow-outline" type="email" placeholder="Enter your email" required>
            </div>

            <div class="w-full space-y-1">
                <label for="password" class="block mb-2 text-sm font-bold text-gray-700">
                    Password
                </label>
                <input id="password" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" type="password" placeholder="Enter your password" required>
            </div>



            <div class="mb-3 w-1/3 bg-red-400">
                <input type="submit" class="w-full h-8 bg-green-800 hover:bg-green-600 text-white " name="submit" value="Log In">
            </div>

        </div>
    </form>
</div>

<?php
include './../inc/footer.php';
?>