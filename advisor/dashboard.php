<?php
require_once __DIR__ . '/../includes/functions.php';
// Add authentication check here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Dashboard</title>
    <link rel="stylesheet" href="../assets/css/advisorstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="advisor-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Enhanced Sidebar Navigation -->
            <div class="col-md-3 col-lg-2 d-md-block advisor-sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="advisor-brand">
                            <i class="fas fa-user-tie"></i> Advisor
                        </h4>
                    </div>
                    
                    <ul class="nav flex-column advisor-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
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
                            <a class="nav-link" href="events.php">
                                <i class="fas fa-calendar-plus"></i> Create Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_events.php">
                                <i class="fas fa-calendar-alt"></i> Manage Events
                            </a>
                        </li>
                        
                        <li class="sidebar-heading">STUDENT SYSTEM</li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_merit.php">
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
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 advisor-content">
                <header class="advisor-header mb-4">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="javascript:history.back()" class="btn btn-back">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <h2 class="d-inline-block mb-0"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i> Advisor Name
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="container-fluid">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card advisor-card card-attendance">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Today's Attendance</h5>
                                            <p class="card-text">Students attended today</p>
                                        </div>
                                        <div class="card-stat text-primary">87</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card advisor-card card-events">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Upcoming Events</h5>
                                            <p class="card-text">Events this week</p>
                                        </div>
                                        <div class="card-stat text-warning">3</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card advisor-card card-students">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Total Students</h5>
                                            <p class="card-text">Registered students</p>
                                        </div>
                                        <div class="card-stat text-info">142</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<!-- Recent Activity Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Recent Attendance Table -->
                            <div class="card advisor-table">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Attendance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Event</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>John Doe</td>
                                                    <td>Tech Conference</td>
                                                    <td>Today, 08:55 AM</td>
                                                    <td><span class="status-badge badge-present">Present</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Jane Smith</td>
                                                    <td>Tech Conference</td>
                                                    <td>Today, 09:15 AM</td>
                                                    <td><span class="status-badge badge-late">Late</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Alex Johnson</td>
                                                    <td>Workshop</td>
                                                    <td>Yesterday, 01:45 PM</td>
                                                    <td><span class="status-badge badge-present">Present</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Upcoming Events -->
                            <div class="card advisor-table">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Upcoming Events</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Annual Science Fair</h6>
                                                <small>Tomorrow</small>
                                            </div>
                                            <p class="mb-1">Main Hall, 9:00 AM - 3:00 PM</p>
                                            <small>45 students registered</small>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Career Workshop</h6>
                                                <small>Dec 22</small>
                                            </div>
                                            <p class="mb-1">Room 101, 2:00 PM - 4:00 PM</p>
                                            <small>32 students registered</small>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Programming Competition</h6>
                                                <small>Dec 24</small>
                                            </div>
                                            <p class="mb-1">Computer Lab, 10:00 AM - 5:00 PM</p>
                                            <small>18 students registered</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>