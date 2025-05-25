<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1:3307";
$username = "root";
$password = "";
$database = "mypetakom";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to preserve form data
$form_username = '';
$error = '';

if (isset($_POST['login'])) {
    $form_username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT student_id, student_pass FROM student WHERE student_username = ?");
    $stmt->bind_param("s", $form_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($student_id, $hashed_pass);
        $stmt->fetch();

        if (password_verify($password, $hashed_pass)) {
            $_SESSION['student_id'] = $student_id;
            
            // Verify the redirect URL exists
            if (file_exists('studentpage.php')) {
                header("Location: studentpage.php");
                exit();
            } else {
                $error = "Student page not found!";
            }
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
        <input type="text" name="username" value="<?php echo htmlspecialchars($form_username); ?>" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <input type="submit" name="login" value="Login">
    </form>
    
    <?php
    // Temporary debug output
    if (isset($_POST['login'])) {
        echo "<hr><h3>Debug Info:</h3>";
        echo "<pre>POST data: "; print_r($_POST); echo "</pre>";
        if (isset($hashed_pass)) {
            echo "<p>Stored hash: $hashed_pass</p>";
        }
    }
    ?>
</body>
</html>