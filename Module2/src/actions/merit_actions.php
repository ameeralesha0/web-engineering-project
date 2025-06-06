<?php
require_once '../config/db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $conn->real_escape_string($_POST['event_id']);
    $application_text = $conn->real_escape_string($_POST['application_text']);
    $advisor_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO merits 
                          (event_id, advisor_id, application_text) 
                          VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $event_id, $advisor_id, $application_text);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Merit application submitted successfully!";
    } else {
        $_SESSION['error'] = "Error submitting application: " . $conn->error;
    }
    
    header("Location: ../merits/view.php");
    exit();
}