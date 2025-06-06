<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

$event_id = '';
$event_name = '';
$error = '';
$success = '';
$existing_member = null;

// Position configuration with limits, merit points, and descriptions
$positions = [
    'Chairperson' => [
        'limit' => 1,
        'merit' => 50,
        'description' => 'Overall event leadership and coordination'
    ],
    'Secretary' => [
        'limit' => 1,
        'merit' => 40,
        'description' => 'Documentation and communication management'
    ],
    'Treasurer' => [
        'limit' => 1,
        'merit' => 40,
        'description' => 'Financial management and budgeting'
    ],
    'Head of Department' => [
        'limit' => 5,
        'merit' => 35,
        'description' => 'Departmental leadership and task delegation'
    ],
    'General Member' => [
        'limit' => 10,
        'merit' => 30,
        'description' => 'General event support and task execution'
    ]
];

// Fetch all events for dropdown
$events = $conn->query("SELECT event_id, event_name FROM events ORDER BY event_date_time DESC");

// Handle event selection
if (isset($_GET['select_event'])) {
    $event_id = $_GET['event_id'];
    
    $stmt = $conn->prepare("SELECT event_id, event_name FROM events WHERE event_id = ?");
    $stmt->bind_param("s", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $event_name = $event['event_name'];
    } else {
        $error = "Event not found! Please select a valid event";
    }
    $stmt->close();
}

// Handle committee submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_committee'])) {
    $event_id = $_POST['event_id'];
    $student_id = $_POST['student_id'];
    $position = $_POST['position'];
    $merit_point = $positions[$position]['merit'];

    // Check position availability
    $countStmt = $conn->prepare("SELECT COUNT(*) AS count FROM event_committee 
                               WHERE event_id = ? AND committee_position = ?");
    $countStmt->bind_param("ss", $event_id, $position);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    
    if ($countResult['count'] >= $positions[$position]['limit']) {
        $error = "Position limit reached for $position! Maximum allowed: {$positions[$position]['limit']}";
        $countStmt->close();
        goto render_page;
    }
    $countStmt->close();

    // Check existing assignment
    $checkStmt = $conn->prepare("SELECT * FROM event_committee 
                               WHERE student_id = ? AND event_id = ?");
    $checkStmt->bind_param("ss", $student_id, $event_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $existing_member = $checkResult->fetch_assoc();
        $error = "This student is already assigned to this event!";
        $checkStmt->close();
        goto render_page;
    }
    
    // Generate sequential committee ID
    $maxQuery = $conn->query("SELECT MAX(CAST(SUBSTRING(committee_id, 4) AS UNSIGNED)) AS max_num 
                             FROM event_committee WHERE committee_id LIKE 'COM%'");
    $next_num = $maxQuery->fetch_assoc()['max_num'] + 1 ?? 1;
    $committee_id = 'COM' . str_pad($next_num, 3, '0', STR_PAD_LEFT);

    // Insert committee member
    $stmt = $conn->prepare("INSERT INTO event_committee 
                          (committee_id, committee_position, merit_point, event_id, student_id) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $committee_id, $position, $merit_point, $event_id, $student_id);
    
    if ($stmt->execute()) {
        $success = "Committee member added successfully! ID: $committee_id";
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

render_page:
$students = $conn->query("SELECT student_id, student_name FROM student ORDER BY student_name");
$committee_members = $conn->query("SELECT c.*, s.student_name 
                                  FROM event_committee c
                                  JOIN student s ON c.student_id = s.student_id
                                  WHERE c.event_id = '$event_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Committee Management</title>
    <link rel="stylesheet" href="../../../public/styles/committee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function updateMeritPoints() {
            const positionSelect = document.getElementById('position');
            const meritPointInput = document.getElementById('merit_point');
            const positions = <?= json_encode($positions) ?>;
            meritPointInput.value = positions[positionSelect.value].merit;
        }
    </script>
</head>
<body>
    <div class="committee-container">
        <div class="committee-header">
            <h1 class="committee-title">Committee Management System</h1>
        </div>

        <!-- Position  -->
        <div class="reminder-box">
            <h3><i class="fas fa-info-circle"></i> Position Guidelines</h3>
            <table>
                <tr>
                    <th>Position</th>
                    <th>Limit</th>
                    <th>Merit Points</th>
                    <th>Job Scope</th>
                </tr>
                <?php foreach ($positions as $position => $details): ?>
                <tr>
                    <td><?= $position ?></td>
                    <td><?= $details['limit'] ?></td>
                    <td><?= $details['merit'] ?></td>
                    <td><?= $details['description'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="search-section">
            <form method="get" class="search-box">
                <div class="form-group">
                    <label class="form-label">Select Event</label>
                    <div class="select-wrapper">
                        <select class="form-control" name="event_id" required>
                            <option value="">-- Select Event --</option>
                            <?php while ($event = $events->fetch_assoc()): ?>
                                <option value="<?= $event['event_id'] ?>" <?= $event['event_id'] == $event_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['event_name']) ?> (<?= $event['event_id'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary" name="select_event">
                    <i class="fas fa-search"></i> Select Event
                </button>
            </form>

            <?php if (!empty($event_name)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-calendar-check"></i>
                    Managing committees for: <strong><?= htmlspecialchars($event_name) ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($event_name)): ?>
        <div class="form-section">
            <?php if ($existing_member): ?>
            <div class="existing-member">
                <h4>Existing Assignment</h4>
                <p>
                    <?= $existing_member['student_name'] ?> is currently assigned as 
                    <?= $existing_member['committee_position'] ?> (Merit Points: <?= $existing_member['merit_point'] ?>)
                </p>
                <div class="action-buttons">
                    <a href="modi.php?id=<?= $existing_member['committee_id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modify
                    </a>
                    <a href="remove.php?id=<?= $existing_member['committee_id'] ?>" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Remove
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <div class="form-group">
                    <label class="form-label">Select Student</label>
                    <div class="select-wrapper">
                        <select class="form-control" name="student_id" required>
                            <?php while ($row = $students->fetch_assoc()): ?>
                                <option value="<?= $row['student_id'] ?>">
                                    <?= htmlspecialchars($row['student_name']) ?> (<?= $row['student_id'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Committee Position</label>
                    <div class="select-wrapper">
                        <select class="form-control" name="position" id="position" onchange="updateMeritPoints()" required>
                            <?php foreach ($positions as $position => $details): 
                                // Check position availability
                                $count = $conn->query("SELECT COUNT(*) FROM event_committee 
                                                     WHERE event_id = '$event_id' 
                                                     AND committee_position = '$position'")->fetch_row()[0];
                                $disabled = $count >= $details['limit'] ? 'disabled' : '';
                            ?>
                            <option value="<?= $position ?>" <?= $disabled ?>>
                                <?= $position ?> (<?= $count ?>/<?= $details['limit'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Merit Points</label>
                    <input type="number" 
                           class="form-control" 
                           id="merit_point" 
                           name="merit_point" 
                           value="<?= reset($positions)['merit'] ?>" 
                           readonly
                           required>
                </div>

                <div class="btn-group">
                    <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
                    <button type="submit" class="btn btn-primary" name="add_committee">
                        <i class="fas fa-user-plus"></i> Add Committee Member
                    </button>
                    <a href="view.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                </div>
            </form>

            <!-- Current Committee List -->
            <div class="committee-list">
                <h3>Current Committee Members</h3>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Merit Points</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($member = $committee_members->fetch_assoc()): ?>
                    <tr>
                        <td><?= $member['student_name'] ?></td>
                        <td><?= $member['committee_position'] ?></td>
                        <td><?= $member['merit_point'] ?></td>
                        <td>
                            <a href="modi.php?id=<?= $member['committee_id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="remove.php?id=<?= $member['committee_id'] ?>" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>