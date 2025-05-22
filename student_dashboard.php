<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/mypetakom_logo.png" alt="Mypetakom Logo">
        </div>
        <h1>Student Dashboard</h1>
        <button class="logout-button">Logout</button>
    </header>

    <main>
        <section class="welcome-section">
            <h2>Welcome, <?php
                session_start(); // Start the session (if not already started)
                // Assuming you store the student's name in a session variable
                if (isset($_SESSION['student_name'])) {
                    echo htmlspecialchars($_SESSION['student_name']);
                } else {
                    echo "Student"; // Default if name isn't in session
                }
            ?>!</h2>
        </section>

        <section class="summary-section">
            <div class="summary-item">
                <p class="label">Total Merit:</p>
                <p class="value"><?php
                    // Include database connection (replace with your actual details)
                    $servername = "localhost";
                    $username = "your_db_username";
                    $password = "your_db_password";
                    $dbname = "your_db_name";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $studentID = "[USER_ID]"; // Replace with actual student ID from session
                    $sql_total_merit = "SELECT SUM(merit_points) AS total_points FROM awarded_merits WHERE student_id = ?";
                    $stmt_total_merit = $conn->prepare($sql_total_merit);
                    $stmt_total_merit->bind_param("s", $studentID);
                    $stmt_total_merit->execute();
                    $result_total_merit = $stmt_total_merit->get_result();

                    if ($row_total_merit = $result_total_merit->fetch_assoc()) {
                        echo htmlspecialchars($row_total_merit["total_points"] ?: 0);
                    } else {
                        echo "0";
                    }
                    $stmt_total_merit->close();
                    ?></p>
            </div>
            <div class="summary-item">
                <p class="label">Current Semester:</p>
                <p class="value"><?php
                    // Fetch current semester from database or session
                    $sql_semester = "SELECT current_semester FROM students WHERE student_id = ?";
                    $stmt_semester = $conn->prepare($sql_semester);
                    $stmt_semester->bind_param("s", $studentID);
                    $stmt_semester->execute();
                    $result_semester = $stmt_semester->get_result();

                    if ($row_semester = $result_semester->fetch_assoc()) {
                        echo htmlspecialchars($row_semester["current_semester"] ?: "N/A");
                    } else {
                        echo "N/A";
                    }
                    $stmt_semester->close();
                    ?></p>
            </div>
            <div class="summary-item">
                <p class="label">Events Participated:</p>
                <p class="value"><?php
                    // Fetch count of events participated from database
                    $sql_events = "SELECT COUNT(DISTINCT event_id) AS event_count FROM participation_records WHERE student_id = ?";
                    $stmt_events = $conn->prepare($sql_events);
                    $stmt_events->bind_param("s", $studentID);
                    $stmt_events->execute();
                    $result_events = $stmt_events->get_result();

                    if ($row_events = $result_events->fetch_assoc()) {
                        echo htmlspecialchars($row_events["event_count"] ?: 0);
                    } else {
                        echo "0";
                    }
                    $stmt_events->close();
                    $conn->close();
                    ?></p>
            </div>
        </section>

        <section class="visualization-section">
            <div class="graph-placeholder">
                <img src="images/graph_placeholder.png" alt="Merit Graph">
                <button class="view-graph-button">View Graph</button>
            </div>
            <div class="pie-chart-placeholder">
                <img src="images/pie_chart_placeholder.png" alt="Merit Distribution Pie Chart">
                <button class="view-pie-chart-button">View Pie Chart</button>
            </div>
        </section>

        <section class="actions-section">
            <button class="export-pdf-button">Export PDF</button>
            <button class="back-button">Back</button>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButton = document.querySelector('.logout-button');
            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    alert('Logging out (this is a simulation)');
                    window.location.href = '/login';
                });
            }

            const backButton = document.querySelector('.back-button');
            if (backButton) {
                backButton.addEventListener('click', function() {
                    window.history.back(); // Or a specific navigation
                });
            }

            const exportPdfButton = document.querySelector('.export-pdf-button');
            if (exportPdfButton) {
                exportPdfButton.addEventListener('click', function() {
                    alert('Exporting to PDF (this is a simulation)');
                    // In a real application, trigger the PDF generation process
                    window.location.href = '/generate_dashboard_pdf.php'; // Example PHP script
                });
            }

            const viewGraphButton = document.querySelector('.view-graph-button');
            if (viewGraphButton) {
                viewGraphButton.addEventListener('click', function() {
                    alert('Navigating to Merit Graph (this is a simulation)');
                    window.location.href = '/merit_graph.html'; // Example page
                });
            }

            const viewPieChartButton = document.querySelector('.view-pie-chart-button');
            if (viewPieChartButton) {
                viewPieChartButton.addEventListener('click', function() {
                    alert('Navigating to Merit Pie Chart (this is a simulation)');
                    window.location.href = '/merit_pie_chart.html'; // Example page
                });
            }
        });
    </script>
</body>
</html>