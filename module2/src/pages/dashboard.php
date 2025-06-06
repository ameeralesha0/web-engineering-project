<!--Database connection -->
<?php
require_once 'config/db_config.php';

// Get dashboard stats
$advisor_id = $_SESSION['advisor_id'];

$total_events = $conn->query("SELECT COUNT(*) FROM event WHERE advisor_id = '$advisor_id'")->fetch_row()[0];
$pending_merits = $conn->query("SELECT COUNT(*) FROM merit_application 
                              WHERE advisor_id = '$advisor_id' 
                              AND application_status = 'Pending'")->fetch_row()[0];
$total_attendance = $conn->query("SELECT COUNT(*) FROM attendance 
                                WHERE event_id IN (SELECT event_id FROM event 
                                WHERE advisor_id = '$advisor_id')")->fetch_row()[0];
?>

<?php include '../components/header.php'; ?>

<div class="dashboard">
    <div class="metrics">
        <div class="metric-card">
            <h3>Total Events</h3>
            <p><?= getTotalEvents() ?></p>
        </div>
        <div class="metric-card">
            <h3>Pending Merits</h3>
            <p><?= getPendingMerits() ?></p>
        </div>
    </div>

    <div class="charts">
        <canvas id="eventsChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js implementation
const ctx = document.getElementById('eventsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar'],
        datasets: [{
            label: 'Events per Month',
            data: [12, 19, 3]
        }]
    }
});
</script>

<?php include '../components/footer.php'; ?>