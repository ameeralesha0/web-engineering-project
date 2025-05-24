<?php
session_start();

// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect using mysqli
$conn = mysqli_connect("127.0.0.1:3307", "root", "", "student");

// check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine table and column based on role
    if ($role == "student") {
        $query = "SELECT student_id, student_pass FROM student WHERE student_username = ?";
        $redirect = "studentpage.php";
    } elseif ($role == "advisor") {
        $query = "SELECT advisor_id, advisor_pass FROM advisor WHERE advisor_username = ?";
        $redirect = "advisorpage.php";
    } elseif ($role == "coordinator") {
        $query = "SELECT staff_id, staff_pass FROM petakom_coordinator WHERE staff_username = ?";
        $redirect = "coordinatorpage.php";
    } else {
        $error = "Invalid role selected.";
    }

    if (!isset($error)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $stored_pass);
            $stmt->fetch();

            // Use plain text check only if passwords are not hashed
            if ($password === $stored_pass) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;
                header("Location: $redirect");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Username not found.";
        }

        $stmt->close();
    }
}
if (isset($error)) echo "<p class='error'>$error</p>"; 

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
            <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
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
