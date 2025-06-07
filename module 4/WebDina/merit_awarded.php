<?php
session_start();
$host = "localhost";
$username = "webuser";
$password = "admin1234"; // your actual password if any
$database = "mypetakom"; // correct database name
$port = 4306;

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student is logged in (adjust session variable accordingly)
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Query cumulative merit per semester
$sql = "
SELECT
  m.semester,
  SUM(
    CASE m.position
      WHEN 'Main' THEN 
        CASE mc.event_level
          WHEN 'International' THEN 100
          WHEN 'National' THEN 80
          WHEN 'State' THEN 60
          WHEN 'District' THEN 40
          WHEN 'UMPSA' THEN 30
          ELSE 0
        END
      WHEN 'Committee' THEN
        CASE mc.event_level
          WHEN 'International' THEN 70
          WHEN 'National' THEN 50
          WHEN 'State' THEN 40
          WHEN 'District' THEN 30
          WHEN 'UMPSA' THEN 20
          ELSE 0
        END
      WHEN 'Participant' THEN
        CASE mc.event_level
          WHEN 'International' THEN 50
          WHEN 'National' THEN 40
          WHEN 'State' THEN 30
          WHEN 'District' THEN 15
          WHEN 'UMPSA' THEN 5
          ELSE 0
        END
      ELSE 0
    END
  ) AS cumulative_merit
FROM
  merit m
  LEFT JOIN merit_claim mc ON m.event_id = mc.event_id AND m.student_id = mc.student_id
WHERE m.student_id = ?
GROUP BY m.semester
ORDER BY m.semester ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$merits = [];
while ($row = $result->fetch_assoc()) {
    $merits[] = $row;
}

// Also get total merit for the academic year (sum all semesters)
$total_merit = 0;
foreach ($merits as $m) {
    $total_merit += $m['cumulative_merit'];
}

// Get basic student info for QR code display page
$sqlStudent = "SELECT student_name, student_course, student_email FROM student WHERE student_id = ?";
$stmt2 = $conn->prepare($sqlStudent);
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$resStudent = $stmt2->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Merit Awarded</title>


<link rel="stylesheet" href="meritawarded.css" />


<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
  table { border-collapse: collapse; width: 50%; margin-bottom: 20px;}
  th, td { border: 1px solid #ccc; padding: 8px; text-align: center;}
  th { background: #eee; }
  #qrcode { margin-top: 20px; }
</style>
</head>
<body>

<h2>Merit Awarded for <?php echo htmlspecialchars($resStudent['student_name']); ?></h2>

<table>
  <thead>
    <tr>
      <th>Semester</th>
      <th>Cumulative Merit</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($merits as $m): ?>
    <tr>
      <td><?php echo htmlspecialchars($m['semester']); ?></td>
      <td><?php echo htmlspecialchars($m['cumulative_merit']); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <th>Total</th>
      <th><?php echo $total_merit; ?></th>
    </tr>
  </tbody>
</table>

<h3>Scan QR code to view your merit info</h3>
<div id="qrcode"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  // The URL that the QR code should encode
  const studentId = <?php echo json_encode($student_id); ?>;
  const url = window.location.origin + '/student_merit_info.php?student_id=' + studentId;

  // Generate QR code in the div #qrcode
  new QRCode(document.getElementById("qrcode"), {
    text: url,
    width: 180,
    height: 180,
  });

</script>
<a href="studentpage.php" class="button">Back</a>
  <button class="button" onclick="window.print()">Print Merit Info</button>

</body>
</html>
