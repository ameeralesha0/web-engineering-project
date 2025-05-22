<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim History</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/mypetakom_logo.png" alt="Mypetakom Logo">
        </div>
        <h1>Claim History</h1>
        <button class="logout-button">Logout</button>
    </header>

    <main>
        <div class="history-table-container">
            <table>
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

                        // Assuming you have a student ID in the session (replace with your actual session handling)
                        $studentID = "[USER_ID]"; // Replace with actual student ID from session

                        $sql = "SELECT submission_date, student_id, event_name, event_level, position, status FROM claims WHERE student_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $studentID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["submission_date"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["student_id"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["event_name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["event_level"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["position"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No claim history found.</td></tr>";
                        }

                        $stmt->close();
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

        <div class="back-button-container">
            <button class="back-button">Back</button>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.back-button');
            if (backButton) {
                backButton.addEventListener('click', function() {
                    window.history.back(); // Simple way to go back to the previous page
                    // Or you could use window.location.href = '/dashboard.html'; for a specific page
                });
            }

            const logoutButton = document.querySelector('.logout-button');
            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    alert('Logging out (this is a simulation)');
                    // In a real application, you would handle the logout process
                    window.location.href = '/login'; // Example redirect
                });
            }
        });
    </script>
</body>
</html>