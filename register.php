<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Dummy storage (replace with DB insert)
    // You can also hash the password here using password_hash()

    echo "<script>alert('Account for $name registered successfully!'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side1" >
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Register a New Account</h2>
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="advisor">Event Advisor</option>
                <option value="coordinator">Petakom Coordinator</option>
            </select>
            <button type="submit">Register</button>
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
