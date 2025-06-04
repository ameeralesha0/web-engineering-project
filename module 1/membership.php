<?php 
include('db_connection.php');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$uploadMessage = "";

// Get student_id from username
$studentQuery = $db->prepare("SELECT student_id FROM student WHERE student_username = ?");
$studentQuery->bind_param("s", $username);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();

if ($studentResult->num_rows === 0) {
    die("❌ Error: Username '$username' not found.");
}
$student_id = $studentResult->fetch_assoc()['student_id'];
$studentQuery->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['student_card'])) {
    $file = $_FILES['student_card'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowedTypes)) {
        $uploadMessage = "<p class='error'>❌ Only JPG, PNG, and GIF files are allowed.</p>";
    } elseif ($file['size'] > 2 * 1024 * 1024) {
        $uploadMessage = "<p class='error'>❌ File must be under 2MB.</p>";
    } else {
        $uploadDirectory = 'uploads/';
        $fileName = uniqid() . "-" . basename($file['name']);
        $destination = $uploadDirectory . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Check existing membership
            $check = $db->prepare("SELECT membership_id FROM membership WHERE student_id = ?");
            $check->bind_param("s", $student_id);
            $check->execute();
            $existing = $check->get_result();
            
            if ($existing->num_rows > 0) {
                $membership_id = $existing->fetch_assoc()['membership_id'];
                $update = $db->prepare("UPDATE membership SET upload_card = ?, status = 'pending' WHERE membership_id = ?");
                $update->bind_param("ss", $fileName, $membership_id);
                $update->execute();
                $update->close();
            } else {
                // Create new membership ID
                $lastIDRes = $db->query("SELECT membership_id FROM membership ORDER BY membership_id DESC LIMIT 1");
                $newID = "M001";
                if ($lastIDRes->num_rows > 0) {
                    $lastID = $lastIDRes->fetch_assoc()['membership_id'];
                    $num = (int)substr($lastID, 1) + 1;
                    $newID = 'M' . str_pad($num, 3, '0', STR_PAD_LEFT);
                }

                $insert = $db->prepare("INSERT INTO membership (membership_id, student_id, upload_card, status) VALUES (?, ?, ?, 'pending')");
                $insert->bind_param("sss", $newID, $student_id, $fileName);
                $insert->execute();
                $insert->close();
            }
            $check->close();
            $uploadMessage = "<p class='success'>✅ Upload successful. Application is now pending.</p>";
        } else {
            $uploadMessage = "<p class='error'>❌ Upload failed. Please try again.</p>";
        }
    }
}

// Fetch membership info
$fetch = $db->prepare("SELECT * FROM membership WHERE student_id = ?");
$fetch->bind_param("s", $student_id);
$fetch->execute();
$membership = $fetch->get_result()->fetch_assoc();
$fetch->close();
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

        <?= $uploadMessage ?>

        <?php if (!$membership || empty($membership['upload_card'])): ?>
            <form method="post" enctype="multipart/form-data">
                <label>Upload Student Card:</label><br>
                <input type="file" name="student_card" required><br><br>
                <button type="submit">Apply</button>
            </form>
        <?php else: ?>
            <div>
                <strong>Membership ID:</strong> <?= htmlspecialchars($membership['membership_id']) ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($membership['status']) ?><br><br>
                <img src="uploads/<?= htmlspecialchars($membership['upload_card']) ?>" width="200" alt="Uploaded Card"><br><br>

                <?php if ($membership['status'] === 'rejected'): ?>
                    <p class="error">❌ Your membership was rejected. Please re-upload your card.</p>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="student_card" required>
                        <button type="submit">Re-upload</button>
                    </form>
                <?php elseif ($membership['status'] === 'pending'): ?>
                    <p class="info">⏳Your membership is currently under review.</p>
                <?php elseif ($membership['status'] === 'approved'): ?>
                    <p class="success">✅ Your membership has been approved.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 20px;">
            <a href="profile.php"><button type="button">Back to Profile</button></a>
        </div>
    </div>
</body>
</html>
