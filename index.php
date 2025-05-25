<?php 
session_start();
$conn = mysqli_connect('127.0.0.1:3307', 'root', '', 'mypetakom') or die ('Unable to connect');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $table = "";
    $user_field = "";
    $pass_field = "";

    // Identify the table and fields based on the role
    if ($role == "student") {
        $table = "student";
        $user_field = "student_username";
        $pass_field = "student_pass";
    } elseif ($role == "advisor") {
        $table = "advisor";
        $user_field = "advisor_username";
        $pass_field = "advisor_pass";
    } elseif ($role == "coordinator") {
        $table = "petakom_coordinator";
        $user_field = "staff_username";
        $pass_field = "staff_pass";
    }

    // Query the appropriate table
    $query = "SELECT * FROM $table WHERE $user_field = '$username' AND $pass_field = '$password'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);

    if (is_array($row)) {
        $_SESSION['username'] = $row[$user_field];
        $_SESSION['role'] = $role;

        // Redirect to dashboard based on role
        if ($role == "student") {
            header("Location: studentpage.php");
        } elseif ($role == "advisor") {
            header("Location: advisorpage.php");
        } elseif ($role == "coordinator") {
            header("Location: coordinatorpage.php");
        }
        exit();
    } else {
        echo '<script>alert("Invalid Username or Password");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Login to MyPetakom</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required></br> 
            <input type="password" name="password" placeholder="Password" required></br>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="advisor">Event Advisor</option>
                <option value="coordinator">Petakom Coordinator</option>
            </select>
            <button type="submit" name="login">Login</button>
        </form>

        <div class="links">
            <a href="register.php">Register</a> | <a href="forgotpassword.php">Forgot Password?</a>
        </div>
    </div>
    <div class="title-side">
        <h1>WELCOME</h1>
        <h2>to MyPetakom</h2>
    </div>
</div>
</body>
</html>
