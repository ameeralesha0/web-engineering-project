<?php
session_start();
include 'db_connection.php'; // Your mysqli connection setup

// Initialize variables for form values and error message
$username = "";
$role = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate inputs
    if ($username === "" || $password === "" || $role === "") {
        $error = "Please fill in all fields.";
    } else {
        // Prepare SQL based on role
        if ($role === 'student') {
            $sql = "SELECT * FROM student WHERE student_username = ? AND student_pass = ?";
        } elseif ($role === 'advisor') {
            $sql = "SELECT * FROM advisor WHERE advisor_username = ? AND advisor_pass = ?";
        } elseif ($role === 'coordinator') {
            $sql = "SELECT * FROM petakom_coordinator WHERE staff_username = ? AND staff_pass = ?";
        } else {
            $error = "Invalid role selected.";
        }

        if (empty($error)) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {
                    // Successful login
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;

                    // Redirect to role-specific page
                    if ($role === 'student') {
                        header("Location: studentpage.php");
                    } elseif ($role === 'advisor') {
                        header("Location: advisorpage.php");
                    } else {
                        header("Location: coordinatorpage.php");
                    }
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
                $stmt->close();
            } else {
                $error = "Database error: " . htmlspecialchars($conn->error);
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - MyPetakom</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .login-container { max-width: 400px; margin: 50px auto; }
        input, select, button { width: 100%; padding: 10px; margin: 8px 0; }
        .error { color: red; margin-bottom: 10px; }
        button { background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login to MyPetakom</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>" />
        <input type="password" name="password" placeholder="Password" required />
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="student" <?php if ($role === 'student') echo 'selected'; ?>>Student</option>
            <option value="advisor" <?php if ($role === 'advisor') echo 'selected'; ?>>Event Advisor</option>
            <option value="coordinator" <?php if ($role === 'coordinator') echo 'selected'; ?>>Petakom Coordinator</option>
        </select>
        <button type="submit">Login</button>
    </form>

    <p>
        <a href="register.php">Register</a> | <a href="forgotpassword.php">Forgot Password?</a>
    </p>
</div>
</body>
</html>
