<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

$committee_id = $_GET['id'] ?? null;
$error = '';
$success = '';
$positions = []; // Will be populated from your configuration

// Fetch committee details
$committee = [];
if ($committee_id) {
    $stmt = $conn->prepare("SELECT c.*, s.student_name, e.event_name 
                          FROM event_committee c
                          JOIN student s ON c.student_id = s.student_id
                          JOIN events e ON c.event_id = e.event_id
                          WHERE c.committee_id = ?");
    $stmt->bind_param("s", $committee_id);
    $stmt->execute();
    $committee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$committee) {
    $_SESSION['error'] = "Committee member not found!";
    header("Location: assign.php");
    exit;
}

// Get position configuration (should match your assign.php config)
$positions = [
    'Chairperson' => ['limit' => 1, 'merit' => 50],
    'Secretary' => ['limit' => 1, 'merit' => 40],
    'Treasurer' => ['limit' => 1, 'merit' => 40],
    'Head of Department' => ['limit' => 5, 'merit' => 35],
    'General Member' => ['limit' => 10, 'merit' => 30]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_position = $_POST['position'];
    $merit_point = $positions[$new_position]['merit'];

    // Check position availability
    $countStmt = $conn->prepare("SELECT COUNT(*) AS count FROM event_committee 
                               WHERE event_id = ? AND committee_position = ?
                               AND committee_id != ?");
    $countStmt->bind_param("sss", $committee['event_id'], $new_position, $committee_id);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();

    if ($countResult['count'] >= $positions[$new_position]['limit']) {
        $error = "Position limit reached for $new_position! Maximum allowed: {$positions[$new_position]['limit']}";
    } else {
        // Update position
        $updateStmt = $conn->prepare("UPDATE event_committee 
                                     SET committee_position = ?, merit_point = ?
                                     WHERE committee_id = ?");
        $updateStmt->bind_param("sis", $new_position, $merit_point, $committee_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['success'] = "Committee member updated successfully!";
            header("Location: assign.php?event_id={$committee['event_id']}");
            exit;
        } else {
            $error = "Error updating record: " . $conn->error;
        }
        $updateStmt->close();
    }
    $countStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Committee Member</title>
    <link rel="stylesheet" href="../../../public/styles/committee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function updateMeritPoints() {
            const positions = <?= json_encode(array_column($positions, 'merit')) ?>;
            const positionSelect = document.getElementById('position');
            document.getElementById('merit_point').value = positions[positionSelect.selectedIndex];
        }
    </script>
</head>
<body>
    <div class="committee-container">
        <div class="committee-header">
            <h1 class="committee-title">Modify Committee Member</h1>
        </div>

        <div class="form-section">
            <form method="post" class="form-grid">
                <div class="form-group">
                    <label class="form-label">Student Name</label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars($committee['student_name']) ?>" 
                           readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Event Name</label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars($committee['event_name']) ?>" 
                           readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Committee Position</label>
                    <div class="select-wrapper">
                        <select class="form-control" name="position" id="position" 
                                onchange="updateMeritPoints()" required>
                            <?php foreach ($positions as $pos => $details): 
                                $countStmt = $conn->prepare("SELECT COUNT(*) FROM event_committee 
                                                           WHERE event_id = ? AND committee_position = ?
                                                           AND committee_id != ?");
                                $countStmt->bind_param("sss", $committee['event_id'], $pos, $committee_id);
                                $countStmt->execute();
                                $count = $countStmt->get_result()->fetch_row()[0];
                                $disabled = $count >= $details['limit'] ? 'disabled' : '';
                                $selected = $pos === $committee['committee_position'] ? 'selected' : '';
                            ?>
                            <option value="<?= $pos ?>" <?= $disabled ?> <?= $selected ?>>
                                <?= $pos ?> (<?= $count ?>/<?= $details['limit'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Merit Points</label>
                    <input type="number" class="form-control" 
                           id="merit_point" 
                           value="<?= $committee['merit_point'] ?>" 
                           readonly>
                </div>

                <div class="btn-group">
                    <a href="assign.php?event_id=<?= $committee['event_id'] ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>