<?php 

session_start();
$conn = mysqli_connect('127.0.0.1:3307', 'root', '' , 'mypetakom') or die ('Unable to connect');
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
        <form method="post" action = "index.php">
            <input type="text" name="username" placeholder="Username" required = "" ></br> 
            <input type="password" name="password" placeholder="Password" required ="" ></br>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="advisor">Event Advisor</option>
                <option value="coordinator">Petakom Coordinator</option>
            </select>
            <button>
                <input type= "submit" name="login" value="Login">
            </button>
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
<?php
    if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $select = mysqli_query($conn, "SELECT * FROM student WHERE username = '$username' AND password = '$password'");
    $row = mysqli_fetch_array($select);

    if(is_array($row)){
        $SESSION["username"] = $row['username'];
        $SESSION["password"] = $row['password'];
    } else {
        echo '<script type = "text/javascript">';
        echo 'alert("Invalid Username or Password")';
        echo 'window.location.href = "index.php"';
        echo '</script>';

    }
}
if(isset($_SESSION["username"])){
    header("Location:login.php");
}
?>
</body>
</html>