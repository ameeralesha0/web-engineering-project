<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

// Handle filters and search
$search = $_GET['search'] ?? '';
$filter_status = $_GET['status'] ?? '';
$sort_key = $_GET['sort'] ?? 'date_desc';
$events = [];

// Valid sorting options
$valid_sorts = [
    'date_asc' => 'event_date_time ASC',
    'date_desc' => 'event_date_time DESC',
    'name_asc' => 'event_name ASC',
    'name_desc' => 'event_name DESC'
];
$sort = $valid_sorts[$sort_key] ?? 'event_date_time DESC';

// Base query
$query = "SELECT * FROM events WHERE 1=1";
$params = [];
$types = '';

// Apply filters
if (!empty($search)) {
    $query .= " AND (event_id LIKE ? OR event_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

if (!empty($filter_status)) {
    $query .= " AND event_status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

// Add sorting
$query .= " ORDER BY $sort";

// Prepare and execute
try {
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching events: " . $e->getMessage();
}

function getStatusClass($status) {
    switch ($status) {
        case 'Active': return 'bg-success';
        case 'Postponed': return 'bg-warning text-dark';
        case 'Cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/styles/events.css">
</head>
<body>
    <div class="container py-4" style="max-width: 1000px;">
        <div class="event-header mb-4">
            <h1 class="text-center mb-3">Event Management</h1>
            
            <!-- Search and Filter Section -->
            <div class="event-filters card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by ID or Name" 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Active" <?= $filter_status === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Postponed" <?= $filter_status === 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                                <option value="Cancelled" <?= $filter_status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <select name="sort" class="form-select">
                                <option value="date_desc" <?= $sort_key === 'date_desc' ? 'selected' : '' ?>>Newest First</option>
                                <option value="date_asc" <?= $sort_key === 'date_asc' ? 'selected' : '' ?>>Oldest First</option>
                                <option value="name_asc" <?= $sort_key === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                                <option value="name_desc" <?= $sort_key === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Apply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-auto-close"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($events)): ?>
            <div class="alert alert-info">No events found matching your criteria</div>
        <?php else: ?>
            <div class="event-list table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['event_id']) ?></td>
                                <td><?= htmlspecialchars($event['event_name']) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($event['event_date_time'])) ?></td>
                                <td>
                                    <span class="status-badge badge <?= getStatusClass($event['event_status']) ?>">
                                        <?= $event['event_status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- View Button -->
                                        <a href="detail.php?id=<?= $event['event_id'] ?>" 
                                           class="btn btn-sm btn-info" 
                                           data-bs-toggle="tooltip" 
                                           title="View full event details">
                                            <i class="fas fa-eye"></i> View
                                        </a>

                                        <!-- Edit Button (Only for responsible advisor) -->  
                                        <a href="edit.php?id=<?= $event['event_id'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Modify event details">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?= $event['event_id'] ?>"
                                                title="Permanently remove event">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal<?= $event['event_id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to permanently delete this event?</p>
                                                    <div class="alert alert-warning small">
                                                        <strong>Warning:</strong> This action cannot be undone. 
                                                        All related data will be permanently removed from the system.
                                                    </div>
                                                    <dl class="row">
                                                        <dt class="col-sm-3">Event ID</dt>
                                                        <dd class="col-sm-9"><?= $event['event_id'] ?></dd>
                                                        
                                                        <dt class="col-sm-3">Event Name</dt>
                                                        <dd class="col-sm-9"><?= $event['event_name'] ?></dd>
                                                    </dl>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="delete.php?id=<?= $event['event_id'] ?>" class="btn btn-danger">
                                                        Confirm Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable Bootstrap tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Auto-close alerts
        document.addEventListener('DOMContentLoaded', () => {
            const autoCloseAlerts = document.querySelectorAll('.alert-auto-close');
            autoCloseAlerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            });
        });
    </script>
</body>
</html>