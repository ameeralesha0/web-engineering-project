<?php
$host = "localhost";
$username = "webuser";
$password = "admin1234";
$database = "mypetakom";
$port = 4306;

$conn = new mysqli($host, $username, $password, $database, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_GET['sid'])) {
    die("Student ID missing.");
}
$student_id = $_GET['sid'];

// Fetch student data
$stmt = $conn->prepare("SELECT student_name, student_course FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($name, $course);
$stmt->fetch();
$stmt->close();

// Fetch approved merit claims
$result = $conn->query("SELECT event_level, position FROM merit_claim WHERE student_id = '$student_id' AND claim_status = 'APPROVED'");

// Merit logic
function calculateMerit($level, $position) {
    $score = 0;
    $position_scores = ["Main" => 10, "Committee" => 7, "Participant" => 5];
    $level_multiplier = ["International" => 2, "National" => 1.8, "State" => 1.5, "District" => 1.2, "UMPSA" => 1];

    if (isset($position_scores[$position]) && isset($level_multiplier[$level])) {
        $score = $position_scores[$position] * $level_multiplier[$level];
    }

    return $score;
}

$total_merit = 0;
while ($row = $result->fetch_assoc()) {
    $total_merit += calculateMerit($row['event_level'], $row['position']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Merit Summary</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 40px auto; border: 1px solid #ccc; padding: 30px; }
    </style>
</head>
<body>
    <h2>Student Merit Report</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>Student ID:</strong> <?= htmlspecialchars($student_id) ?></p>
    <p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>
    <p><strong>Total Merit This Year:</strong> <?= number_format($total_merit, 2) ?> points</p>
</body>
</html>