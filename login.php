<?php
session_start();
include 'db_connection.php';

$error = "";
$username = "";
$role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username'], $_POST['password'], $_POST['role'])) {
        $error = "Missing required form fields.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Validate role and prepare SQL accordingly
        if ($role == 'student') {
            $sql = "SELECT * FROM student WHERE student_username = ? AND student_pass = ?";
        } elseif ($role == 'advisor') {
            $sql = "SELECT * FROM advisor WHERE advisor_username = ? AND advisor_pass = ?";
        } elseif ($role == 'coordinator') {
            $sql = "SELECT * FROM petakom_coordinator WHERE staff_username = ? AND staff_pass = ?";
        } else {
            $error = "Invalid role selected.";
        }

        if (empty($error)) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;

                    // Redirect based on role
                    if ($role == 'student') {
                        header("Location: studentpage.php");
                    } elseif ($role == 'advisor') {
                        header("Location: advisorpage.php");
                    } elseif ($role == 'coordinator') {
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
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="login-container">
    <div class="form-side">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo" />
        <h2>Login to MyPetakom</h2>
        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <form method="post" action="" novalidate>
            <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>" />
            <input type="password" name="password" placeholder="Password" required />
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student" <?php if ($role == 'student') echo 'selected'; ?>>Student</option>
                <option value="advisor" <?php if ($role == 'advisor') echo 'selected'; ?>>Event Advisor</option>
                <option value="coordinator" <?php if ($role == 'coordinator') echo 'selected'; ?>>Petakom Coordinator</option>
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
