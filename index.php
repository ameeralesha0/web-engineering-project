<?php
require_once __DIR__ . '/includes/functions.php';

$attendance = getAllAttendance();
$message = '';

if (isset($_GET['delete'])) {
    $message = deleteAttendance($_GET['delete']) 
        ? '<div class="alert alert-success">Attendance record deleted successfully!</div>'
        : '<div class="alert alert-danger">Error deleting attendance record.</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance Slots</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clipboard-check"></i>Event Attendance Slots</h1>
        </div>

        <?php echo $message; ?>

        <div class="actions">
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Attendance
            </a>
            <a href="qr_generator.php" class="btn btn-success">
                <i class="fas fa-qrcode"></i> Generate QR Code
            </a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Check-in</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance as $record): ?>
                    <tr>
                        <td><?= $record['id'] ?></td>
                        <td><?= htmlspecialchars($record['student_name']) ?> (<?= $record['student_id'] ?>)</td>
                        <td><?= htmlspecialchars($record['event_name']) ?></td>
                        <td><?= date('M j, Y', strtotime($record['event_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($record['check_in'])) ?></td>
                        <td class="status-<?= strtolower($record['status']) ?>">
                            <?= $record['status'] ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $record['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="index.php?delete=<?= $record['id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>