<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No event specified";
    header('Location: view.php');
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/styles/events.css">
</head>
<body>
    <div class="container py-4">
        <div class="event-details-card card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2><?= htmlspecialchars($event['event_name']) ?></h2>
                <p class="mb-0">Event ID: <?= htmlspecialchars($event['event_id']) ?></p>
            </div>
            
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        <span class="badge <?= getStatusClass($event['event_status']) ?>">
                            <?= htmlspecialchars($event['event_status']) ?>
                        </span>
                    </dd>

                    <dt class="col-sm-3">Date & Time</dt>
                    <dd class="col-sm-9"><?= date('F j, Y H:i', strtotime($event['event_date_time'])) ?></dd>

                    <dt class="col-sm-3">Location</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($event['event_location']) ?></dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9"><?= nl2br(htmlspecialchars($event['event_description'])) ?></dd>

                    <dt class="col-sm-3">Approval Letter</dt>
                    <dd class="col-sm-9">
                        <?php if ($event['approval_letter']): ?>
                            <a href="<?= htmlspecialchars($event['approval_letter']) ?>" 
                               class="btn btn-sm btn-secondary"
                               target="_blank">
                                <i class="fas fa-file-pdf"></i> View Document
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No document uploaded</span>
                        <?php endif; ?>
                    </dd>
                </dl>

                <div class="mt-4">
                    <a href="view.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Active': return 'bg-success';
        case 'Postponed': return 'bg-warning text-dark';
        case 'Cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>