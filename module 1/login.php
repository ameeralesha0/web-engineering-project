<?php 
session_start();
include('db_connection.php');
$errors = [];

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password']; 
    $role = $_POST['role'];

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    if (empty($role)) {
        array_push($errors, "Role is required");
    }

    if (count($errors) == 0) {
        // Role-based login
        if ($role === 'student') {
            $query = "SELECT * FROM student WHERE student_username='$username' LIMIT 1";
            $result = mysqli_query($db, $query);
            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['student_pass'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $user['student_email'];
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['role'] = 'student';
                    header('location: studentpage.php');
                    exit;
                }
            }
        } elseif ($role === 'advisor') {
            $query = "SELECT * FROM advisor WHERE advisor_username='$username' LIMIT 1";
            $result = mysqli_query($db, $query);
            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['advisor_pass'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $user['advisor_email'];
                    $_SESSION['role'] = 'advisor';
                    header('location: advisorpage.php');
                    exit;
                }
            }
        } elseif ($role === 'coordinator') {
            $query = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' LIMIT 1";
            $result = mysqli_query($db, $query);
            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['staff_pass'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $user['staff_email'];
                    $_SESSION['role'] = 'coordinator';
                    header('location: coordinatorpage.php');
                    exit;
                }
            }
        }

        array_push($errors, "Wrong username/password combination.");
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

