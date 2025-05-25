<?php
session_start();
include('db_connection.php'); // ensure $db is your DB connection

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT student_id, student_pass FROM student WHERE student_username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($student_id, $hashed_pass);
        $stmt->fetch();

        if (password_verify($password, $hashed_pass)) {
            $_SESSION['student_id'] = $student_id;
            header("Location: studentpage.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }
    $stmt->close();
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