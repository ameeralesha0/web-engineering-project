<?php
include('db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'], $_POST['password'])) {
    $token = mysqli_real_escape_string($db, $_POST['token']);
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Find token
    $query = "SELECT * FROM password_resets WHERE token='$token' AND expires_at > NOW() LIMIT 1";
    $result = mysqli_query($db, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $resetData = mysqli_fetch_assoc($result);
        $email = $resetData['email'];

        // Update password in user tables
        $tables = ['student', 'advisor', 'petakom_coordinator'];
        $updated = false;
        foreach ($tables as $table) {
            $update = "UPDATE $table SET password='$newPassword' WHERE email='$email'";
            if (mysqli_query($db, $update) && mysqli_affected_rows($db) > 0) {
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // Delete token after successful reset
            mysqli_query($db, "DELETE FROM password_resets WHERE token='$token'");
            $message = "Your password has been reset successfully. <a href='login.php'>Login here</a>";
        } else {
            $error = "Password reset failed. User not found.";
        }
    } else {
        $error = "Invalid or expired token.";
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
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <?php if (!isset($message)) : ?>
            <form method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? $_POST['token'] ?? ''); ?>">
                <input type="password" name="password" placeholder="Enter new password" required>
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
