<?php
session_start();

// Connect using mysqli
$conn = mysqli_connect("127.0.0.1:3307", "root", "", "student");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === "student") {
        $query = "SELECT student_id, student_pass FROM student WHERE student_username = ?";
    } elseif ($role === "advisor") {
        $query = "SELECT advisor_id, advisor_pass FROM advisor WHERE advisor_username = ?";
    } elseif ($role === "coordinator") {
        $query = "SELECT staff_id, staff_pass FROM petakom_coordinator WHERE staff_username = ?";
    } else {
        $error = "Invalid role selected.";
    }

    if (!isset($error)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_pass);
            $stmt->fetch();

            // If using hashed passwords, verify with password_verify
            if ($password === $hashed_pass) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;

                if ($role === "student") {
                    header("Location: studentpage.php");
                } elseif ($role === "advisor") {
                    header("Location: advisorpage.php");
                } else {
                    header("Location: coordinatorpage.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Username not found.";
        }

        $stmt->close();
    }
    echo "Form submitted!";
    exit();

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
            <button type="submit" name="login">Login</button> <!-- Fixed: name="login" added -->
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
