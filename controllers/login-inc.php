<?php
$rootDir = $_SERVER['DOCUMENT_ROOT'] . '/deanproject';
require_once($rootDir . '/config.php');

session_start();

if (isset($_POST['submit'])) {

    require_once($rootDir . '/database.php');

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo '<script>alert("User details not found");';
        echo 'window.location= "./../pages/login.php"; </script>';
    } else {

        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (empty($user)) {
            echo '<script>alert("Email not registered.");';
            echo 'window.location= "./../pages/login.php"; </script>';
        } else {
            if ($password == $user[0]['password']) {

                $user = $user[0];

                $_SESSION['category'] = $user['category'];
                $_SESSION['isAdmin'] = $user['isAdmin'];
                $_SESSION['uid'] = $user['id'];

                $category = $_SESSION['category'];
                $uid = $user['id'];

                $sql = "SELECT * FROM $category WHERE user_id='$uid'";
                $result = mysqli_query($conn, $sql);
                $userData = mysqli_fetch_all($result, MYSQLI_ASSOC);

                if (empty($userData)) {
                    echo '<script>alert("User data not found");';
                    echo 'window.location= "./../pages/login.php"; </script>';
                } else {
                    $userData = $userData[0];
                    print_r($userData);

                    $_SESSION['name'] = $userData['name'];

                    if ($category == 'student') {
                        $_SESSION['regNo'] = $userData['regNo'];
                        $_SESSION['program'] = $userData['program'];

                        header('Location:  ./../pages/student.php');
                    } else {
                        $_SESSION['eid'] = $userData['id'];
                        $_SESSION['position'] = $userData['position'];

                        header("Location:  './../pages/employee.php'");
                    }
                }
            } else {
                echo '<script>alert("Invalid credentials. Please login again");';
                echo 'window.location= "./../pages/login.php"; </script>';
            }
        }
    }
} else {

    echo 'Bad request <br> Go to <a href="' . rootUrl . '/pages/login.php' . '"/>Login</a> page';
}
