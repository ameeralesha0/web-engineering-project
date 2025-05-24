<?php
session_start();
include 'db_connection.php';

// Check if all required POST data is present
if (!isset($_POST['email'], $_POST['password'], $_POST['role'])) {
    die("Missing required form fields.");
}

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

// Prepare and execute the SQL query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists and password matches
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $stored_pass = $row[array_keys($row)[1]];

    // Check plain text password (replace with password_verify if using hashes)
    if ($password === $stored_pass) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header("Location: $redirect");
        exit();
    } else {
        echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('Username not found.'); window.location.href='login.php';</script>";
}

$stmt->close();
$conn->close();
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
