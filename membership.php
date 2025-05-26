<!-- register.php -->
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $student_card = $_FILES['student_card'];

    // Dummy upload logic (implement validation and storage)
    move_uploaded_file($student_card['tmp_name'], 'uploads/' . $student_card['name']);
    echo "<p>Registration successful. Please wait for verification.</p>";
}
$previous = $_SERVER['HTTP_REFERER'] ?? 'Location: studentpage.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - MyPetakom</title>
    <link rel="stylesheet" href="membershipstyle.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <img src="logo-petakom.png" alt="MyPetakom Logo" class="logo">
            <h2>Apply for Membership</h2>
        </div>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <label>Upload Student Card:</label><br>
            <input type="file" name="student_card" required><br>
            <button type="submit">Apply</button>
            <a href="profile.php"><button type="button">Back</button></a>
        </form>
    </div>
</body>
</html>
