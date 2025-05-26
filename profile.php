<?php 
  session_start(); 
  include("server.php"); // DB connection

  if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit();
  }

  $role = $_SESSION['role'];
  $currentUsername = $_SESSION['username'];

  // Fetch current user info
  $query = "SELECT * FROM users WHERE username = '$currentUsername' LIMIT 1";
  $result = mysqli_query($db, $query);
  $user = mysqli_fetch_assoc($result);

  $email = $user['email'];
  $password = $user['password'];

  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = mysqli_real_escape_string($db, $_POST['email']);
    $newUsername = mysqli_real_escape_string($db, $_POST['username']);
    $newPassword = !empty($_POST['password']) ? md5($_POST['password']) : $password;

    $update = "UPDATE users SET email='$newEmail', username='$newUsername', password='$newPassword' WHERE username='$currentUsername'";

    if (mysqli_query($db, $update)) {
      $_SESSION['username'] = $newUsername;
      $_SESSION['email'] = $newEmail;

      echo "<script>alert('Account updated successfully.'); window.location='profile.php';</script>";
    } else {
      echo "<script>alert('Failed to update account.');</script>";
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Account - MyPetakom</title>
    <link rel="stylesheet" href="styleprofile.css">
</head>
<body>
    <div class="sidebar">
        <img src="logo-petakom.png" alt="MyPetakomLogo" class="logo">

        <form action="<?= htmlspecialchars($dashboard ?? '#') ?>" method="get">
            <button type="submit" class="blue-btn">Main</button>
        </form>

        <form action="accountdetails.php">
            <button type="submit" class="blue-btn">Account Details</button>
        </form>

        <?php if ($role === 'student'): ?>
            <form action="membership.php">
                <button type="submit" class="blue-btn">Manage Membership</button>
            </form>
            <form action="merit.php">
                <button type="submit" class="blue-btn">Manage Merit</button>
            </form>
            <form action="viewmerit.php">
                <button type="submit" class="blue-btn">View Merit</button>
            </form>
            <form action="missingmerit.php">
                <button type="submit" class="blue-btn">Claim Messing Merit</button>
            </form>
        <?php endif; ?>

        <?php if ($role === 'coordinator'): ?>
            <form action="approve_membership.php">
                <button type="submit" class="blue-btn">Manage Membership</button>
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
        <?php endif; ?>

        <?php if ($role === 'advisor'): ?>
            <form action="registration.php">
                <button type="submit" class="blue-btn">Registration</button>
            </form>
            <form action="merit.php">
                <button type="submit" class="blue-btn">Manage Merit</button>
            </form>
            <form action="eventcommittee.php">
                <button type="submit" class="blue-btn">Event Committee</button>
            </form>
            <form action="qrcode.php">
                <button type="submit" class="blue-btn">Qr Code</button>
            </form>
        <?php endif; ?>

        <form action="coordinatordashboard.php">
            <button type="submit" class="blue-btn">Dashboard</button>
        </form>
        <form action="logout.php">
            <button type="submit" class="red-btn">Logout</button>
        </form>
    </div>

    <div class="main-content">
        <h2>Manage Account</h2>
        <form method="post">
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($currentUsername) ?>" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
</body>
</html>
