<!--Login Authentication, after combine with module 1-->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPetakom - <?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2c3e50;
            --secondary-blue: #34495e;
            --accent-green: #27ae60;
            --text-light: #ecf0f1;
        }
        
        .sidebar {
            background-color: var(--primary-blue);
            height: 100vh;
            padding: 20px;
            position: fixed;
            width: 280px;
        }
        
        .brand-logo {
            width: 180px;
            margin: 20px auto;
            display: block;
        }
        
        .user-profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--secondary-blue);
        }
        
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-green);
        }
        
        .nav-button {
            width: 100%;
            margin: 8px 0;
            text-align: left;
            padding: 12px 20px;
            border-radius: 5px;
            transition: all 0.3s;
            color: var(--text-light);
        }
        
        .nav-button:hover {
            background-color: var(--secondary-blue);
            transform: translateX(5px);
        }
        
        .nav-button.active {
            background-color: var(--accent-green);
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            background-color: #f5f6fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="../assets/images/mypetakom-logo.png" alt="MyPetakom Logo" class="brand-logo">
        
        <div class="user-profile">
            <img src="../assets/uploads/profile/<?php echo $_SESSION['profile_pic']; ?>" 
                 class="profile-pic" 
                 alt="User Profile">
            <h5 class="mt-3 text-white"><?php echo $_SESSION['user_name']; ?></h5>
        </div>

        <div class="navigation-menu">
            <button class="btn nav-button active">Manage Event Registration</button>
            <button class="btn nav-button">Manage Event Committee</button>
            <button class="btn nav-button">Manage Merit Application</button>
            <button class="btn nav-button">Manage QR Code</button>
            <button class="btn nav-button">Generate Reports</button>
            
            <div class="mt-4">
                <a href="profile.php" class="btn nav-button">View Profile</a>
                <a href="../actions/logout.php" class="btn nav-button">Logout</a>
            </div>
        </div>
    </div>

    <main class="main-content">