<?php
require_once __DIR__ . '/../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Merit</title>
    <link rel="stylesheet" href="../assets/css/advisorstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="advisor-body">
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/sidebar.php'; ?>
            
            <div class="col-md-9 col-lg-10 advisor-content">
                <header class="advisor-header mb-4">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="dashboard.php" class="btn btn-back">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <h2 class="d-inline-block mb-0"><i class="fas fa-star"></i> Manage Merit</h2>
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
                    <div class="card advisor-table">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-star-half-alt"></i> Merit Points System</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Current Merit</th>
                                            <th>Last Award</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>STU1001</td>
                                            <td>John Doe</td>
                                            <td>85</td>
                                            <td>2023-12-15</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                                <button class="btn btn-sm btn-warning">
                                                    <i class="fas fa-minus"></i> Deduct
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>STU1002</td>
                                            <td>Jane Smith</td>
                                            <td>92</td>
                                            <td>2023-12-14</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                                <button class="btn btn-sm btn-warning">
                                                    <i class="fas fa-minus"></i> Deduct
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <h5><i class="fas fa-plus-circle"></i> Award Merit in Bulk</h5>
                                <form class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Select Event</label>
                                        <select class="form-select">
                                            <option>Tech Conference</option>
                                            <option>Workshop</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Points to Award</label>
                                        <input type="number" class="form-control" value="5" min="1">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Apply
                                        </button>
                                    </div>
                                </form>
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