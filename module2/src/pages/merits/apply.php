<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

$selected_event_id = $_GET['event_id'] ?? '';
$error = '';
$success = '';

// Handle merit application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $event_id = $conn->real_escape_string($_POST['event_id']);
    $committee_ids = $_POST['committee_ids'] ?? [];
    
    if (empty($committee_ids)) {
        $error = "Please select at least one committee member!";
    } else {
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($committee_ids as $committee_id) {
            $committee_id = $conn->real_escape_string($committee_id);
            
            // Check if application already exists
            $check_stmt = $conn->prepare("SELECT * FROM merit_application WHERE committee_id = ?");
            $check_stmt->bind_param("s", $committee_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows > 0) {
                $errorCount++;
            } else {
                // Generate application ID
                $max_id = $conn->query("SELECT MAX(CAST(SUBSTRING(merit_application_id, 6) AS UNSIGNED)) AS max_num 
                                      FROM merit_application")->fetch_assoc()['max_num'];
                $next_num = $max_id ? $max_id + 1 : 1;
                $merit_application_id = 'MERIT' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

                // Insert application
                $insert_stmt = $conn->prepare("INSERT INTO merit_application 
                                            (merit_application_id, application_status, submission_date, 
                                             event_id, committee_id)
                                            VALUES (?, 'Pending', CURDATE(), ?, ?)");
                $insert_stmt->bind_param("sss", $merit_application_id, $event_id, $committee_id);
                
                if ($insert_stmt->execute()) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        }
        
        if ($successCount > 0) {
            $success = "Successfully submitted $successCount merit application(s)!";
        }
        if ($errorCount > 0) {
            $error = "Failed to submit $errorCount application(s) (already submitted or error)";
        }
    }
}

// Fetch all events for dropdown
$events = $conn->query("SELECT * FROM events ORDER BY event_date_time DESC");

// Fetch committee members for selected event
$committee_members = [];
if ($selected_event_id) {
    $committee_members = $conn->query("SELECT c.*, s.student_name, s.student_id, 
                                      (SELECT application_status FROM merit_application m 
                                       WHERE m.committee_id = c.committee_id) AS app_status
                                    FROM event_committee c
                                    JOIN student s ON c.student_id = s.student_id
                                    WHERE c.event_id = '$selected_event_id'");
}

// Fetch all applications
$applications = $conn->query("SELECT ma.*, e.event_name, c.committee_position, s.student_name, st.staff_name AS advisor_name
                            FROM merit_application ma
                            JOIN events e ON ma.event_id = e.event_id
                            JOIN event_committee c ON ma.committee_id = c.committee_id
                            JOIN student s ON c.student_id = s.student_id
                            LEFT JOIN petakom_coordinator st ON ma.advisor_id = st.staff_id
                            ORDER BY ma.submission_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merit Application System</title>
    <link rel="stylesheet" href="../../../public/styles/committee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="committee-container">
        <div class="committee-header">
            <h1 class="committee-title">Merit Application System</h1>
        </div>

        <!-- Event Selection -->
        <div class="search-section">
            <form method="get" class="search-box">
                <div class="form-group">
                    <div class="select-wrapper">
                        <select class="form-control" name="event_id" required onchange="this.form.submit()">
                            <option value="">-- Select Event --</option>
                            <?php while ($event = $events->fetch_assoc()): ?>
                                <option value="<?= $event['event_id'] ?>" 
                                    <?= $event['event_id'] == $selected_event_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['event_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- Committee List for Selected Event -->
        <?php if ($selected_event_id): ?>
            <div class="form-section">
                <h2><i class="fas fa-users"></i> Committee Members for Event</h2>
                
                <?php if ($committee_members->num_rows === 0): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No committee members found for this event.
                    </div>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="event_id" value="<?= $selected_event_id ?>">
                        
                        <table class="committee-table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Student Name</th>
                                    <th>Position</th>
                                    <th>Merit Points</th>
                                    <th>Application Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($member = $committee_members->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (empty($member['app_status'])): ?>
                                            <input type="checkbox" name="committee_ids[]" 
                                                   value="<?= $member['committee_id'] ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($member['student_name']) ?></td>
                                    <td><?= $member['committee_position'] ?></td>
                                    <td><?= $member['merit_point'] ?></td>
                                    <td>
                                        <?php if ($member['app_status']): ?>
                                            <span class="status-badge 
                                                <?= $member['app_status'] === 'Approved' ? 'status-approved' : 
                                                   ($member['app_status'] === 'Rejected' ? 'status-rejected' : 'status-pending') ?>">
                                                <?= $member['app_status'] ?>
                                            </span>
                                        <?php else: ?>
                                            Not Applied
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="modi.php?id=<?= $member['committee_id'] ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="remove.php?id=<?= $member['committee_id'] ?>&event_id=<?= $selected_event_id ?>&source=apply" 
                                           class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" name="submit_application">
                                <i class="fas fa-paper-plane"></i> Apply for Selected Members
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- All Applications -->
        <div class="application-list">
            <h2><i class="fas fa-history"></i> All Applications</h2>
            
            <?php if ($applications->num_rows === 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No merit applications found.
                </div>
            <?php else: ?>
                <table class="application-table">
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Event</th>
                            <th>Committee Member</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applications->fetch_assoc()): ?>
                        <tr>
                            <td><?= $app['merit_application_id'] ?></td>
                            <td><?= htmlspecialchars($app['event_name']) ?></td>
                            <td><?= htmlspecialchars($app['student_name']) ?></td>
                            <td><?= $app['committee_position'] ?></td>
                            <td>
                                <span class="status-badge 
                                    <?= $app['application_status'] === 'Approved' ? 'status-approved' : 
                                       ($app['application_status'] === 'Rejected' ? 'status-rejected' : 'status-pending') ?>">
                                    <?= $app['application_status'] ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($app['submission_date'])) ?></td>
                            <td>
                                <?= $app['approval_date'] ? 
                                   date('d/m/Y', strtotime($app['approval_date'])) : 'N/A' ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>