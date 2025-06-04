<?php 
include('db_connection.php');
// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password); // Still using md5 as requested

        // Check student table
        $query_student = "SELECT * FROM student WHERE student_username='$username' AND student_pass='$password'";
        $result_student = mysqli_query($db, $query_student);

        // Check advisor table
        $query_advisor = "SELECT * FROM advisor WHERE advisor_username='$username' AND advisor_pass='$password'";
        $result_advisor = mysqli_query($db, $query_advisor);

        // Check coordinator table
        $query_coordinator = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' AND staff_pass='$password'";
        $result_coordinator = mysqli_query($db, $query_coordinator);

        if (mysqli_num_rows($result_student) == 1) {
            $user = mysqli_fetch_assoc($result_student);
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $user['email'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['role'] = 'student';
            header('location: studentpage.php');
        } elseif (mysqli_num_rows($result_advisor) == 1) {
            $user = mysqli_fetch_assoc($result_advisor);
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'advisor';
            header('location: advisorpage.php');
        } elseif (mysqli_num_rows($result_coordinator) == 1) {
            $user = mysqli_fetch_assoc($result_coordinator);
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'coordinator';
            header('location: coordinatorpage.php');
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
?>
<!DOCTYPE html>
<html>
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
  		<form method="post" action="login.php">
  		<?php include('errors.php'); ?>
		<input type="text" name="username" placeholder="Username" required>
		<input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
			<option value="">-- Select Role --</option>
			<option value="student">Student</option>
            <option value="advisor">Event Advisor</option>
            <option value="coordinator">Petakom Coordinator</option>
        </select>
  		<button type="submit" class="btn" name="login_user">Login</button>
  		<div class="links">
            <a href="register.php">Register</a> | <a href="forgotpassword.php">Forgot Password?</a>
        </div>
  		</form>
	</div>
	<div class="title-side">
        <h1>WELCOME</h1>
        <h2>to MyPetakom</h2>
    </div>
</div>
</body>
</html>

