<?php

//importing env var
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

//starting session
session_start();

// handling login POST request
if (isset($_POST['submit']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) {
    //connecting to database
    require_once($rootDir . '/database.php');

    //extracting and checking user form-input of POST request
    $userType = htmlspecialchars($_POST['userType']);
    $email = htmlspecialchars($_POST['email']);
    $name = htmlspecialchars($_POST['name']);

    if (empty($userType) || empty($email) || empty($name)) {
        //if not present then redirecting to form page
        echo '<script>alert("User details not found");';
        echo 'window.location= "./../pages/addUserForm.php"; </script>';
    } else {

        //searching for duplicate entry in user table
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $userPresence = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!empty($userPresence)) {
            echo '<script>alert("A user with this email already present.");';
            echo 'window.location= "./../pages/addUserForm.php"; </script>';
        } else {
            if ($userType == 'student') {

                //extracting and checking student form-input of POST request
                $regNo = htmlspecialchars($_POST['regNo']);
                $program = htmlspecialchars($_POST['program']);

                if (empty($regNo) || empty($program)) {
                    //if not present then redirecting to form page
                    echo '<script>alert("Student details not found");';
                    echo 'window.location= "./../pages/addUserForm.php"; </script>';
                } else {
                    //searching for duplicate entry in student table
                    $sql = "SELECT * FROM student WHERE regNo = '$regNo'";
                    $result = mysqli_query($conn, $sql);
                    $studentPresence = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    if (!empty($studentPresence)) {
                        echo '<script>alert("A student with this regNo already present.");';
                        echo 'window.location= "./../pages/addUserForm.php"; </script>';
                    } else {

                        //sql for inserting user
                        $sql = "INSERT INTO user (id, email, password, category, isAdmin,createdAt) VALUES (NULL, '$email', '$regNo', '$userType', '0', current_timestamp())";

                        //inserting to database
                        $result = mysqli_query($conn, $sql);
                        echo $result;
                        if ($result) {
                            // success

                            //fetching user id of the added user
                            $sql = "SELECT * FROM user WHERE email = '$email'";
                            $result = mysqli_query($conn, $sql);
                            $user_id = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['id'];

                            //sql for inserting student
                            $sql = "INSERT INTO student (user_id, regNo, name, program) VALUES ($user_id, '$regNo', '$name', '$program')";

                            //inserting to database
                            if (mysqli_query($conn, $sql)) {
                                // success
                                echo '<script>alert("Student added successfully");';
                                echo 'window.location= "./../pages/admin.php"; </script>';
                            } else {
                                // error
                                header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                            }
                        } else {
                            // error
                            header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                        }
                    }
                }
            } else {
                $phoneNo = htmlspecialchars($_POST['phoneNo']);
                $position = htmlspecialchars($_POST['position']);

                //extracting and checking employee form-input of POST request
                if (empty($phoneNo) || empty($position)) {
                    //if not present then redirecting to form page
                    echo '<script>alert("Employee details not found");';
                    echo 'window.location= "./../pages/addUserForm.php"; </script>';
                } else {
                    //searching for duplicate entry in employee table
                    $sql = "SELECT * FROM employee WHERE phoneNo = '$phoneNo'";
                    $result = mysqli_query($conn, $sql);
                    $employeePresence = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    if (!empty($employeePresence)) {
                        echo '<script>alert("A employee with this phone no already present.");';
                        echo 'window.location= "./../pages/addUserForm.php"; </script>';
                    } else {

                        //sql for inserting user
                        $sql = "INSERT INTO user (id, email, password, category, isAdmin,createdAt) VALUES (NULL, '$email', '$phoneNo', '$userType', '0', current_timestamp())";

                        //inserting to database
                        $result = mysqli_query($conn, $sql);
                        if ($result) {
                            // success

                            //fetching user id of the added user
                            $sql = "SELECT * FROM user WHERE email = '$email'";
                            $result = mysqli_query($conn, $sql);
                            $user_id = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['id'];


                            //sql for inserting employee
                            $sql = "INSERT INTO employee (id, user_id, name, position, phoneNo) VALUES (NULL, $user_id, '$name', '$position','$phoneNo')";

                            //inserting to database
                            if (mysqli_query($conn, $sql)) {
                                // success
                                echo '<script>alert("Employee added successfully");';
                                echo 'window.location= "./../pages/admin.php"; </script>';
                            } else {
                                // error
                                header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                            }
                        } else {
                            // error
                            header('Location: ./../pages/error.php?error=' . mysqli_error($conn));
                        }
                    }
                }
            }
        }
    }
} else {
    // handling directing access of this file 
    header('Location: ./../pages/error.php?error=Page not found 1');
}
