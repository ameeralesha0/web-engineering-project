<?php
include('server.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $user = null;
    $role = '';
    $emailColumn = '';

    // Table => Email column mapping
    $tables = [
        'student' => 'student_email',
        'advisor' => 'advisor_email',
        'petakom_coordinator' => 'staff_email'
    ];

    foreach ($tables as $table => $emailCol) {
        $query = "SELECT * FROM $table WHERE $emailCol = '$email' LIMIT 1";
        $result = mysqli_query($db, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $role = $table;
            $emailColumn = $emailCol;
            break;
        }
    }

    if ($user) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save token into a new table `password_resets`
        $insert = "INSERT INTO password_resets (email, token, expires_at) 
                   VALUES ('$email', '$token', '$expires')";
        mysqli_query($db, $insert);

        // Send reset link
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "MyPetakom Password Reset";
        $body = "Click this link to reset your password: $resetLink";
        $headers = "From: no-reply@mypetakom.com";

        if (mail($email, $subject, $body, $headers)) {
            $message = "A password reset link has been sent to your email.";
        } else {
            $error = "Failed to send reset email. Please try again.";
        }
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
