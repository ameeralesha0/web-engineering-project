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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - <?= htmlspecialchars($event['name']) ?></title>
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
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 35px 25px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        .event-details {
            padding: 30px;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .detail-section h2 {
            color: #2d3748;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .info-card h3 {
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .info-card p {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
        }
        .description-box {
            background: #f8fafc;
            padding: 25px;
            border-radius: 8px;
            line-height: 1.7;
            color: #4a5568;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .map-placeholder {
            height: 250px;
            background: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4a5568;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 0.9rem;
            border-top: 1px solid #e2e8f0;
        }
        @media (max-width: 600px) {
            .header {
                padding: 25px 15px;
            }
            .header h1 {
                font-size: 1.8rem;
            }
            .event-details {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($event['name']) ?></h1>
            <p>Event Details</p>
        </div>
        
        <div class="event-details">
            <div class="detail-section">
                <div class="info-grid">
                    <div class="info-card">
                        <h3>DATE</h3>
                        <p><?= htmlspecialchars($event['date']) ?></p>
                    </div>
                    <div class="info-card">
                        <h3>TIME</h3>
                        <p><?= htmlspecialchars($event['time']) ?></p>
                    </div>
                    <div class="info-card">
                        <h3>LOCATION</h3>
                        <p><?= htmlspecialchars($event['location']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2>Event Description</h2>
                <div class="description-box">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>
            </div>
            
            <div class="detail-section">
                <h2>Location Map</h2>
                <div class="map-placeholder">
                    Map of <?= htmlspecialchars($event['location']) ?> would be displayed here
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Scanned from event QR code | <?= date('Y') ?></p>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
$stmt->close();
$conn->close();
?>