<?php
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 

include('db_connection.php');

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

        // Prepare email
        $resetLink = "http://localhost/mypetakom/reset_password.php?token=$token";

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io'; // replace with your Mailtrap host
            $mail->SMTPAuth = true;
            $mail->Username = 'ef2553e427d7c2';       // replace with your Mailtrap username
            $mail->Password = 'd7a2f83cb903e6';       // replace with your Mailtrap password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            // Sender and recipient
            $mail->setFrom('no-reply@mypetakom.com', 'MyPetakom');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'MyPetakom Password Reset';
            $mail->Body    = "Click this link to reset your password: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
