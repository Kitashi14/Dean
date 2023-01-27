<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

// handling login POST request
if (isset($_POST['submit'])) {

    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting form-input of POST request
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    //checking if form-input are present
    if (empty($email) || empty($password)) {

        //if not present then redirecting to login page
        echo '<script>alert("User details not found");';
        echo 'window.location= "./../pages/login.php"; </script>';

    } else {

        //fetching user as per provided email 
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);


        //checking if user found
        if (empty($user)) {

            //if user not found then redirecting to login page
            echo '<script>alert("Email not registered.");';
            echo 'window.location= "./../pages/login.php"; </script>';
        } else {

            //checking whether password is matching or not 
            if ($password == $user[0]['password']) {
                
                //when password matched
                $user = $user[0];

                //storing data of user to variables
                $category = $user['category'];
                $isAdmin = $user['isAdmin'];
                $uid = $user['id'];

                // fetching data of user as per category
                $sql = "SELECT * FROM $category WHERE user_id='$uid'";
                $result = mysqli_query($conn, $sql);
                $userData = mysqli_fetch_all($result, MYSQLI_ASSOC);

                //checking if user data found
                if (empty($userData)) {

                    // if not found then redirect to login page with necessary alert
                    echo '<script>alert("User data not found");';
                    echo 'window.location= "./../pages/login.php"; </script>';
                } else {

                    // if found then storing required data to session variables
                    $userData = $userData[0];

                    $_SESSION['uid'] = $uid;
                    $_SESSION['isAdmin'] = $isAdmin;
                    $_SESSION['category'] = $category;
                    $_SESSION['email'] = $email;
                    $_SESSION['name'] = $userData['name'];

                    //storing session variables as per category
                    if ($category == 'student') {
                        $_SESSION['regNo'] = $userData['regNo'];
                        $_SESSION['program'] = $userData['program'];

                        header('Location:  ./../pages/student.php');
                    } else {
                        $_SESSION['eid'] = $userData['id'];
                        $_SESSION['position'] = $userData['position'];

                        header('Location:  ./../pages/employee.php');
                    }


                }
            } 
            //if password not matched then redirecting to login page with necessary alert
            else {
                echo '<script>alert("Invalid credentials. Please login again");';
                echo 'window.location= "./../pages/login.php"; </script>';
            }
        }
    }
} else {
    // handling directing access of this file 
    echo 'Bad request <br> Go to <a href="' . rootUrl . '/pages/login.php' . '"/>Login</a> page';
}
