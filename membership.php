<?php
include('server.php');

if (!isset($_SESSION['username'])) {
    // redirect to login or show message
    header('Location: login.php');
    exit();
}

$uploadMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_card = $_FILES['student_card'] ?? null;

    // Handle file upload only
    if ($student_card && $student_card['error'] === UPLOAD_ERR_OK) {
    $uploadDirectory = 'uploads/';
    $fileName = basename($student_card['name']);
    $destinationPath = $uploadDirectory . $fileName;

    if (move_uploaded_file($student_card['tmp_name'], $destinationPath)) {
        // Update or insert membership record with the uploaded card and set status to 'pending'
        $student_id = $_SESSION['student_id'];

        // Check if membership already exists
        $checkQuery = "SELECT membership_id FROM membership WHERE student_id = ?";
        $stmtCheck = $db->prepare($checkQuery);
        $stmtCheck->bind_param("s", $student_id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Update existing record
            $row = $resultCheck->fetch_assoc();
            $membership_id = $row['membership_id'];

            $updateQuery = "UPDATE membership SET upload_card = ?, status = 'pending' WHERE membership_id = ?";
            $stmtUpdate = $db->prepare($updateQuery);
            $stmtUpdate->bind_param("ss", $fileName, $membership_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            // Insert new membership record
            $insertQuery = "INSERT INTO membership (student_id, upload_card, status, membership_name) VALUES (?, ?, 'pending', 'Your Club Name')";
            $stmtInsert = $db->prepare($insertQuery);
            $stmtInsert->bind_param("ss", $student_id, $fileName);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
        $stmtCheck->close();

        $uploadMessage = "<p class='success'>✅ Your student card has been uploaded successfully.<br>Please wait while we verify your registration.</p>";
    } else {
        $uploadMessage = "<p class='error'>❌ Upload failed. Please try again or check the file format and size.</p>";
    }
    } else {
    $uploadMessage = "<p class='error'>❌ No file uploaded or upload error.</p>";
    }
}
    $student_id = $_SESSION['username'];

    $query = "SELECT * FROM membership WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $membership = $result->fetch_assoc();
    $stmt->close();
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

        <?php if (!empty($uploadMessage)) echo $uploadMessage; ?>

        <form method="post" enctype="multipart/form-data">
            <!-- Since user is logged in, no need to ask username, email, password here -->
            <label>Upload Student Card:</label><br>
            <input type="file" name="student_card" required><br><br>
            <button type="submit">Apply</button>
            <a href="profile.php"><button type="button">Back</button></a>
        </form>
        <?php 
        // Your membership display logic here
    if ($membership) {
    if ($membership['status'] === 'approved') {
        // Show membership details
        echo "<h2>Your Membership Details</h2>";
        echo "Membership ID: " . htmlspecialchars($membership['membership_id']) . "<br>";
        echo "Name: " . htmlspecialchars($membership['membership_name']) . "<br>";
        echo "Status: " . htmlspecialchars($membership['status']) . "<br>";
        echo "<img src='uploads/" . htmlspecialchars($membership['upload_card']) . "' alt='Membership Card' width='200'>";
    } elseif ($membership['status'] === 'rejected') {
        // Show rejection message + reupload form
        echo "<h2>Your membership application was rejected. Please reupload your membership card.</h2>";

        if (isset($_SESSION['upload_error'])) {
            echo "<p style='color:red'>" . $_SESSION['upload_error'] . "</p>";
            unset($_SESSION['upload_error']);
        }

        echo '<form action="upload_membership_card.php" method="post" enctype="multipart/form-data">';
        echo '<input type="file" name="membership_card" required>';
        echo '<button type="submit">Upload Card</button>';
        echo '</form>';
    } else {
        // Pending or other statuses
        echo "<h2>Your membership application is still pending approval.</h2>";
    }
    } else {
        echo "<p>You have not applied for membership yet.</p>";
    }
    ?>
    </div>
</body>
</html>
