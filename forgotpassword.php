<?php
// Start session (optional, depending on needs)
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Dummy email check — replace with DB lookup in real app
    if ($email == 'student@example.com') {
        // Simulate sending a reset link
        $message = "A password reset link has been sent to your email.";
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side2">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Forgot Password</h2>
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
</body>
</html>
