<?php
include('server.php'); 
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Map roles to their respective tables and columns
$role_map = [
    'student' => [
        'table' => 'student',
        'username_col' => 'student_username',
        'email_col' => 'student_email',
        'password_col' => 'student_password'  // Adjust if needed
    ],
    'advisor' => [
        'table' => 'advisor',
        'username_col' => 'advisor_username',
        'email_col' => 'advisor_email',
        'password_col' => 'advisor_password'
    ],
    'coordinator' => [
        'table' => 'petakom_coordinator',
        'username_col' => 'staff_username',
        'email_col' => 'staff_email',
        'password_col' => 'staff_password'
    ],
];

if (!array_key_exists($role, $role_map)) {
    die("Invalid user role.");
}

$table = $role_map[$role]['table'];
$username_col = $role_map[$role]['username_col'];
$email_col = $role_map[$role]['email_col'];
$password_col = $role_map[$role]['password_col'];

// Get POST data and trim
$new_username = trim($_POST['username']);
$new_email = trim($_POST['email']);
$new_password = $_POST['password']; // Don't trim password

// Basic validation
if (empty($new_username) || empty($new_email)) {
    die("Username and email are required.");
}

if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Build SQL dynamically based on whether password will be updated
$update_password = false;
$params = [];
$types = "";

// Base query
$query = "UPDATE $table SET $username_col = ?, $email_col = ?";

if (!empty($new_password)) {
    $query .= ", $password_col = ?";
    $update_password = true;
}

$query .= " WHERE $username_col = ?";

$stmt = $db->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

if ($update_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $types = "ssss"; // string, string, string, string
    $stmt->bind_param($types, $new_username, $new_email, $hashed_password, $username);
} else {
    $types = "sss"; // string, string, string
    $stmt->bind_param($types, $new_username, $new_email, $username);
}

if ($stmt->execute()) {
    // Update session username if changed
    if ($new_username !== $username) {
        $_SESSION['username'] = $new_username;
    }
    $_SESSION['email'] = $new_email;

    $_SESSION['success'] = "Profile updated successfully.";
    header('Location: profile.php');
    exit();
} else {
    die("Update failed: " . $stmt->error);
}
