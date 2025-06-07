<?php
// advisor_dashboard.php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

// Fetch data for summary cards
$event_count = $active_count = $postponed_count = $cancelled_count = $merit_yes_count = $committee_count = 0;

$summary_query = "SELECT 
    (SELECT COUNT(*) FROM events WHERE advisor_id = ?) AS total,
    (SELECT COUNT(*) FROM events WHERE advisor_id = ? AND event_status = 'Active') AS active,
    (SELECT COUNT(*) FROM events WHERE advisor_id = ? AND event_status = 'Postponed') AS postponed,
    (SELECT COUNT(*) FROM events WHERE advisor_id = ? AND event_status = 'Cancelled') AS cancelled,
    (SELECT COUNT(*) FROM events WHERE advisor_id = ? AND merit_applied = 'Yes') AS merit_yes,
    (SELECT COUNT(*) FROM event_committee WHERE event_id IN (SELECT event_id FROM events WHERE advisor_id = ?)) AS committee_total";

$stmt = $conn->prepare($summary_query);
$stmt->bind_param("ssssss", $advisor_id, $advisor_id, $advisor_id, $advisor_id, $advisor_id, $advisor_id);
$stmt->execute();
$stmt->bind_result($event_count, $active_count, $postponed_count, $cancelled_count, $merit_yes_count, $committee_count);
$stmt->fetch();
$stmt->close();

// Fetch events
$events = [];
$event_query = "SELECT event_id, event_name, event_date_time, event_status, qr_code_link, merit_applied FROM events WHERE advisor_id = ?";
$stmt = $conn->prepare($event_query);
$stmt->bind_param("s", $advisor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Advisor Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Event Advisor Dashboard</h2>

  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-md-2"><div class="card text-bg-primary mb-3"><div class="card-body"><h6>Total Events</h6><p class="fs-4"><?= $event_count ?></p></div></div></div>
    <div class="col-md-2"><div class="card text-bg-success mb-3"><div class="card-body"><h6>Active</h6><p class="fs-4"><?= $active_count ?></p></div></div></div>
    <div class="col-md-2"><div class="card text-bg-warning mb-3"><div class="card-body"><h6>Postponed</h6><p class="fs-4"><?= $postponed_count ?></p></div></div></div>
    <div class="col-md-2"><div class="card text-bg-danger mb-3"><div class="card-body"><h6>Cancelled</h6><p class="fs-4"><?= $cancelled_count ?></p></div></div></div>
    <div class="col-md-2"><div class="card text-bg-info mb-3"><div class="card-body"><h6>With Merit</h6><p class="fs-4"><?= $merit_yes_count ?></p></div></div></div>
    <div class="col-md-2"><div class="card text-bg-secondary mb-3"><div class="card-body"><h6>Total Committees</h6><p class="fs-4"><?= $committee_count ?></p></div></div></div>
  </div>

  <!-- Event Table -->
  <h4>My Events</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Event Name</th>
        <th>Date & Time</th>
        <th>Status</th>
        <th>QR Code</th>
        <th>Merit Applied</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($events as $event): ?>
      <tr>
        <td><?= htmlspecialchars($event['event_name']) ?></td>
        <td><?= htmlspecialchars($event['event_date_time']) ?></td>
        <td><span class="badge bg-<?= $event['event_status'] === 'Active' ? 'success' : ($event['event_status'] === 'Postponed' ? 'warning' : 'danger') ?>"><?= $event['event_status'] ?></span></td>
        <td><?= $event['qr_code_link'] ? '<a href="' . htmlspecialchars($event['qr_code_link']) . '" target="_blank">View QR</a>' : 'N/A' ?></td>
        <td><?= $event['merit_applied'] ?></td>
        <td><a href="edit_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-primary">Edit</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
