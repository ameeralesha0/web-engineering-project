<?php
session_start();

// Redirect if not logged in or not a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php'; // Include the PDO database connection file

$studentID = $_SESSION['user_id'];
$statusMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $claimID = uniqid('CLAIM_'); // Generate a unique claim ID
    $eventID = $_POST['event_id'] ?? '';
    $eventName = $_POST['event_name'] ?? '';
    $eventLevel = $_POST['event_level'] ?? '';
    $position = $_POST['position'] ?? '';
    $date = $_POST['date'] ?? '';
    // Handle file upload
    $uploadStatus = 'No file uploaded';
    $filePath = null;

    if (isset($_FILES['upload_document']) && $_FILES['upload_document']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/merit_claims/'; // Ensure this directory exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = basename($_FILES['upload_document']['name']);
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['upload_document']['tmp_name'], $filePath)) {
            $uploadStatus = 'File uploaded successfully.';
        } else {
            $uploadStatus = 'Failed to upload file.';
            error_log("Failed to move uploaded file: " . $_FILES['upload_document']['tmp_name'] . " to " . $filePath);
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO merit_claims (claim_id, student_id, event_id, event_name, event_level, position, event_date, document_path, status) VALUES (:claim_id, :student_id, :event_id, :event_name, :event_level, :position, :event_date, :document_path, 'Pending')");

        $stmt->execute([
            ':claim_id' => $claimID,
            ':student_id' => $studentID,
            ':event_id' => $eventID,
            ':event_name' => $eventName,
            ':event_level' => $eventLevel,
            ':position' => $position,
            ':event_date' => $date,
            ':document_path' => $filePath
        ]);

        $statusMessage = "Claim submitted successfully! Status: Pending. " . $uploadStatus;

    } catch (PDOException $e) {
        error_log("Error submitting merit claim: " . $e->getMessage());
        $statusMessage = "Error submitting claim: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Merit - MyPetakom</title>
    <link rel="stylesheet" href="stylepage.css">
    <style>
        /* Specific styles for claim_merit.php */
        .claim-form-container {
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .claim-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .claim-form input[type="text"],
        .claim-form input[type="date"],
        .claim-form select {
            width: calc(100% - 22px); /* Adjust for padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        .claim-form input[type="file"] {
            margin-bottom: 15px;
        }
        .form-actions {
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap; /* Allow buttons to wrap */
        }
        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            min-width: 120px; /* Ensure buttons have a minimum width */
        }
        .form-actions .save-draft-btn {
            background-color: #ffc107; /* Warning yellow */
            color: #333;
        }
        .form-actions .save-draft-btn:hover {
            background-color: #e0a800;
        }
        .form-actions .submit-claim-btn {
            background-color: #28a745; /* Success green */
            color: white;
        }
        .form-actions .submit-claim-btn:hover {
            background-color: #218838;
        }
        .form-actions .delete-btn {
            background-color: #dc3545; /* Danger red */
            color: white;
        }
        .form-actions .delete-btn:hover {
            background-color: #c82333;
        }
        .form-actions .back-btn {
            background-color: #6c757d; /* Secondary gray */
            color: white;
        }
        .form-actions .back-btn:hover {
            background-color: #5a6268;
        }
        .status-message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: green; /* Default to green for success */
        }
        .status-message.error {
            color: red; /* Red for errors */
        }
        .claim-history-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/mypetakom_logo.png" alt="MyPetakom Logo" class="logo">
        <h1>Claim Merit</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="claim-form-container">
        <?php if (!empty($statusMessage)): ?>
            <p class="status-message <?php echo strpos($statusMessage, 'Error') !== false ? 'error' : ''; ?>"><?php echo htmlspecialchars($statusMessage); ?></p>
        <?php endif; ?>

        <form class="claim-form" action="claim_merit.php" method="POST" enctype="multipart/form-data">
            <label for="claim_id">Claim ID:</label>
            <input type="text" id="claim_id" name="claim_id" value="Automatically Generated" disabled>

            <label for="student_id">Student ID:</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($studentID); ?>" disabled>

            <label for="event_id">Event ID:</label>
            <input type="text" id="event_id" name="event_id" required>

            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" required>

            <label for="event_level">Event Level:</label>
            <select id="event_level" name="event_level" required>
                <option value="">Select Level</option>
                <option value="University">University</option>
                <option value="Faculty">Faculty</option>
                <option value="Club">Club</option>
                <option value="National">National</option>
                <option value="International">International</option>
            </select>

            <label for="position">Position:</label>
            <select id="position" name="position" required>
                <option value="">Select Position</option>
                <option value="Participant">Participant</option>
                <option value="Organizer">Organizer</option>
                <option value="Volunteer">Volunteer</option>
                <option value="Winner">Winner</option>
            </select>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="upload_document">Upload Document:</label>
            <input type="file" id="upload_document" name="upload_document" accept=".pdf,.jpg,.jpeg,.png">

            <div class="form-actions">
                <button type="button" class="save-draft-btn" onclick="alert('Save Draft functionality to be implemented.')">Save Draft</button>
                <button type="submit" class="submit-claim-btn">Submit Claim</button>
                <button type="button" class="delete-btn" onclick="alert('Delete functionality to be implemented.')">Delete</button>
                <button type="button" class="back-btn" onclick="location.href='student_homepage.php'">Back</button>
            </div>
        </form>

        <a href="claim_history.php" class="claim-history-link">View Claim History</a>
    </main>
</body>
</html>