<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No event specified";
    header('Location: view.php');
    exit;
}

try {
    // Get current approval letter path
    $stmt = $conn->prepare("SELECT approval_letter FROM events WHERE event_id = ?");
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Delete from database
    $deleteStmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $deleteStmt->bind_param('s', $_GET['id']);
    
    if ($deleteStmt->execute()) {
        // Delete associated file
        if ($event['approval_letter']) {
            $filePath = __DIR__ . '/../../' . $event['approval_letter'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $_SESSION['success'] = "Event deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting event";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Delete failed: " . $e->getMessage();
}

header('Location: view.php');
exit;
?>