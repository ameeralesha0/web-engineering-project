<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../../../vendor/autoload.php'; // Adjust path if needed

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$qrImage = '';
$selectedEventId = $_POST['event_id'] ?? null;

// Fetch all events for the dropdown
$eventList = $conn->query("SELECT event_id, event_name FROM events");

// If the form is submitted
if (isset($_POST['generate']) && $selectedEventId) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("s", $selectedEventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Create QR data
        $eventData = "Event: {$row['event_name']}\n"
                   . "Date & Time: {$row['event_date_time']}\n"
                   . "Status: {$row['event_status']}\n"
                   . "Location: {$row['event_location']}\n"
                   . "Description: {$row['event_description']}";

        // QR code options
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => true
        ]);

        $qrImage = (new QRCode($options))->render($eventData);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate QR Code</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/styles/qrcode.css">

</head>
<body>

<div class="qr-container mt-5">
    <div class="qr-header">
        <h2>Generate QR Code for Your Event</h2>
    </div>

    <form method="POST" class="qr-form my-4">
        <label for="event_id" class="form-label">Select Event</label>
        <select name="event_id" id="event_id" class="form-select mb-3" required>
            <option value="">-- Choose Event --</option>
            <?php while ($event = $eventList->fetch_assoc()): ?>
                <option value="<?= $event['event_id'] ?>" <?= $selectedEventId === $event['event_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($event['event_name']) ?> (<?= $event['event_id'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="generate" class="btn btn-primary">Generate QR Code</button>
    </form>

    <?php if ($qrImage): ?>
        <div class="qr-preview">
            <h4>Scan this QR Code to see event details:</h4>
            <img id="qrImage" src="<?= $qrImage ?>" alt="QR Code">
            <div class="download-btn">
                <a id="downloadLink" href="<?= $qrImage ?>" download="event_qrcode.png" class="btn btn-success">
                    Download QR Code
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
