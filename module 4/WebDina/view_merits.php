<?php
session_start();
$host = "localhost";
$username = "webuser";
$password = "admin1234";
$database = "mypetakom";
$port = 4306;

$conn = new mysqli($host, $username, $password, $database, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT student_name, student_course FROM student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($name, $course);
$stmt->fetch();
$stmt->close();

// Fetch merit claims (approved only)
$merit_query = $conn->query("SELECT event_level, position FROM merit_claim WHERE student_id = '$student_id' AND claim_status = 'APPROVED'");

// Merit scoring table
function calculateMerit($level, $position) {
    $score = 0;
    $position_scores = [
        "Main" => 10,
        "Committee" => 7,
        "Participant" => 5
    ];
    $level_multiplier = [
        "International" => 2,
        "National" => 1.8,
        "State" => 1.5,
        "District" => 1.2,
        "UMPSA" => 1
    ];

    if (isset($position_scores[$position]) && isset($level_multiplier[$level])) {
        $score = $position_scores[$position] * $level_multiplier[$level];
    }

    return $score;
}

// Calculate total merit
$total_merit = 0;
while ($row = $merit_query->fetch_assoc()) {
    $total_merit += calculateMerit($row['event_level'], $row['position']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Merits</title>
    <link rel="stylesheet" href="viewmerits.css" />
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<header>
        <img src="petakom.jpg" alt="MyPetakom Logo" class="logo">
        <h1>Claim Missing Merit</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>
<body>
    <div class="merit-box">
        <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
        <p><strong>Student ID:</strong> <?= htmlspecialchars($student_id) ?></p>
        <p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>
        <p><strong>Total Merit:</strong> <?= number_format($total_merit, 2) ?> points</p>
    </div>

    <div class="qr-container">
        <h3>Scan QR Code to view merit</h3>
        <div id="qrcode"></div>
    </div>

    <script>
        // QR Code links to public student QR profile
        const qrLink = "http://localhost/mypetakom/student_qr_view.php?sid=<?= urlencode($student_id) ?>";
        new QRCode(document.getElementById("qrcode"), {
            text: qrLink,
            width: 128,
            height: 128
        });
    </script>
    <a href="studentpage.php" class="button">Back</a>
  <button class="button" onclick="window.print()">Print Merit Info</button>
</body>
</html>
