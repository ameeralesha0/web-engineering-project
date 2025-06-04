<?php
session_start();
$host = "localhost";
$username = "webuser";
$password = "admin1234"; // your actual password if any
$database = "mypetakom"; // correct database name
$port = 4306;

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

// Handle file upload helper
function uploadFile($file) {
    $targetDir = "uploads/";
    $fileName = basename($file["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allow only pdf/doc/docx files
    $allowedTypes = ['pdf', 'doc', 'docx'];
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $targetFilePath;
    } else {
        return false;
    }
}


// Handle Add Claim
if (isset($_POST['add_claim'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_level = $_POST['event_level'];
    $position = $_POST['position'];
    $claim_date = date('Y-m-d');
    
    if ($_FILES['support_doc']['name']) {
        $uploadPath = uploadFile($_FILES['support_doc']);
        if (!$uploadPath) {
            $error = "Invalid file type or upload error. Allowed: pdf, doc, docx.";
        }
    } else {
        $uploadPath = null;
    }

    if (!isset($error)) {
        // Generate unique claim_id
        $claim_id = 'C' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
        
        $stmt = $conn->prepare("INSERT INTO merit_claim (claim_id, student_id, event_id, event_name, event_level, position, support_doc, claim_status, claim_date)
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING', ?)");
        $stmt->bind_param("ssssssss", $claim_id, $student_id, $event_id, $event_name, $event_level, $position, $uploadPath, $claim_date);
        $stmt->execute();
        $stmt->close();
        header("Location: claim_merit.php");
        exit();
    }
}

// Handle Update Claim
if (isset($_POST['update_claim'])) {
    $claim_id = $_POST['claim_id'];
    $event_name = $_POST['event_name'];
    $event_level = $_POST['event_level'];
    $position = $_POST['position'];
    
    // Check if claim is still PENDING
    $stmt = $conn->prepare("SELECT claim_status, support_doc FROM merit_claim WHERE claim_id = ? AND student_id = ?");
    $stmt->bind_param("ss", $claim_id, $student_id);
    $stmt->execute();
    $stmt->bind_result($claim_status, $old_doc);
    $stmt->fetch();
    $stmt->close();
    
    if ($claim_status === 'PENDING') {
        // Handle file upload if new file given
        if ($_FILES['support_doc']['name']) {
            $uploadPath = uploadFile($_FILES['support_doc']);
            if (!$uploadPath) {
                $error = "Invalid file type or upload error. Allowed: pdf, doc, docx.";
            }
        } else {
            $uploadPath = $old_doc;
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE merit_claim SET event_name=?, event_level=?, position=?, support_doc=? WHERE claim_id=? AND student_id=?");
            $stmt->bind_param("ssssss", $event_name, $event_level, $position, $uploadPath, $claim_id, $student_id);
            $stmt->execute();
            $stmt->close();
            header("Location: claim_merit.php");
            exit();
        }
    } else {
        $error = "Cannot update claim that is already submitted or processed.";
    }
}

// Handle Delete Claim
if (isset($_GET['delete']) && $_GET['delete'] != '') {
    $claim_id = $_GET['delete'];
    // Only delete if PENDING
    $stmt = $conn->prepare("SELECT claim_status FROM merit_claim WHERE claim_id = ? AND student_id = ?");
    $stmt->bind_param("ss", $claim_id, $student_id);
    $stmt->execute();
    $stmt->bind_result($claim_status);
    $stmt->fetch();
    $stmt->close();
    
    if ($claim_status === 'PENDING') {
        $stmt = $conn->prepare("DELETE FROM merit_claim WHERE claim_id = ? AND student_id = ?");
        $stmt->bind_param("ss", $claim_id, $student_id);
        $stmt->execute();
        $stmt->close();
        header("Location: claim_merit.php");
        exit();
    } else {
        $error = "Cannot delete claim that is already submitted or processed.";
    }
}

// Fetch all claims for logged in student
$result = $conn->query("SELECT * FROM merit_claim WHERE student_id = '$student_id' ORDER BY claim_date DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="claimmerit.css">

</head>
<header>
        <img src="petakom.jpg" alt="MyPetakom Logo" class="logo">
        <h1>Claim Merit</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>
<body>
    
    
    <?php if (isset($error)) echo '<p class="error">'.$error.'</p>'; ?>
    
    <!-- Add / Update form -->
    <?php 
    // If update mode?
    if (isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $stmt = $conn->prepare("SELECT * FROM merit_claim WHERE claim_id = ? AND student_id = ?");
        $stmt->bind_param("ss", $edit_id, $student_id);
        $stmt->execute();
        $claim = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($claim['claim_status'] !== 'PENDING') {
            echo "<p>You cannot edit a claim that is not pending.</p>";
        } else {
    ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="claim_id" value="<?= htmlspecialchars($claim['claim_id']) ?>">
        <label>Event Name</label>
        <input type="text" name="event_name" required value="<?= htmlspecialchars($claim['event_name']) ?>">
        
        <label>Event Level</label>
        <select name="event_level" required>
            <?php
            $levels = ['International', 'National', 'State', 'District', 'UMPSA'];
            foreach ($levels as $level) {
                $selected = ($claim['event_level'] == $level) ? "selected" : "";
                echo "<option value=\"$level\" $selected>$level</option>";
            }
            ?>
        </select>

        <label>Position</label>
        <select name="position" required>
            <?php
            $positions = ['Main', 'Committee', 'Participant'];
            foreach ($positions as $pos) {
                $selected = ($claim['position'] == $pos) ? "selected" : "";
                echo "<option value=\"$pos\" $selected>$pos</option>";
            }
            ?>
        </select>

        <label>Supporting Document (PDF/DOC/DOCX)</label>
        <input type="file" name="support_doc" accept=".pdf,.doc,.docx">
        <small>Leave empty to keep existing document.</small><br>

        <input type="submit" name="update_claim" value="Update Claim">
        <a href="claim_merit.php"><button type="button">Cancel</button></a>
    </form>

    <?php
        }
    } else {
    ?>
    <!-- Add new claim -->
    <form method="post" enctype="multipart/form-data">
        <label>Event ID</label>
        <input type="text" name="event_id" required placeholder="Enter event ID e.g. EVT001">

        <label>Event Name</label>
        <input type="text" name="event_name" required placeholder="Enter event name">

        <label>Event Level</label>
        <select name="event_level" required>
            <option value="">Select level</option>
            <option value="International">International</option>
            <option value="National">National</option>
            <option value="State">State</option>
            <option value="District">District</option>
            <option value="UMPSA">UMPSA</option>
        </select>

        <label>Position</label>
        <select name="position" required>
            <option value="">Select position</option>
            <option value="Main">Main</option>
            <option value="Committee">Committee</option>
            <option value="Participant">Participant</option>
        </select>

        <label>Supporting Document (PDF/DOC/DOCX)</label>
        <input type="file" name="support_doc" accept=".pdf,.doc,.docx" required>

        <input type="submit" name="add_claim" value="Submit Claim">
        <a href="studentpage.php" class="button-link">Back</a>

    </form>
    <?php } ?>

    <h3>Your Claims</h3>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Event Name</th>
                <th>Level</th>
                <th>Position</th>
                <th>Status</th>
                <th>Claim Date</th>
                <th>Supporting Document</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['event_id']) ?></td>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['event_level']) ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= htmlspecialchars($row['claim_status']) ?></td>
                <td><?= htmlspecialchars($row['claim_date']) ?></td>
                <td>
                    <?php if ($row['support_doc']) { ?>
                    <a href="<?= htmlspecialchars($row['support_doc']) ?>" target="_blank">View</a>
                    <?php } else { echo "No file"; } ?>
                </td>
                <td>
                    <?php if ($row['claim_status'] === 'PENDING') { ?>
                        <a href="claim_merit.php?edit=<?= $row['claim_id'] ?>">Edit</a> |
                        <a href="claim_merit.php?delete=<?= $row['claim_id'] ?>" onclick="return confirm('Delete this claim?')">Delete</a>
                    <?php } else {
                        echo "-";
                    } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
