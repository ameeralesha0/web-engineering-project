<?php
require_once '../../components/header.php';
$page_title = "My Merit Applications";
require_once '../../config/db_config.php';

// Get advisor's applications
$applications = $conn->query("SELECT m.*, e.name as event_name 
                            FROM merits m
                            JOIN events e ON m.event_id = e.id
                            WHERE m.advisor_id = {$_SESSION['user_id']}
                            ORDER BY m.created_at DESC");
?>
<main class="main-content">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Merit Applications</h4>
            <a href="apply.php" class="btn btn-light">+ New Application</a>
        </div>
        
        <div class="card-body">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                            <th>Decision Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($app = $applications->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['event_name']) ?></td>
                            <td><?= date('d M Y H:i', strtotime($app['created_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $app['status'] === 'approved' ? 'success' : 
                                    ($app['status'] === 'pending' ? 'warning' : 'danger') 
                                ?>">
                                    <?= ucfirst($app['status']) ?>
                                </span>
                            </td>
                            <td><?= $app['decision_notes'] ? htmlspecialchars($app['decision_notes']) : '--' ?></td>
                            <td>
                                <a href="details.php?id=<?= $app['id'] ?>" 
                                   class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../components/footer.php'; ?>