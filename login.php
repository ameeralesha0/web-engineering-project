<?php
session_start();
include 'db_connection.php'; // Make sure this file sets up $conn

$username = "";
$role = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username === "" || $password === "" || $role === "") {
        $error = "Please fill in all fields.";
    } else {
        if ($role === 'student') {
            $sql = "SELECT * FROM student WHERE student_username = ? AND student_pass = ?";
        } else {
            $error = "Only student login is enabled for this test.";
        }

        if (empty($error)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: studentpage.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - MyPetakom</title>
</head>
<body>
    <h2>Login to MyPetakom</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>"><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="student" <?php if ($role == 'student') echo 'selected'; ?>>Student</option>
        </select><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
