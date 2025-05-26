<?php
require_once __DIR__ . '/includes/functions.php';

$events = getAllEvents();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'event_id' => $_POST['event_id'],
        'student_id' => $_POST['student_id'],
        'student_name' => $_POST['student_name'],
        'check_in' => $_POST['check_in_date'] . ' ' . $_POST['check_in_time'],
        'status' => $_POST['status']
    ];

    if (createAttendance($data)) {
        $message = '<div class="alert alert-success">Attendance record created successfully!</div>';
        // Clear form
        $_POST = [];
    } else {
        $message = '<div class="alert alert-danger">Error creating attendance record.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance Record</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Add Attendance Record</h1>
        </div>

        <a href="index.php" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>

        <?php echo $message; ?>

        <form method="POST">
            <div class="form-group">
                <label for="event_id">Event</label>
                <select name="event_id" id="event_id" class="form-control" required>
                    <option value="">Select Event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['event_id'] ?>" <?= isset($_POST['event_id']) && $_POST['event_id'] == $event['event_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['event_name']) ?> (<?= date('M j, Y', strtotime($event['event_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" name="student_id" id="student_id" class="form-control" 
                       value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" name="student_name" id="student_name" class="form-control" 
                       value="<?= htmlspecialchars($_POST['student_name'] ?? '') ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="check_in_date">Check-in Date</label>
                    <input type="date" name="check_in_date" id="check_in_date" class="form-control" 
                           value="<?= htmlspecialchars($_POST['check_in_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="check_in_time">Check-in Time</label>
                    <input type="time" name="check_in_time" id="check_in_time" class="form-control" 
                           value="<?= htmlspecialchars($_POST['check_in_time'] ?? date('H:i')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Present" <?= isset($_POST['status']) && $_POST['status'] == 'Present' ? 'selected' : '' ?>>Present</option>
                    <option value="Late" <?= isset($_POST['status']) && $_POST['status'] == 'Late' ? 'selected' : '' ?>>Late</option>
                    <option value="Absent" <?= isset($_POST['status']) && $_POST['status'] == 'Absent' ? 'selected' : '' ?>>Absent</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Record
            </button>
        </form>
    </div>
</body>
</html>