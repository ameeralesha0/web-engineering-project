<?php include('server.php'); ?>

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
