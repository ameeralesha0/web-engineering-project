<?php
session_start();
include 'db_connection.php';

// Check if all required POST data is present
if (!isset($_POST['username'], $_POST['password'], $_POST['role'])) {
    die("Missing required form fields.");
}

$email = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

// Determine SQL based on role
if ($role == 'student') {
    $sql = "SELECT * FROM student WHERE student_username = ? AND student_pass = ?";
} elseif ($role == 'advisor') {
    $sql = "SELECT * FROM advisor WHERE advisor_username = ? AND advisor_pass = ?";
} elseif ($role == 'coordinator') {
    $sql = "SELECT * FROM petakom_coordinator WHERE staff_username = ? AND staff_pass = ?";
} else {
    die("Invalid role selected.");
}
// Prepare and execute the SQL query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;
    
    // Redirect to dashboard (customize this per role if needed)
    header("Location: register.html");
    exit();
} else {
    // Invalid login
    echo "<script>alert('Invalid credentials.'); window.location.href='login.php';</script>";
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
