<?php
require_once '../../components/header.php';
$page_title = "Application Details";
require_once '../../config/db_config.php';

$app_id = $conn->real_escape_string($_GET['id']);
$application = $conn->query("SELECT m.*, e.name as event_name, 
                            u.username as advisor_name, c.username as coordinator_name
                            FROM merits m
                            JOIN events e ON m.event_id = e.id
                            JOIN users u ON m.advisor_id = u.id
                            LEFT JOIN users c ON m.coordinator_id = c.id
                            WHERE m.id = $app_id")->fetch_assoc();

// Check permissions
if ($_SESSION['user_role'] === 'advisor' && $application['advisor_id'] != $_SESSION['user_id']) {
    header("Location: ../dashboard/");
    exit();
}
?>
<main class="main-content">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Application Details</h4>
        </div>
        
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Event Name</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($application['event_name']) ?></dd>

                <dt class="col-sm-3">Submitted By</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($application['advisor_name']) ?></dd>

                <dt class="col-sm-3">Submission Date</dt>
                <dd class="col-sm-9"><?= date('d M Y H:i', strtotime($application['created_at'])) ?></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-<?= 
                        $application['status'] === 'approved' ? 'success' : 
                        ($application['status'] === 'pending' ? 'warning' : 'danger') 
                    ?>">
                        <?= ucfirst($application['status']) ?>
                    </span>
                </dd>

                <?php if($application['status'] !== 'pending'): ?>
                <dt class="col-sm-3">Decision By</dt>
                <dd class="col-sm-9"><?= $application['coordinator_name'] ?? '--' ?></dd>

                <dt class="col-sm-3">Decision Notes</dt>
                <dd class="col-sm-9"><?= $application['decision_notes'] ? nl2br(htmlspecialchars($application['decision_notes'])) : '--' ?></dd>
                <?php endif; ?>

                <dt class="col-sm-3">Application Details</dt>
                <dd class="col-sm-9"><?= nl2br(htmlspecialchars($application['application_text'])) ?></dd>
            </dl>
            
            <?php if($_SESSION['user_role'] === 'coordinator' && $application['status'] === 'pending'): ?>
            <hr>
            <form action="../actions/merit_decision.php" method="POST">
                <input type="hidden" name="merit_id" value="<?= $application['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Decision Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" name="decision" value="approve" 
                            class="btn btn-success">Approve</button>
                    <button type="submit" name="decision" value="reject" 
                            class="btn btn-danger">Reject</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../../components/footer.php'; ?>