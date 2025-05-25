<?php
session_start();
include('db_connection.php');

// Initialize error message
$error = '';

if (isset($_POST['login'])) {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate role selection
    if (empty($role)) {
        $error = "Please select a role.";
    } else {
        // Map roles to table names and fields
        $role_table_map = [
            'student' => [
                'table' => 'student', 
                'id_field' => 'student_id', 
                'username_field' => 'student_username', 
                'pass_field' => 'student_pass',
                'redirect' => 'studentpage.php'
            ],
            'advisor' => [
                'table' => 'advisor', 
                'id_field' => 'advisor_id', 
                'username_field' => 'advisor_username', 
                'pass_field' => 'advisor_pass',
                'redirect' => 'advisorpage.php'
            ],
            'coordinator' => [
                'table' => 'petakom_coordinator', 
                'id_field' => 'staff_id', 
                'username_field' => 'staff_username', 
                'pass_field' => 'staff_pass',
                'redirect' => 'coordinatorpage.php'
            ],
        ];

        if (!isset($role_table_map[$role])) {
            $error = "Invalid role selected.";
        } else {
            $config = $role_table_map[$role];
            
            try {
                // Prepare and execute query
                $query = "SELECT {$config['id_field']}, {$config['pass_field']} 
                          FROM {$config['table']} 
                          WHERE {$config['username_field']} = ?";
                $stmt = $conn->prepare($query);
                
                if (!$stmt) {
                    throw new Exception("Database error: " . $conn->error);
                }

                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $hashed_pass);
                    $stmt->fetch();

                    // Verify password
                    if (password_verify($password, $hashed_pass)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['role'] = $role;
                        $_SESSION['username'] = $username;
                        
                        // Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Redirect to appropriate page
                        header("Location: " . $config['redirect']);
                        exit();
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
                
                $stmt->close();
            } catch (Exception $e) {
                error_log($e->getMessage());
                $error = "A system error occurred. Please try again later.";
            }
        }
    }
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

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
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