<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No event specified";
    header('Location: view.php');
    exit;
}

// Fetch existing event data
try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param('s', $_GET['id']);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found";
        header('Location: view.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching event: " . $e->getMessage();
    header('Location: view.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['event_name', 'event_description', 'event_date_time', 'event_status', 'event_location'];
    $formErrors = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $formErrors[$field] = "This field is required";
        }
    }

    // File validation
    $approvalLetterPath = $event['approval_letter'];
    if (!empty($_FILES['approval_letter']['name'])) {
        $allowedTypes = ['application/pdf'];
        $maxSize = 5 * 1024 * 1024;

        if ($_FILES['approval_letter']['error'] !== UPLOAD_ERR_OK) {
            $formErrors['approval_letter'] = "File upload error";
        } elseif (!in_array($_FILES['approval_letter']['type'], $allowedTypes)) {
            $formErrors['approval_letter'] = "Only PDF files are allowed";
        } elseif ($_FILES['approval_letter']['size'] > $maxSize) {
            $formErrors['approval_letter'] = "File size exceeds 5MB limit";
        } else {
            $uploadDir = __DIR__ . '/../../uploads/approval_letters/';
            $filename = $event['event_id'] . '_' . basename($_FILES['approval_letter']['name']);
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['approval_letter']['tmp_name'], $targetPath)) {
                $approvalLetterPath = '/uploads/approval_letters/' . $filename;
            }
        }
    }

    if (empty($formErrors)) {
        try {
            $stmt = $conn->prepare("
                UPDATE events SET
                event_name = ?,
                event_description = ?,
                event_date_time = ?,
                event_status = ?,
                event_location = ?,
                approval_letter = ?
                WHERE event_id = ?
            ");
            
            $stmt->bind_param(
                'sssssss',
                $_POST['event_name'],
                $_POST['event_description'],
                $_POST['event_date_time'],
                $_POST['event_status'],
                $_POST['event_location'],
                $approvalLetterPath,
                $event['event_id']
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Event updated successfully";
                header('Location: view.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating event: " . $e->getMessage();
            header('Location: edit.php?id=' . $event['event_id']);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="../../../public/styles/events.css">
</head>
<body>
    <div class="event-form-container">
        <div class="event-form-card">
            <div class="event-form-header text-white">
                <h3>Edit Event: <?= htmlspecialchars($event['event_id']) ?></h3>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <!-- Existing Data -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label form-required">Event Name</label>
                                <input type="text" name="event_name" 
                                    class="form-control event-form-control" 
                                    value="<?= htmlspecialchars($event['event_name']) ?>" 
                                    required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required">Event Status</label>
                                <select name="event_status" class="form-select" required>
                                    <option value="Active" <?= $event['event_status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Postponed" <?= $event['event_status'] === 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                                    <option value="Cancelled" <?= $event['event_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label form-required">Date & Time</label>
                                <input type="datetime-local" name="event_date_time" 
                                    class="form-control" 
                                    value="<?= date('Y-m-d\TH:i', strtotime($event['event_date_time'])) ?>" 
                                    required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Approval Letter</label>
                                <input type="file" name="approval_letter" 
                                    class="form-control" 
                                    accept=".pdf">
                                <?php if ($event['approval_letter']): ?>
                                    <small class="text-muted">Current file: <?= basename($event['approval_letter']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label form-required">Description</label>
                                <textarea name="event_description" 
                                        class="form-control" 
                                        rows="4" 
                                        required><?= htmlspecialchars($event['event_description']) ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required">Location</label>
                                <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                <input type="text" name="event_location" 
                                    class="form-control mt-2" 
                                    value="<?= htmlspecialchars($event['event_location']) ?>" 
                                    required>
                            </div>
                        </div>

                        <div class="event-form-buttons d-flex justify-content-end mt-4">
                            <a href="view.php" class="btn btn-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map with existing coordinates
        const initialCoords = "<?= $event['event_location'] ?>";
        const [latPart, lngPart] = initialCoords.split(', ');
        const lat = parseFloat(latPart.split('*')[0]);
        const lng = parseFloat(lngPart.split('*')[0]);

        const map = L.map('map').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        let marker = L.marker([lat, lng]).addTo(map);
        
        // Update marker on click
        map.on('click', e => {
            const lat = e.latlng.lat.toFixed(4);
            const lng = e.latlng.lng.toFixed(4);
            const latDir = lat >= 0 ? 'N' : 'S';
            const lngDir = lng >= 0 ? 'E' : 'W';
            
            if(marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            
            document.querySelector('[name="event_location"]').value = 
                `${Math.abs(lat)}*${latDir}, ${Math.abs(lng)}*${lngDir}`;
        });
    </script>
</body>
</html>