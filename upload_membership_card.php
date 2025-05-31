<?php 
include('server.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['membership_card'])) {
    $file = $_FILES['membership_card'];

    // Validate file (type, size)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['upload_error'] = "Only JPG, PNG, and GIF files are allowed.";
        header("Location: membership_page.php");
        exit();
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        $_SESSION['upload_error'] = "File size must be under 2MB.";
        header("Location: membership_page.php");
        exit();
    }

    // Move uploaded file
    $uploads_dir = 'uploads';
    $filename = uniqid() . "-" . basename($file['name']);
    $target_path = $uploads_dir . "/" . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Update membership record in DB:
        // Reset status to pending and update upload_card filename
        $query = "UPDATE membership SET upload_card = ?, status = 'pending' WHERE student_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", $filename, $student_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "File uploaded successfully. Your membership application is pending approval.";
        } else {
            $_SESSION['upload_error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['upload_error'] = "Failed to upload file.";
    }
} else {
    $_SESSION['upload_error'] = "No file uploaded.";
}

header("Location: membership_page.php");
exit();
?>