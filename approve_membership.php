<?php 
include('server.php');

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership_id'], $_POST['action'])) {
    $membershipId = mysqli_real_escape_string($db, $_POST['membership_id']);
    $action = $_POST['action']; // 'approve' or 'reject'

    if (in_array($action, ['approve', 'reject'])) {
        $query = "UPDATE membership SET status = '$action' WHERE membership_id = '$membershipId'";
        if (mysqli_query($db, $query)) {
            $_SESSION['success'] = "Membership ID $membershipId has been " . $action . "d.";
        } else {
            $_SESSION['error'] = "Error updating status: " . mysqli_error($db);
        }
        header("Location: approve_membership.php");
        exit();
    }
}

// Fetch applications (only those not approved), and join student for full info
$membershipApplications = [];
$query = "
    SELECT membership.*, student.student_name 
    FROM membership 
    JOIN student ON membership.student_id = student.student_id 
    WHERE membership.status != 'approved'
";
$result = mysqli_query($db, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $membershipApplications[] = $row;
    }
} else {
    $_SESSION['error'] = "Error fetching applications: " . mysqli_error($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Membership - MyPetakom</title>
    <link rel="stylesheet" href="styleapprovemem.css">
</head>
<body>
<div class="sidebar">
    <img src="logo-petakom.png" alt="Logo">
    <form action="coordinatorpage.php">
        <button type="submit" class="blue-btn">Main</button>
    </form>
    <button onclick="location.href='accountdetails.php'" class="blue-btn">Account Details</button>
    <form action="profile.php">
        <button type="submit" class="blue-btn">Manage Account</button>
    </form>
    <form action="merit.php">
        <button type="submit" class="blue-btn">Manage Merit</button>
    </form>
    <form action="studentpage.php">
        <button type="submit" class="blue-btn">Student Page</button>
    </form>
    <form action="report.php">
        <button type="submit" class="blue-btn">Generate Report</button>
    </form>
    <form action="coordinatordashboard.php">
        <button type="submit" class="blue-btn">Dashboard</button>
    </form>
    <button onclick="location.href='logout.php'" class="logout">Logout</button>
</div>

<div class="main">
    <div class="top-bar">
        <div class="title">Approve Membership</div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (count($membershipApplications) === 0): ?>
        <p>No membership applications pending approval.</p>
    <?php else: ?>
        <?php foreach ($membershipApplications as $app): ?>
            <div class="application">
                <strong>Membership ID:</strong> <?= htmlspecialchars($app['membership_id']) ?><br>
                <strong>Student ID:</strong> <?= htmlspecialchars($app['student_id']) ?><br>
                <strong>Student Name:</strong> <?= htmlspecialchars($app['student_name']) ?><br>
                <strong>Club Name:</strong> <?= htmlspecialchars($app['membership_name']) ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($app['status']) ?><br>

                <?php if (!empty($app['upload_card'])): ?>
                    <strong>Uploaded Card:</strong><br>
                    <img src="uploads/<?= htmlspecialchars($app['upload_card']) ?>" alt="Uploaded Card" style="max-width: 200px;"><br>
                <?php endif; ?>

                <form method="post" style="margin-top: 10px;">
                    <input type="hidden" name="membership_id" value="<?= htmlspecialchars($app['membership_id']) ?>">
                    <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                    <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
