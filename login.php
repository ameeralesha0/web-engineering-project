<?php
session_start();

// Connect using mysqli
$conn = mysqli_connect("127.0.0.1:3307", "root", "", "student");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine table and column names based on role
    if ($role === 'student') {
        $table = 'student';
        $username_col = 'student_username';
        $password_col = 'student_pass';
        $redirect = 'studentpage.php';
    } elseif ($role === 'advisor') {
        $table = 'advisor';
        $username_col = 'advisor_username';
        $password_col = 'advisor_pass';
        $redirect = 'advisorpage.php';
    } elseif ($role === 'coordinator') {
        $table = 'petakom_coordinator';
        $username_col = 'staff_username';
        $password_col = 'staff_pass';
        $redirect = 'coordinatorpage.php';
    } else {
        $error = "Invalid credentials.";
        exit();
    }

}
mysqli_close($conn);
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
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="student">Student</option>
                <option value="advisor">Event Advisor</option>
                <option value="coordinator">Petakom Coordinator</option>
            </select>
            <button type="submit">Login</button>
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
