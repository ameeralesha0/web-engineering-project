<?php
session_start();
$host = "127.0.0.1:3307";
$username = "root";      // use your DB username
$password = "";          // use your DB password
$database = "mypetakom";   // your DB name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT student_id, student_pass FROM student WHERE student_username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($student_id, $hashed_pass);
        $stmt->fetch();

        if (password_verify($password, $hashed_pass)) {
            $_SESSION['student_id'] = $student_id;
            header("Location: studentpage.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; margin: 50px auto; width: 300px; }
        label, input { display: block; margin: 10px 0; }
        input[type=submit] { margin-top: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Student Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>
