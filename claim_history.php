<?php
session_start();

// Redirect if not logged in or not a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php'; // Include the PDO database connection file

$studentID = $_SESSION['user_id'];
$claimHistory = [];

try {
    $stmt = $pdo->prepare("SELECT claim_date, student_id, event_name, event_level, role_in_event, status FROM merit_claims WHERE student_id = :student_id ORDER BY claim_date DESC");
    $stmt->execute([':student_id' => $studentID]);
    $claimHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching claim history: " . $e->getMessage());
    echo "<p style='color: red; text-align: center;'>An error occurred while loading claim history. Please try again.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim History - MyPetakom</title>
    <link rel="stylesheet" href="stylepage.css">
    <style>
        /* Specific styles for claim_history.php */
        .claim-history-container {
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .claim-history-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .claim-history-table th, .claim-history-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .claim-history-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .claim-history-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .claim-history-table tbody tr:hover {
            background-color: #e9e9e9;
        }
        .claim-history-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .claim-history-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            background-color: #6c757d; /* Gray */
            color: white;
        }
        .claim-history-actions button:hover {
            background-color: #5a6268;
        }
        /* Status styling */
        .status-Pending { color: #ffc107; font-weight: bold; } /* Warning yellow */
        .status-Approved { color: #28a745; font-weight: bold; } /* Success green */
        .status-Rejected { color: #dc3545; font-weight: bold; } /* Danger red */
    </style>
</head>
<body>
    <header>
        <img src="images/mypetakom_logo.png" alt="MyPetakom Logo" class="logo">
        <h1>Claim History</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="claim-history-container">
        <table class="claim-history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student ID</th>
                    <th>Event Name</th>
                    <th>Event Level</th>
                    <th>Role in Event</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($claimHistory) > 0): ?>
                    <?php foreach ($claimHistory as $claim): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($claim['claim_date']); ?></td>
                            <td><?php echo htmlspecialchars($claim['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($claim['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($claim['event_level']); ?></td>
                            <td><?php echo htmlspecialchars($claim['role_in_event']); ?></td>
                            <td class="status-<?php echo htmlspecialchars($claim['status']); ?>"><?php echo htmlspecialchars($claim['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No claim history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <section class="claim-history-actions">
            <button class="back-btn" onclick="location.href='student_homepage.php'">Back</button>
        </section>
    </main>
</body>
</html>