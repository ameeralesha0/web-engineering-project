<?php
require "vendor/autoload.php";
include('db_connection.php');

$token = $_GET['token'] ?? '';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = mysqli_real_escape_string($db, $_POST['token']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $check = "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW() LIMIT 1";
        $result = mysqli_query($db, $check);
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $email = $data['email'];

            // Check which table the user belongs to
            $tables = [
                'student' => 'student_email',
                'advisor' => 'advisor_email',
                'petakom_coordinator' => 'staff_email'
            ];

            foreach ($tables as $table => $emailCol) {
            $userQuery = "SELECT * FROM $table WHERE $emailCol = '$email' LIMIT 1";
            $userResult = mysqli_query($db, $userQuery);
            if ($userResult && mysqli_num_rows($userResult) > 0) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Determine correct password column name
            $passwordCol = '';
                if ($table === 'student') {
                    $passwordCol = 'student_pass';
                } elseif ($table === 'advisor') {
                    $passwordCol = 'advisor_pass';
                } elseif ($table === 'petakom_coordinator') {
                    $passwordCol = 'staff_pass';
                }

            // Perform the update
            $update = "UPDATE $table SET $passwordCol = '$hashedPassword' WHERE $emailCol = '$email'";
            mysqli_query($db, $update);

            // Delete the token
            mysqli_query($db, "DELETE FROM password_resets WHERE email = '$email'");
            $success = "Your password has been reset successfully.";
            break;
                }
            }

        } else {
            $errors[] = "Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <div class="form-side2">
        <img src="logo-petakom.png" alt="PETAKOM Logo" class="logo">
        <h2>Reset Password</h2>
        <?php foreach ($errors as $err) echo "<p class='error'>$err</p>"; ?>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if (!$success): ?>
        <form method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
</body>
</html>
