<?php
// attendance/advisor/sidebar.php
?>
<!-- Sidebar Navigation -->
<div class="col-md-3 col-lg-2 d-md-block advisor-sidebar">
    <div class="position-sticky pt-3">
        <!-- Logo Section - Added at the top -->
        <div class="sidebar-logo-container text-center mb-4">
            <img src="petakomLogo.jpeg" alt="PETAKOM Logo" class="sidebar-logo">
            <!-- Keep the advisor title below the logo if needed -->
            <h4 class="advisor-brand mt-2">
                <i class="fas fa-user-tie"></i> Advisor Panel
            </h4>
        </div>
        
        <ul class="nav flex-column advisor-nav">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="sidebar-heading">ATTENDANCE</li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-clipboard-list"></i> View Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../qr_generator.php">
                    <i class="fas fa-qrcode"></i> QR Generator
                </a>
            </li>
            
            <li class="sidebar-heading">EVENT MANAGEMENT</li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>" href="events.php">
                    <i class="fas fa-calendar-plus"></i> Create Event
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'attendance_slots.php' ? 'active' : '' ?>" href="attendance_slots.php">
                    <i class="fas fa-calendar-check"></i> Attendance Slots
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_events.php' ? 'active' : '' ?>" href="manage_events.php">
                    <i class="fas fa-calendar-alt"></i> Manage Events
                </a>
            </li>
            
            <li class="sidebar-heading">STUDENT SYSTEM</li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_merit.php' ? 'active' : '' ?>" href="manage_merit.php">
                    <i class="fas fa-star"></i> Manage Merit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="student_records.php">
                    <i class="fas fa-users"></i> Student Records
                </a>
            </li>
            
            <li class="sidebar-divider"></li>
            
            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
    /* Add this CSS to your stylesheet */
    .sidebar-logo {
        max-width: 180px;
        height: auto;
        margin: 0 auto;
        display: block;
    }
    .sidebar-logo-container {
        padding: 15px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .advisor-brand {
        font-size: 1.1rem;
        color: #fff;
    }
</style>