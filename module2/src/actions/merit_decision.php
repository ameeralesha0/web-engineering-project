<?php
require_once '../config/db_config.php';
session_start();

// Verify coordinator role
if ($_SESSION['user_role'] !== 'coordinator') {
    header("Location: ../pages/dashboard/");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merit_id = $conn->real_escape_string($_POST['merit_id']);
    $decision = $conn->real_escape_string($_POST['decision']);
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');
    $coordinator_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE merits SET 
                          status = ?,
                          decision_notes = ?,
                          coordinator_id = ?,
                          updated_at = NOW()
                          WHERE id = ?");
    $stmt->bind_param("ssii", $decision, $notes, $coordinator_id, $merit_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Application decision updated!";
    } else {
        $_SESSION['error'] = "Error updating decision: " . $conn->error;
    }
    
    header("Location: ../merits/approvals.php");
    exit();
}