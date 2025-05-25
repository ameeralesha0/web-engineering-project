<?php
session_start();
include("db_connection.php");

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($role)) {
        $error = "Please select a role.";
    } else {
        // Map roles to table names and ID fields
        $role_table_map = [
            'student' => ['table' => 'student', 'id_field' => 'student_id', 'username_field' => 'student_username', 'pass_field' => 'student_pass'],
            'advisor' => ['table' => 'advisor', 'id_field' => 'advisor_id', 'username_field' => 'advisor_username', 'pass_field' => 'advisor_pass'],
            'coordinator' => ['table' => 'coordinator', 'id_field' => 'coordinator_id', 'username_field' => 'coordinator_username', 'pass_field' => 'coordinator_pass'],
        ];

        if (!isset($role_table_map[$role])) {
            $error = "Invalid role selected.";
        } else {
            $table = $role_table_map[$role]['table'];
            $id_field = $role_table_map[$role]['id_field'];
            $username_field = $role_table_map[$role]['username_field'];
            $pass_field = $role_table_map[$role]['pass_field'];

            // Prepare dynamic query
            $query = "SELECT $id_field, $pass_field FROM $table WHERE $username_field = ?";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                die("Database error: " . $conn->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $hashed_pass);
                $stmt->fetch();

                if (password_verify($password, $hashed_pass)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role'] = $role;

                    // Redirect to appropriate page
                    switch ($role) {
                        case 'student':
                            header("Location: studentpage.php");
                            break;
                        case 'advisor':
                            header("Location: advisorpage.php");
                            break;
                        case 'coordinator':
                            header("Location: coordinatorpage.php");
                            break;
                    }
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }

            $stmt->close();
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

        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="post">
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
