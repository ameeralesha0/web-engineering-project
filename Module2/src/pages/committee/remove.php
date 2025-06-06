<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

$committee_id = $_GET['id'] ?? null;
$error = '';
$member_details = null;

// Fetch committee member details
if ($committee_id) {
    $stmt = $conn->prepare("SELECT c.*, s.student_name, s.student_id, e.event_name, e.event_id 
                          FROM event_committee c
                          JOIN student s ON c.student_id = s.student_id
                          JOIN events e ON c.event_id = e.event_id
                          WHERE c.committee_id = ?");
    $stmt->bind_param("s", $committee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $member_details = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Committee member not found!";
        header("Location: assign.php");
        exit;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: assign.php");
    exit;
}

// Handle deletion confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $deleteStmt = $conn->prepare("DELETE FROM event_committee WHERE committee_id = ?");
    $deleteStmt->bind_param("s", $committee_id);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = "Successfully removed {$member_details['student_name']} from {$member_details['event_name']}";
        header("Location: assign.php?event_id={$member_details['event_id']}");
        exit;
    } else {
        $_SESSION['error'] = "Deletion failed: " . $conn->error;
        header("Location: assign.php?event_id={$member_details['event_id']}");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="../../../public/styles/committee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="committee-container">
        <div class="committee-header">
            <h1 class="committee-title">Confirm Committee Member Removal</h1>
        </div>

        <div class="confirmation-section">
            <?php if ($member_details): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    You are about to permanently remove a committee member!
                </div>

                <div class="member-details">
                    <h3>Member Information</h3>
                    <div class="detail-card">
                        <div class="detail-item">
                            <span class="detail-label">Student Name:</span>
                            <?= htmlspecialchars($member_details['student_name']) ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Student ID:</span>
                            <?= htmlspecialchars($member_details['student_id']) ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Position:</span>
                            <?= htmlspecialchars($member_details['committee_position']) ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Merit Points:</span>
                            <?= htmlspecialchars($member_details['merit_point']) ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Event:</span>
                            <?= htmlspecialchars($member_details['event_name']) ?>
                        </div>
                    </div>
                </div>

                <form method="post" class="confirmation-form">
                    <input type="hidden" name="committee_id" value="<?= $committee_id ?>">
                    
                    <div class="btn-group">
                        <a href="assign.php?event_id=<?= $member_details['event_id'] ?>" 
                           class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger" name="confirm_delete">
                            <i class="fas fa-trash"></i> Confirm Delete
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>