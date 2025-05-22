<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merit Awarded</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/mypetakom_logo.png" alt="Mypetakom Logo">
        </div>
        <h1>Merit Awarded</h1>
        <button class="logout-button">Logout</button>
    </header>

    <main>
        <div class="merit-list-filter">
            <p class="merit-list-title">My merit list (semester:
                <select id="semesterFilter" onchange="filterMerits(this.value)">
                    <option value="">All Semesters</option>
                    <?php
                        // Include database connection (replace with your actual connection details)
                        $servername = "localhost";
                        $username = "your_db_username";
                        $password = "your_db_password";
                        $dbname = "your_db_name";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Assuming you have a way to get distinct semesters for the student
                        $studentID = "[USER_ID]"; // Replace with actual student ID from session
                        $sql_semesters = "SELECT DISTINCT semester FROM awarded_merits WHERE student_id = ?";
                        $stmt_semesters = $conn->prepare($sql_semesters);
                        $stmt_semesters->bind_param("s", $studentID);
                        $stmt_semesters->execute();
                        $result_semesters = $stmt_semesters->get_result();

                        if ($result_semesters->num_rows > 0) {
                            while ($row_semester = $result_semesters->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row_semester["semester"]) . "'>" . htmlspecialchars($row_semester["semester"]) . "</option>";
                            }
                        }

                        $stmt_semesters->close();
                        // $conn->close(); // Connection will be closed later
                    ?>
                </select>)
            </p>
        </div>

        <div class="merit-list-container">
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Role</th>
                        <th>Level</th>
                        <th>Date</th>
                        <th>Merit Points</th>
                    </tr>
                </thead>
                <tbody id="meritTableBody">
                    <?php
                        // Fetch awarded merits for the current student (initially, all semesters)
                        $sql_merits = "SELECT event_name, role_in_event, event_level, award_date, merit_points FROM awarded_merits WHERE student_id = ?";
                        $stmt_merits = $conn->prepare($sql_merits);
                        $stmt_merits->bind_param("s", $studentID);
                        $stmt_merits->execute();
                        $result_merits = $stmt_merits->get_result();

                        if ($result_merits->num_rows > 0) {
                            while ($row_merit = $result_merits->fetch_assoc()) {
                                echo "<tr data-semester='" . htmlspecialchars($row_merit["semester"]) . "'>";
                                echo "<td>" . htmlspecialchars($row_merit["event_name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row_merit["role_in_event"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row_merit["event_level"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row_merit["award_date"]) . "</td>";
                                echo "<td class='merit-points'>" . htmlspecialchars($row_merit["merit_points"]) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No merits awarded yet.</td></tr>";
                        }

                        $stmt_merits->close();
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

        <div class="total-merit-container">
            <p class="total-merit-text">Total Merit: <span id="totalMeritPoints">
                <?php
                    // Calculate and display total merit points
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
                ?>
            </span></p>
        </div>

        <div class="actions-container">
            <a class="download-pdf-link" href="#">Download PDF</a>
            <a class="qr-code-link" href="#">QR Code</a>
            <button class="back-button">Back</button>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.back-button');
            if (backButton) {
                backButton.addEventListener('click', function() {
                    window.history.back();
                });
            }

            const logoutButton = document.querySelector('.logout-button');
            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    alert('Logging out (this is a simulation)');
                    window.location.href = '/login';
                });
            }
        });

        function filterMerits(semester) {
            const tableRows = document.querySelectorAll('#meritTableBody tr');
            let totalPoints = 0;

            tableRows.forEach(row => {
                const rowSemester = row.getAttribute('data-semester');
                const meritPointsCell = row.querySelector('.merit-points');

                if (semester === '' || rowSemester === semester) {
                    row.style.display = '';
                    if (meritPointsCell && !isNaN(parseInt(meritPointsCell.textContent))) {
                        totalPoints += parseInt(meritPointsCell.textContent);
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('totalMeritPoints').textContent = totalPoints;
        }
    </script>
</body>
</html>