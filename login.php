<?php
session_start();
include 'db_connection.php';

$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        // Prepare SQL query
        $sql = "SELECT student_pass FROM student WHERE student_username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($db_password);
            $stmt->fetch();

            // Plain text password check (not recommended for production)
            if ($password === $db_password) {
                // Login success
                $_SESSION['username'] = $username;
                header("Location: studentpage.php");
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - MyPetakom</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .error { color: red; }
        .login-container { width: 300px; margin: 50px auto; }
        input, button { width: 100%; padding: 8px; margin: 5px 0; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login to MyPetakom</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>" />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
