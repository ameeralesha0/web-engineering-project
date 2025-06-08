<?php ?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Advisor Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fff;
    }

    .sidepanel {
      width: 0;
      position: fixed;
      z-index: 1000;
      height: 100vh;
      top: 0;
      left: 0;
      background-color: white;
      overflow-x: hidden;
      transition: 0.5s;
      padding-top: 20px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidepanel a {
      padding: 12px 15px 12px 35px;
      text-decoration: none;
      font-size: 16px;
      color:#373182;
      display: flex;
      align-items: center;
      transition: all 0.3s;
    }

    .sidepanel a:hover {
      color: #373182;
      background-color: #b7f7eb;
    }

    .sidepanel a.active {
      background-color: #3498db;
      color: white;
    }

    .sidepanel a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }

    .sidepanel .closebtn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 30px;
      color: #f7dc6f;
    }

    .openbtn {
      font-size: 20px;
      cursor: pointer;
      background-color: #2c3e50;
      color: white;
      padding: 12px 16px;
      border: none;
      border-radius: 4px;
      position: fixed;
      left: 20px;
      top: 20px;
      z-index: 999;
      transition: all 0.3s;
    }

    .openbtn:hover {
      background-color: #34495e;
      transform: scale(1.05);
    }

    .sidebar-logo-container {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin-bottom: 20px;
    }

    .sidebar-logo {
      max-width: 150px;
      height: auto;
    }

    .advisor-brand {
      font-size: 1.1rem;
      color: rgb(117, 194, 136);
      margin-top: 10px;
    }

    .sidebar-heading {
      color:rgb(22, 106, 136);
      padding: 15px 20px 5px;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .sidebar-divider {
      border-top: 1px solid rgba(255,255,255,0.1);
      margin: 15px 0;
    }

    .main-content {
      transition: margin-left 0.5s;
      padding: 20px;
      margin-left: 0;
    }

    .sidepanel-open .main-content {
      margin-left: 250px;
    }

    @media (max-width: 768px) {
      .sidepanel-open .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<!-- Toggle Button -->
<button class="openbtn" onclick="openNav()">☰ Menu</button>

<!-- Sidebar -->
<div id="mySidepanel" class="sidepanel">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>

  <div class="sidebar-logo-container">
    <img src="petakomLogo.jpeg" alt="PETAKOM Logo" class="sidebar-logo">
    <h4 class="advisor-brand"><i class="fas fa-user-tie"></i> Advisor</h4>
  </div>

  <!-- Main Section -->
  <div class="sidebar-heading">Main</div>
  <a href="accountdetails.php" class="<?= basename($_SERVER['PHP_SELF']) == 'accountdetails.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-circle-user"></i> Account Details
  </a>
  <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
    <i class="fa-solid fa-pen"></i> Manage Account
  </a>
  <a href="/web-engineering-project/module2/src/pages/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
    <i class="fas fa-tachometer-alt"></i> Dashboard
  </a>

  <!-- Module 2: Event Management -->
  <div class="sidebar-heading">Event Management</div>
  <a href="/web-engineering-project/module2/src/pages/events/create.php" class="<?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">
    <i class="fas fa-calendar-plus"></i> Create Event
  </a>
  <a href="/web-engineering-project/module2/src/pages/events/view.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_events.php' ? 'active' : '' ?>">
    <i class="fas fa-calendar-alt"></i> Manage Events
  </a>
  <a href="/web-engineering-project/module2/src/pages/committee/assign.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_committee.php' ? 'active' : '' ?>">
    <i class="fas fa-users-cog"></i> Manage Committee
  </a>
  <a href="/web-engineering-project/module2/src/pages/merits/apply.php" class="<?= basename($_SERVER['PHP_SELF']) == 'apply_event_merit.php' ? 'active' : '' ?>">
    <i class="fas fa-certificate"></i> Apply Event Merit
  </a>
  <a href="/web-engineering-project/module2/src/pages/qrcode/event_qrcode.php" class="<?= basename($_SERVER['PHP_SELF']) == 'event_qrcode.php' ? 'active' : '' ?>">
    <i class="fas fa-qrcode"></i> Event QR Code
  </a>

  <!-- Module 3: Attendance -->
  <div class="sidebar-heading">Attendance</div>
  <a href="../index.php">
    <i class="fas fa-clipboard-list"></i> View Attendance
  </a>
  <a href="../qr_generator.php">
    <i class="fas fa-qrcode"></i> QR Generator
  </a>

  <!-- Module 4: Student Merit -->
  <div class="sidebar-heading">Student System</div>
  <a href="manage_merit.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_merit.php' ? 'active' : '' ?>">
    <i class="fas fa-star"></i> Manage Merit
  </a>
  <a href="student_records.php">
    <i class="fas fa-users"></i> Student Records
  </a>

  <!-- Reports & Settings -->
  <div class="sidebar-divider"></div>
  <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
    <i class="fas fa-chart-bar"></i> Reports
  </a>
  <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
    <i class="fas fa-cog"></i> Settings
  </a>
</div>

<!-- Main Content -->
<div id="main" class="main-content">
  <!-- Page content goes here -->
</div>

<script>
  function openNav() {
    document.getElementById("mySidepanel").style.width = "250px";
    document.body.classList.add("sidepanel-open");
  }

  function closeNav() {
    document.getElementById("mySidepanel").style.width = "0";
    document.body.classList.remove("sidepanel-open");
  }

  window.addEventListener('click', function(e) {
    const panel = document.getElementById("mySidepanel");
    if (!panel.contains(e.target) && !e.target.matches('.openbtn')) {
      closeNav();
    }
  });
</script>

</body>
</html>
