<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

$formErrors = [];
$preservedValues = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preserve user input
    $preservedValues = $_POST;
    $preservedFiles = $_FILES;

    // Validate required fields
    $required = [
        'event_id' => 'Event ID',
        'event_name' => 'Event Name',
        'event_description' => 'Description',
        'event_date_time' => 'Date & Time',
        'event_status' => 'Status',
        'event_location' => 'Location'
    ];

    foreach ($required as $field => $name) {
        if (empty($_POST[$field])) {
            $formErrors[$field] = "$name is required";
        }
    }

    // Custom validations
    if (!empty($_POST['event_id']) && !preg_match('/^EVT\d{3}$/', $_POST['event_id'])) {
        $formErrors['event_id'] = "Invalid format. Use EVT followed by 3 digits (e.g., EVT001)";
    }

    if (!empty($_POST['event_date_time']) && strtotime($_POST['event_date_time']) < time()) {
        $formErrors['event_date_time'] = "Event date cannot be in the past";
    }

    if (!empty($_POST['event_location']) && !preg_match('/^-?\d+\.\d+\*[NS], -?\d+\.\d+\*[EW]$/', $_POST['event_location'])) {
        $formErrors['event_location'] = "Invalid coordinates format. Click the map to set location";
    }

    // File validation
    $approvalLetterPath = null;
    if (isset($_FILES['approval_letter'])) {
        $allowedTypes = ['application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($_FILES['approval_letter']['error'] !== UPLOAD_ERR_OK) {
            $formErrors['approval_letter'] = "File upload required";
        } elseif (!in_array($_FILES['approval_letter']['type'], $allowedTypes)) {
            $formErrors['approval_letter'] = "Only PDF files are allowed";
        } elseif ($_FILES['approval_letter']['size'] > $maxSize) {
            $formErrors['approval_letter'] = "File size exceeds 5MB limit";
        }
    }

    // Proceed if no errors
    if (empty($formErrors)) {
        // File upload
        $uploadDir = __DIR__ . '/../../uploads/approval_letters/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $filename = $_POST['event_id'] . '_' . basename($_FILES['approval_letter']['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['approval_letter']['tmp_name'], $targetPath)) {
            $approvalLetterPath = '/uploads/approval_letters/' . $filename;
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
                $successMessage = "Event created successfully!";
                // Clear preserved values
                $preservedValues = [];
            }
        } catch (Exception $e) {
            $formErrors['database'] = "Error creating event: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/styles/events.css">
    <style>
        .fade-out {
            animation: fadeOut 3s forwards;
            animation-delay: 2s;
        }
        @keyframes fadeOut {
            from {opacity: 1;}
            to {opacity: 0; display: none;}
        }
    </style>
</head>
<body>
    <div class="event-form-container">
        <div class="event-form-card">
            <div class="event-form-header text-white">
                <h3 class="event-form-title">Event Creation Form</h3>
                <p class="mb-0 text-white-80">Register by Event Advisor</p>
            </div>
            
            <div class="card-body p-4">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success fade-out" role="alert">
                        <?= htmlspecialchars($successMessage) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($formErrors['database'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($formErrors['database']) ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" novalidate>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label form-required h6">Event ID</label>
                                <input type="text" name="event_id" 
                                    class="form-control event-form-control <?= isset($formErrors['event_id']) ? 'is-invalid' : '' ?>" 
                                    placeholder="EVT001"
                                    pattern="EVT\d{3}"
                                    value="<?= htmlspecialchars($preservedValues['event_id'] ?? '') ?>"
                                    required>
                                <?php if (isset($formErrors['event_id'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_id'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Format: EVT followed by 3 digits (e.g., EVT001)</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required h6">Event Name</label>
                                <input type="text" name="event_name" 
                                    class="form-control event-form-control <?= isset($formErrors['event_name']) ? 'is-invalid' : '' ?>" 
                                    value="<?= htmlspecialchars($preservedValues['event_name'] ?? '') ?>"
                                    required>
                                <?php if (isset($formErrors['event_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required h6">Event Status</label>
                                <select name="event_status" 
                                    class="form-select event-form-control <?= isset($formErrors['event_status']) ? 'is-invalid' : '' ?>" 
                                    required>
                                    <option value="Active" <?= ($preservedValues['event_status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Postponed" <?= ($preservedValues['event_status'] ?? '') === 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                                    <option value="Cancelled" <?= ($preservedValues['event_status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <?php if (isset($formErrors['event_status'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_status'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label form-required h6">Date & Time</label>
                                <input type="datetime-local" name="event_date_time" 
                                    class="form-control event-form-control <?= isset($formErrors['event_date_time']) ? 'is-invalid' : '' ?>" 
                                    value="<?= htmlspecialchars($preservedValues['event_date_time'] ?? '') ?>"
                                    min="<?= date('Y-m-d\TH:i') ?>"
                                    required>
                                <?php if (isset($formErrors['event_date_time'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_date_time'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required h6">Approval Letter</label>
                                <input type="file" name="approval_letter" 
                                    class="form-control event-form-control <?= isset($formErrors['approval_letter']) ? 'is-invalid' : '' ?>" 
                                    accept=".pdf" 
                                    required>
                                <?php if (isset($formErrors['approval_letter'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['approval_letter'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">PDF only, max 5MB</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label form-required h6">Description</label>
                                <textarea name="event_description" 
                                    class="form-control event-form-control <?= isset($formErrors['event_description']) ? 'is-invalid' : '' ?>" 
                                    rows="4" 
                                    required><?= htmlspecialchars($preservedValues['event_description'] ?? '') ?></textarea>
                                <?php if (isset($formErrors['event_description'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_description'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-required h6">Location</label>
                                <div id="map" class="event-map"></div>
                                <input type="text" name="event_location" 
                                    class="form-control event-form-control mt-2 <?= isset($formErrors['event_location']) ? 'is-invalid' : '' ?>" 
                                    placeholder="Click map to set coordinates" 
                                    pattern="-?\d+\.\d+\*[NS], -?\d+\.\d+\*[EW]" 
                                    value="<?= htmlspecialchars($preservedValues['event_location'] ?? '') ?>"
                                    required>
                                <?php if (isset($formErrors['event_location'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $formErrors['event_location'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Click the map to set coordinates</small>
                            </div>
                        </div>

                        <div class="event-form-buttons d-flex justify-content-end mt-4">
                            <a href="view.php" class="btn btn-secondary px-4">
                                <i class="fas fa-times-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-calendar-plus me-2"></i>Create Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Map initialization
        const map = L.map('map').setView([3.1390, 101.6869], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        let marker;
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

        // Auto-hide success message
        document.addEventListener('DOMContentLoaded', () => {
            const successAlert = document.querySelector('.fade-out');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>