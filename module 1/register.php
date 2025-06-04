<?php 
include('db_connection.php');
// REGISTER USER
if (isset($_POST['reg_user'])) {
    // receive all input values from the form
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $role = mysqli_real_escape_string($db, $_POST['role']);

    // form validation
    if (empty($name)) { array_push($errors, "Name is required"); }
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }
    if (empty($id)) { array_push($errors, "ID is required"); }
    if (empty($role)) { array_push($errors, "Role is required"); }

    // check if user already exists
    if ($role === 'student') {
        $_SESSION['student_id'] = $id;
        $check_query = "SELECT * FROM student WHERE student_username='$username' OR student_email='$email' OR student_id='$id' LIMIT 1";  
    } elseif ($role === 'advisor') {
        $check_query = "SELECT * FROM advisor WHERE advisor_username='$username' OR advisor_email='$email' OR advisor_id='$id' LIMIT 1";
    } elseif ($role === 'coordinator') {
        $check_query = "SELECT * FROM petakom_coordinator WHERE staff_username='$username' OR staff_email='$email' OR staff_id='$id' LIMIT 1";
    } else {
        array_push($errors, "Invalid user role selected");
    }

    if (!empty($check_query)) {
        $result = mysqli_query($db, $check_query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            if (
                ($role === 'student' && $user['student_username'] === $username) ||
                ($role === 'advisor' && $user['advisor_username'] === $username) ||
                ($role === 'coordinator' && $user['staff_username'] === $username)
            ) {
                array_push($errors, "Username already exists");
            }

            if (
                ($role === 'student' && $user['student_email'] === $email) ||
                ($role === 'advisor' && $user['advisor_email'] === $email) ||
                ($role === 'coordinator' && $user['staff_email'] === $email)
            ) {
                array_push($errors, "Email already exists");
            }

            if (
                ($role === 'student' && $user['student_id'] === $id) ||
                ($role === 'advisor' && $user['advisor_id'] === $id) ||
                ($role === 'coordinator' && $user['staff_id'] === $id)
            ) {
                array_push($errors, "ID already exists");
            }
        }
    }

    // Register user if no errors
    if (count($errors) == 0) {
        $password = md5($password); // Still using md5 as requested

        if ($role === 'student') {
            $query = "INSERT INTO student (student_id, student_name, student_username, student_email, student_pass) 
                      VALUES ('$id', '$name', '$username', '$email', '$password')";
        } elseif ($role === 'advisor') {
            $query = "INSERT INTO advisor (advisor_id, advisor_name, advisor_username, advisor_email, advisor_pass) 
                      VALUES ('$id', '$name', '$username', '$email', '$password')";
        } elseif ($role === 'coordinator') {
            $query = "INSERT INTO petakom_coordinator (staff_id, staff_name, staff_username, staff_email, staff_pass) 
                      VALUES ('$id', '$name', '$username', '$email', '$password')";
        }

        if (!empty($query)) {
            mysqli_query($db, $query);
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['success'] = "You are now registered";
            header('location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side1">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Register a New Account</h2>
        <form method="post" action="register.php">
            <?php include('errors.php'); ?>

            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="Username" required>
            <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="Email" required>
            <input type="text" name="id" placeholder="ID" required>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="advisor">Event Advisor</option>
                <option value="coordinator">Petakom Coordinator</option>
            </select>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" class="btn" name="reg_user">Register</button>
        </form>
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>

    <div class="title-side">
        <h1>WELCOME</h1>
        <p>Join the PETAKOM community today!</p>
    </div>
</div>
</body>
</html>
