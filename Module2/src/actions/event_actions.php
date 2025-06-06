<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

// Handle different actions based on request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'update':
        handleUpdate();
        break;
    case 'delete':
        handleDelete();
        break;
    default:
        $_SESSION['error'] = "Invalid action!";
        header('Location: ../events/view.php');
        exit;
}

function handleCreate() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Invalid request method!";
        header('Location: create.php');
        exit;
    }

    // Validation (same as previous create.php logic)
    $required = ['event_id', 'event_name', 'event_description', 
                'event_date_time', 'event_status', 'event_location'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "All required fields must be filled!";
            header('Location: create.php');
            exit;
        }
    }

    // File upload handling
    $approvalLetterPath = null;
    if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../../public/uploads/approval_letters/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $filename = $_POST['event_id'] . '_' . basename($_FILES['approval_letter']['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['approval_letter']['tmp_name'], $targetPath)) {
            $approvalLetterPath = '/public/uploads/approval_letters/' . $filename;
        }
    }

    // Database insertion
    try {
        $stmt = $conn->prepare("
            INSERT INTO events 
            (event_id, event_name, event_description, event_date_time, 
             event_status, event_location, approval_letter)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            'sssssss',
            $_POST['event_id'],
            $_POST['event_name'],
            $_POST['event_description'],
            $_POST['event_date_time'],
            $_POST['event_status'],
            $_POST['event_location'],
            $approvalLetterPath
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Event created successfully!";
            header('Location: ../events/view.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error creating event: " . $e->getMessage();
        header('Location: create.php');
        exit;
    }
}

function handleUpdate() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Invalid request method!";
        header('Location: ../events/view.php');
        exit;
    }

    // Add update logic here
    // Example:
    // $eventId = $_POST['event_id'];
    // Validate and update database
    // Redirect with success/error message
}

function handleDelete() {
    global $conn;
    
    if (!isset($_GET['id'])) {
        $_SESSION['error'] = "Invalid event ID!";
        header('Location: ../events/view.php');
        exit;
    }

    // Add delete logic here
    // Example:
    // $eventId = $_GET['id'];
    // Delete from database
    // Redirect with success/error message
}

// Close connection
$conn->close();