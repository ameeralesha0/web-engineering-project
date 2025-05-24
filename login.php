<?php
session_start();
include 'db_connection.php'; // Make sure $conn is properly initialized

$error = "";

// Redirect URLs based on role
$redirect_pages = [
    'student' => 'studentpage.php',
    'advisor' => 'advisorpage.php',
    'coordinator' => 'coordinatorpage.php'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username'], $_POST['password'], $_POST['role'])) {
        $error = "Please fill in all required fields.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Validate role
        if (!array_key_exists($role, $redirect_pages)) {
            $error = "Invalid role selected.";
        } else {
            // Prepare SQL based on role
            switch ($role) {
                case 'student':
                    $table = 'student';
                    $user_col = 'student_username';
                    $pass_col = 'student_pass';
                    break;
                case 'advisor':
                    $table = 'advisor';
                    $user_col = 'advisor_username';
                    $pass_col = 'advisor_pass';
                    break;
                case 'coordinator':
                    $table = 'petakom_coordinator';
                    $user_col = 'staff_username';
                    $pass_col = 'staff_pass';
                    break;
            }

            $sql = "SELECT $pass_col FROM $table WHERE $user_col = ? LIMIT 1";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($hashed_password);
                    $stmt->fetch();

                    // Verify password using password_verify()
                    if (password_verify($password, $hashed_password)) {
                        // Successful login
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $role;
                        header("Location: " . $redirect_pages[$role]);
                        exit();
                    } else {
                        $error = "Invalid username or password.";
                    }
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
    <meta charset="UTF-8">
    <title>Login - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Login to MyPetakom</h2>
        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <form method="post" novalidate>
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

