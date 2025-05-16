<?php
session_start();

// Redirect if not logged in or not coordinator
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

// Dummy list of applications (replace with DB queries)
$membershipApplications = [
    ['id' => 1, 'name' => 'John Doe', 'student_card' => 'approved-id.jpg'],
    ['id' => 2, 'name' => 'Jane Smith', 'student_card' => 'rejected-id.jpg']
];

// Handle approval (simulation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    $approvedId = $_POST['approve_id'];
    // Here you'd update the DB to approve the application
    $success = "Application ID $approvedId has been approved.";
}

$user = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'coordinator';

// Determine dashboard based on role
switch ($role) {
    case 'coordinator':
        $previous = 'coordinatorpage.php';
        break;
    case 'advisor':
        $previous = 'advisorpage.php';
        break;
    default:
        $previous = 'studentpage.php';
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
        <button type="submit" class="blue-btn">Generate report</button>
    </form>
    <form action="coordinatordashboard.php">
        <button type="submit" class="blue-btn">Dashboard</button>
    </form>
    <button onclick="location.href='logout.php'" class="logout">Logout</a>
</div>

    <div class="main">
        <div class="top-bar">
            <div class="title">Approve Membership</div>
        </div>

        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <?php foreach ($membershipApplications as $app): ?>
            <div class="application">
                <strong>Applicant Name:</strong> <?= htmlspecialchars($app['name']) ?><br>
                <strong>Student Card:</strong><br>
                <img src="<?= htmlspecialchars($app['student_card']) ?>" alt="Student Card"><br>
                <form method="post">
                    <input type="hidden" name="approve_id" value="<?= $app['id'] ?>">
                    <button type="submit" class="approve-btn">Approve</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
