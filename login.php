<?php
session_start();
include('db_connection.php'); // ensure $db is your DB connection

$errors = [];

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $role     = mysqli_real_escape_string($db, $_POST['role']);

    // Validate inputs
    if (empty($username)) array_push($errors, "Username is required");
    if (empty($password)) array_push($errors, "Password is required");
    if (empty($role)) array_push($errors, "Role is required");

    if (count($errors) == 0) {

        // Choose table and column names based on role
        if ($role == 'student') {
            $query = "SELECT * FROM student WHERE student_username='$username' AND student_pass='$password'";
        } elseif ($role == 'advisor') {
            $query = "SELECT * FROM advisor WHERE advisor_username='$username' AND advisor_pass='$password'";
        } elseif ($role == 'coordinator') {
            $query = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' AND staff_pass='$password'";
        } else {
            array_push($errors, "Invalid role selected");
        }

        // Run query if no errors
        if (count($errors) == 0) {
            $results = mysqli_query($db, $query);
            if (mysqli_num_rows($results) == 1) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                $_SESSION['success'] = "You are now logged in";

                // Redirect based on role
                if ($role == 'student') {
                    header('location: studentpage.php');
                } elseif ($role == 'advisor') {
                    header('location: advisorpage.php');
                } elseif ($role == 'coordinator') {
                    header('location: coordinatorpage.php');
                }
                exit();
            } else {
                array_push($errors, "Wrong username/password/role combination");
            }
        }
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
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student" <?php if (isset($_POST['role']) && $_POST['role'] == 'student') echo 'selected'; ?>>Student</option>
                <option value="advisor" <?php if (isset($_POST['role']) && $_POST['role'] == 'advisor') echo 'selected'; ?>>Event Advisor</option>
                <option value="coordinator" <?php if (isset($_POST['role']) && $_POST['role'] == 'coordinator') echo 'selected'; ?>>Petakom Coordinator</option>
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