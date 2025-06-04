<?php
session_start();

// Redirect if not logged in or not a student
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters - update with your actual credentials
$host = 'localhost';
$dbname = 'mypetakom';    // Your database name
$username = 'webuser';       // Your DB username
$password = 'admin1234';           // Your DB password
$port = 4306;

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<p style='color: red; text-align: center;'>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}


$studentID = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

$totalMerit = 0;
$currentSemester = "N/A";
$eventsParticipated = 0;

try {
    // Fetch Total Merit
    $stmt_total_merit = $pdo->prepare("SELECT SUM(merit_points) AS total_points FROM awarded_merits WHERE student_id = :student_id");
    $stmt_total_merit->execute([':student_id' => $studentID]);
    $result_total_merit = $stmt_total_merit->fetch(PDO::FETCH_ASSOC);
    $totalMerit = $result_total_merit['total_points'] ?? 0;

    // Fetch Current Semester
    $stmt_semester = $pdo->prepare("SELECT current_semester FROM students WHERE student_id = :student_id");
    $stmt_semester->execute([':student_id' => $studentID]);
    $result_semester = $stmt_semester->fetch(PDO::FETCH_ASSOC);
    $currentSemester = $result_semester['current_semester'] ?? "N/A";

    // Fetch Events Participated
    $stmt_events = $pdo->prepare("SELECT COUNT(DISTINCT event_id) AS event_count FROM awarded_merits WHERE student_id = :student_id");
    $stmt_events->execute([':student_id' => $studentID]);
    $result_events = $stmt_events->fetch(PDO::FETCH_ASSOC);
    $eventsParticipated = $result_events['event_count'] ?? 0;

} catch (PDOException $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    echo "<p style='color: red; text-align: center;'>An error occurred while loading dashboard data. Please try again.</p>";
}
// Prepare data for charts
$chartLabels = [];
$chartMerits = [];

try {
    $stmt_chart = $pdo->prepare("SELECT event_name, SUM(merit_points) as total 
        FROM awarded_merits 
        INNER JOIN events ON awarded_merits.event_id = events.event_id 
        WHERE student_id = :student_id 
        GROUP BY awarded_merits.event_id");
    $stmt_chart->execute([':student_id' => $studentID]);

    while ($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)) {
        $chartLabels[] = $row['event_name'];
        $chartMerits[] = (int)$row['total'];
    }
} catch (PDOException $e) {
    error_log("Chart data error: " . $e->getMessage());
}
?>

<!-- Output chart data to JavaScript -->
<script>
    const chartLabels = <?php echo json_encode($chartLabels); ?>;
    const chartMerits = <?php echo json_encode($chartMerits); ?>;
</script>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="studentdashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <header>
        <img src="petakom.jpg" alt="MyPetakom Logo" class="logo">
        <h1>Student Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main id="dashboard" class="dashboard-content">

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
        <canvas id="meritLineChart"></canvas>
    </div>
    <div class="chart-placeholder">
        <canvas id="meritPieChart"></canvas>
    </div>
</section>


        <section class="action-buttons">
            <button class="export-btn" onclick="alert('Export PDF.')">Export PDF</button>
            <button class="back-btn" onclick="location.href='studentpage.php'">Back</button>
        </section>
    </main>
</body>
<script>
  window.onload = () => {
    const { jsPDF } = window.jspdf;

    document.querySelector('.export-btn').addEventListener('click', () => {
      const doc = new jsPDF();

      let yPosition = 10;
      doc.setFontSize(16);
      doc.text("Student Dashboard - MyPetakom", 10, yPosition);
      yPosition += 15;

      const dashboard = document.getElementById('dashboard');

      const welcomeText = dashboard.querySelector('.welcome-section h2').innerText;
      doc.setFontSize(14);
      doc.text(welcomeText, 10, yPosition);
      yPosition += 15;

      const summaryItems = dashboard.querySelectorAll('.summary-item');
      summaryItems.forEach(item => {
        const label = item.querySelector('.label').innerText;
        const value = item.querySelector('.value').innerText;
        doc.text(`${label}: ${value}`, 10, yPosition);
        yPosition += 10;
      });

      yPosition += 10;

      doc.setFontSize(12);
      doc.text("Graphs and charts are not included in this PDF export.", 10, yPosition);

      doc.save('StudentDashboard.pdf');
    });
  };
  
</script>
<!-- Chart.js rendering script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Line Chart
    const ctxLine = document.getElementById('meritLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Merit Points by Event',
                data: chartMerits,
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Merit Points by Event'
                }
            }
        }
    });

    // Pie Chart
    const ctxPie = document.getElementById('meritPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartMerits,
                backgroundColor: chartLabels.map((_, i) =>
                    `hsl(${(i * 60) % 360}, 70%, 70%)`
                )
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Merit Distribution by Event'
                }
            }
        }
    });
});
</script>

</html>