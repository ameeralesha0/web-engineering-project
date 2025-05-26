<?php
require_once __DIR__ . '/../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="../assets/css/advisorstyle.css">
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
                                <h2 class="d-inline-block mb-0"><i class="fas fa-calendar-plus"></i> Create Event</h2>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="container-fluid">
                    <!-- Event creation form here -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>