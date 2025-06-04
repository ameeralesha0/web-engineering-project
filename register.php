<?php
// ✅ Use actual MySQL credentials
$host = "localhost";
$username = "webuser";
$password = "admin1234"; // <-- use your real MySQL root password here
$database = "mypetakom";
$port = 4306;

// ✅ Create DB connection
$conn = new mysqli($host, $username, $password, $database, $port);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else {
    echo "Connected successfully!";
}

// ✅ Only process form if submitted
if (isset($_POST['register'])) {
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $student_course = $_POST['student_course'];
    $student_username = $_POST['student_username'];
    $student_email = $_POST['student_email'];
    $student_pass = password_hash($_POST['student_pass'], PASSWORD_DEFAULT);

    // ✅ Check if username already exists
    $check = $conn->prepare("SELECT student_id FROM student WHERE student_username = ?");
    $check->bind_param("s", $student_username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO student (student_id, student_name, student_course, student_username, student_pass, student_email)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $student_id, $student_name, $student_course, $student_username, $student_pass, $student_email);
        if ($stmt->execute()) {
            $success = "Registration successful. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        body { font-family: Arial; margin: 50px auto; width: 400px; }
        label, input { display: block; margin: 10px 0; }
        input[type=submit] { margin-top: 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Student Registration</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="post">
        <label>Student ID</label>
        <input type="text" name="student_id" required>

        <label>Student Name</label>
        <input type="text" name="student_name" required>

        <label>Course</label>
        <input type="text" name="student_course" required>

        <label>Username</label>
        <input type="text" name="student_username" required>

        <label>Password</label>
        <input type="password" name="student_pass" required>

        <label>Email</label>
        <input type="email" name="student_email" required>

        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>
