<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <div class="text-center mb-4">
                <img src="user-profile-picture.jpg" class="rounded-circle" width="100" alt="Profile">
                <h5 class="mt-2"><?php echo $_SESSION['user_name']; ?></h5>
            </div>
            
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="button">Manage Event Registration</button>
                <button class="btn btn-secondary" type="button">Manage Event Committee</button>
                <button class="btn btn-secondary" type="button">Manage Merit Application</button>
                <button class="btn btn-secondary" type="button">Manage QR Code</button>
                <button class="btn btn-secondary" type="button">Generate Report</button>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>