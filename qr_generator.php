<?php
require_once __DIR__ . '/vendor/autoload.php';    
require_once __DIR__ . '/includes/functions.php';  

$events = getAllEvents();
$qrPath = '';
$selectedEvent = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event = getEventById($_POST['event_id']);
    if ($event) {
        $qrData = json_encode([
            'event_id' => $event['event_id'],
            'event_name' => $event['event_name'],
            'date' => $event['event_date'],
            'time' => $event['event_time']
        ]);
        $qrPath = generateQRCode($qrData);
        $selectedEvent = $event;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-qrcode"></i> QR Code Generator</h1>
        </div>

        <a href="index.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>

        <form method="POST">
            <div class="form-group">
                <label for="event_id">Select Event</label>
                <select name="event_id" id="event_id" class="form-control" required>
                    <option value="">Select Event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['event_id'] ?>" <?= isset($_POST['event_id']) && $_POST['event_id'] == $event['event_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['event_name']) ?> (<?= date('M j, Y', strtotime($event['event_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-qrcode"></i> Generate QR Code
            </button>
        </form>

        <?php if ($qrPath && $selectedEvent): ?>
            <div class="qr-container mt-4">
                <h3>QR Code for: <?= htmlspecialchars($selectedEvent['event_name']) ?></h3>
                <p>Date: <?= date('F j, Y', strtotime($selectedEvent['event_date'])) ?></p>
                <p>Time: <?= date('h:i A', strtotime($selectedEvent['event_time'])) ?></p>
                
                <img src="<?= $qrPath ?>" alt="QR Code" class="qr-code">
                
                <div class="mt-3">
                    <a href="<?= $qrPath ?>" download="event_<?= $selectedEvent['event_id'] ?>_qrcode.png" class="btn btn-success">
                        <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>