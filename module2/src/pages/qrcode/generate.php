<?php
// Database configuration
$servername = "localhost";
$username = "your_db_user";
$password = "your_db_pass";
$dbname = "your_db_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get event ID from query string
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Fetch event data
$event = [];
$stmt = $conn->prepare("SELECT id, name, date, time, location, description FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
}

if (empty($event)) {
    die("Event not found");
}

// QR code generation function
function generateEventQR($event_id) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    use Endroid\QrCode\Builder\Builder;
    use Endroid\QrCode\Encoding\Encoding;
    use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
    use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
    use Endroid\QrCode\Writer\PngWriter;

    // Create directory if not exists
    $qrDir = __DIR__ . '/qrcodes/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0755, true);
    }
    
    // Generate URL to event details page
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    $event_url = "$protocol://$domain" . dirname($_SERVER['REQUEST_URI']) . "/event_details.php?event_id=$event_id";
    
    $qrFilename = $qrDir . 'event_' . $event_id . '.png';

    // Generate QR code
    $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data($event_url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(10)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();

    $qrCode->saveToFile($qrFilename);
    
    return 'qrcodes/event_' . $event_id . '.png';
}

// Generate QR code
$qrPath = generateEventQR($event_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event QR Code - <?= htmlspecialchars($event['name']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        .header p {
            opacity: 0.9;
        }
        .qr-container {
            padding: 30px;
            text-align: center;
        }
        .qr-image {
            width: 260px;
            height: 260px;
            margin: 0 auto 25px;
            border: 15px solid #f1f5fe;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(37, 117, 252, 0.2);
        }
        .event-info {
            padding: 20px;
            background: #f9fbfd;
            border-top: 1px solid #eef2f7;
        }
        .info-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e0e6ed;
        }
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border: none;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            width: 100px;
            flex-shrink: 0;
        }
        .info-content {
            flex-grow: 1;
            text-align: left;
        }
        .instructions {
            text-align: center;
            padding: 20px;
            color: #4a5568;
            font-size: 0.95rem;
        }
        .print-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px;
            background: #4caf50;
            color: white;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .print-button:hover {
            background: #388e3c;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
                background: white;
            }
            .container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($event['name']) ?></h1>
            <p>Event QR Code</p>
        </div>
        
        <div class="qr-container">
            <img src="<?= $qrPath ?>" alt="Event QR Code" class="qr-image">
            <p>Scan this QR code to view event details</p>
        </div>
        
        <div class="event-info">
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span class="info-content"><?= htmlspecialchars($event['date']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Time:</span>
                <span class="info-content"><?= htmlspecialchars($event['time']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Location:</span>
                <span class="info-content"><?= htmlspecialchars($event['location']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Description:</span>
                <span class="info-content"><?= nl2br(htmlspecialchars($event['description'])) ?></span>
            </div>
        </div>
        
        <div class="instructions">
            <p>Print this page and display the QR code at your event for attendees to scan</p>
            <a href="javascript:window.print()" class="print-button">Print QR Code</a>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
$stmt->close();
$conn->close();
?>