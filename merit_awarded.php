<?php
session_start();

// Redirect if not logged in or not a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php'; // Include the PDO database connection file

$studentID = $_SESSION['user_id'];
$meritList = [];
$totalMeritAwarded = 0;
$selectedSemester = $_GET['semester'] ?? 'All'; // Get selected semester from query string

try {
    $sql = "SELECT am.event_name, am.role_in_event, am.event_level, am.event_date, am.merit_points
            FROM awarded_merits am
            WHERE am.student_id = :student_id";

    // Add semester filter if a specific semester is selected
    if ($selectedSemester !== 'All') {
        // You'll need a way to link events to semesters. This is an example assuming an 'events' table with 'semester' column.
        // Or if awarded_merits has a semester column directly.
        // For now, I'll assume awarded_merits might have a semester column, or you join with an events table.
        // Let's assume there's a 'semester' column in 'awarded_merits' for simplicity.
        $sql .= " AND am.semester = :semester";
    }

    $stmt = $pdo->prepare($sql . " ORDER BY am.event_date DESC");
    $stmt->bindValue(':student_id', $studentID);

    if ($selectedSemester !== 'All') {
        $stmt->bindValue(':semester', $selectedSemester);
    }
    $stmt->execute();
    $meritList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total merit
    $stmt_total_merit = $pdo->prepare("SELECT SUM(merit_points) AS total_points FROM awarded_merits WHERE student_id = :student_id");
    $stmt_total_merit->execute([':student_id' => $studentID]);
    $totalMeritAwarded = $stmt_total_merit->fetch(PDO::FETCH_ASSOC)['total_points'] ?? 0;

    // Fetch available semesters for the dropdown (assuming from a relevant table or distinct from awarded_merits)
    $stmt_semesters = $pdo->prepare("SELECT DISTINCT semester FROM awarded_merits WHERE student_id = :student_id ORDER BY semester DESC");
    $stmt_semesters->execute([':student_id' => $studentID]);
    $availableSemesters = $stmt_semesters->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Error fetching awarded merits: " . $e->getMessage());
    echo "<p style='color: red; text-align: center;'>An error occurred while loading merit data. Please try again.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merit Awarded - MyPetakom</title>
    <link rel="stylesheet" href="stylepage.css">
    <style>
        /* Specific styles for merit_awarded.php */
        .merit-awarded-container {
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .filter-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .filter-section label {
            font-weight: bold;
            color: #333;
        }
        .filter-section select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            flex-grow: 1; /* Allow dropdown to take space */
            max-width: 200px; /* Limit dropdown width */
        }
        .merit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .merit-table th, .merit-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .merit-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .merit-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .merit-table tbody tr:hover {
            background-color: #e9e9e9;
        }
        .total-merit {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .merit-awarded-actions {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .merit-awarded-actions button,
        .merit-awarded-actions a {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            text-decoration: none; /* For links styled as buttons */
            color: white; /* For links styled as buttons */
            display: inline-flex; /* For links to center content if needed */
            align-items: center;
            justify-content: center;
            min-width: 120px;
        }
        .merit-awarded-actions .download-btn {
            background-color: #17a2b8; /* Info blue */
        }
        .merit-awarded-actions .download-btn:hover {
            background-color: #138496;
        }
        .merit-awarded-actions .qr-code-btn {
            background-color: #6f42c1; /* Purple */
        }
        .merit-awarded-actions .qr-code-btn:hover {
            background-color: #563d7c;
        }
        .merit-awarded-actions .back-btn {
            background-color: #6c757d;
        }
        .merit-awarded-actions .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/mypetakom_logo.png" alt="MyPetakom Logo" class="logo">
        <h1>Merit Awarded</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="merit-awarded-container">
        <section class="filter-section">
            <p>My merit list (semester: </p>
            <select id="semester_dropdown" onchange="filterMerits()">
                <option value="All" <?php echo ($selectedSemester === 'All') ? 'selected' : ''; ?>>All</option>
                <?php foreach ($availableSemesters as $semester): ?>
                    <option value="<?php echo htmlspecialchars($semester); ?>" <?php echo ($selectedSemester === $semester) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($semester); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p>)</p>
        </section>

        <table class="merit-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Role</th>
                    <th>Level</th>
                    <th>Date</th>
                    <th>Merit Points</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($meritList) > 0): ?>
                    <?php foreach ($meritList as $merit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($merit['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($merit['role_in_event']); ?></td>
                            <td><?php echo htmlspecialchars($merit['event_level']); ?></td>
                            <td><?php echo htmlspecialchars($merit['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($merit['merit_points']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No merits awarded yet or for the selected semester.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-merit">
            Total Merit: <?php echo htmlspecialchars($totalMeritAwarded); ?>
        </div>

        <section class="merit-awarded-actions">
            <button class="download-btn" onclick="alert('Download PDF functionality to be implemented.')">Download PDF</button>
            <button class="qr-code-btn" onclick="alert('QR Code generation functionality to be implemented.')">QR Code</button>
            <button class="back-btn" onclick="location.href='student_homepage.php'">Back</button>
        </section>
    </main>

    <script>
        function filterMerits() {
            const semester = document.getElementById('semester_dropdown').value;
            window.location.href = 'merit_awarded.php?semester=' + semester;
        }
    </script>
</body>
</html>