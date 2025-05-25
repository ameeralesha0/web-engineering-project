<?php
session_start();

// Database connection
$conn = mysqli_connect('127.0.0.1:3307', 'root', '', 'mypetakom');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$error = '';
$username_value = '';
$selected_role = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Store form values for repopulation
    $username_value = htmlspecialchars($username);
    $selected_role = htmlspecialchars($role);

    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Determine table and field names based on role
        switch ($role) {
            case 'student':
                $table = 'student';
                $id_field = 'student_id';
                $username_field = 'student_username';
                $pass_field = 'student_pass';
                $redirect = 'studentpage.php';
                break;
            case 'advisor':
                $table = 'advisor';
                $id_field = 'advisor_id';
                $username_field = 'advisor_username';
                $pass_field = 'advisor_pass';
                $redirect = 'advisorpage.php';
                break;
            case 'coordinator':
                $table = 'petakom_coordinator';
                $id_field = 'staff_id';
                $username_field = 'staff_username';
                $pass_field = 'staff_pass';
                $redirect = 'coordinatorpage.php';
                break;
            default:
                $error = "Invalid role selected.";
                break;
        }

        if (empty($error)) {
            // Prepare SQL statement to prevent SQL injection
            $query = "SELECT $id_field, $pass_field FROM $table WHERE $username_field = ?";
            $stmt = mysqli_prepare($conn, $query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) == 1) {
                    $user = mysqli_fetch_assoc($result);
                    
                    // Verify password (assuming passwords are hashed with password_hash())
                    if (password_verify($password, $user[$pass_field])) {
                        $_SESSION['user_id'] = $user[$id_field];
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $role;
                        
                        // Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Redirect to appropriate page
                        header("Location: $redirect");
                        exit();
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
                
                mysqli_stmt_close($stmt);
            } else {
                $error = "Database error. Please try again later.";
            }
        }
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
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required 
                   value="<?php echo $username_value; ?>">
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student" <?php echo ($selected_role == 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="advisor" <?php echo ($selected_role == 'advisor') ? 'selected' : ''; ?>>Event Advisor</option>
                <option value="coordinator" <?php echo ($selected_role == 'coordinator') ? 'selected' : ''; ?>>Petakom Coordinator</option>
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