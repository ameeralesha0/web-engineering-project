<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if not logged in or not a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php'; // Include the PDO database connection file

$studentID = $_SESSION['user_id'];
$studentName = $_SESSION['student_name'] ?? 'Student'; // Assuming student_name is set in session

$totalMerit = 0;
$currentSemester = "N/A";
$eventsParticipated = 0;

try {
    // Fetch Total Merit
    $stmt_total_merit = $pdo->prepare("SELECT SUM(merit_points) AS total_points FROM awarded_merits WHERE student_id = :student_id");
    $stmt_total_merit->execute([':student_id' => $studentID]);
    $result_total_merit = $stmt_total_merit->fetch(PDO::FETCH_ASSOC);
    $totalMerit = $result_total_merit['total_points'] ?? 0;

    // Fetch Current Semester (assuming a 'students' table with 'current_semester' column)
    $stmt_semester = $pdo->prepare("SELECT current_semester FROM students WHERE student_id = :student_id");
    $stmt_semester->execute([':student_id' => $studentID]);
    $result_semester = $stmt_semester->fetch(PDO::FETCH_ASSOC);
    $currentSemester = $result_semester['current_semester'] ?? "N/A";

    // Fetch Events Participated (assuming 'awarded_merits' table contains event_id)
    $stmt_events = $pdo->prepare("SELECT COUNT(DISTINCT event_id) AS event_count FROM awarded_merits WHERE student_id = :student_id");
    $stmt_events->execute([':student_id' => $studentID]);
    $result_events = $stmt_events->fetch(PDO::FETCH_ASSOC);
    $eventsParticipated = $result_events['event_count'] ?? 0;

} catch (PDOException $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    echo "<p style='color: red; text-align: center;'>An error occurred while loading dashboard data. Please try again.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="stylepage.css">
    <style>
        /* Specific styles for student_dashboard.php */
        .dashboard-content {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .welcome-section {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .summary-item {
            background-color: #e0f2f1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .summary-item .label {
            font-weight: bold;
            color: #004d40;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .summary-item .value {
            font-size: 1.8em;
            color: #333;
            margin: 0;
            font-weight: bold;
        }
        .visualizations {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .chart-placeholder {
            flex: 1;
            min-width: 300px;
            border: 1px dashed #ccc;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .chart-placeholder img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .chart-placeholder button {
            background-color: #2196F3;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .chart-placeholder button:hover {
            background-color: #1976D2;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .action-buttons .export-btn {
            background-color: #007bff;
            color: white;
        }
        .action-buttons .export-btn:hover {
            background-color: #0056b3;
        }
        .action-buttons .back-btn {
            background-color: #6c757d;
            color: white;
        }
        .action-buttons .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/mypetakom_logo.png" alt="MyPetakom Logo" class="logo">
        <h1>Student Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="dashboard-content">
        <section class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h2>
        </section>

        <section class="summary-grid">
            <div class="summary-item">
                <p class="label">Total Merit</p>
                <p class="value"><?php echo htmlspecialchars($totalMerit); ?></p>
            </div>
            <div class="summary-item">
                <p class="label">Current Semester</p>
                <p class="value"><?php echo htmlspecialchars($currentSemester); ?></p>
            </div>
            <div class="summary-item">
                <p class="label">Events Participated</p>
                <p class="value"><?php echo htmlspecialchars($eventsParticipated); ?></p>
            </div>
        </section>

        <section class="visualizations">
            <div class="chart-placeholder">
                <img src="images/graph_placeholder.png" alt="Merit Graph Placeholder">
                <button onclick="alert('View Graph functionality to be implemented.')">View Graph</button>
            </div>
            <div class="chart-placeholder">
                <img src="images/pie_chart_placeholder.png" alt="Merit Distribution Pie Chart Placeholder">
                <button onclick="alert('View Pie Chart functionality to be implemented.')">View Pie Chart</button>
            </div>
        </section>

        <section class="action-buttons">
            <button class="export-btn" onclick="alert('Export PDF functionality to be implemented.')">Export PDF</button>
            <button class="back-btn" onclick="location.href='student_homepage.php'">Back</button>
        </section>
    </main>
</body>
</html>