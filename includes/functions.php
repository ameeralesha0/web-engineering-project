<?php
require_once __DIR__ . '/../config/db.php';

function getAllEvents() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEventById($event_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllAttendance() {
    global $conn;
    $stmt = $conn->query("
        SELECT a.*, e.event_name, e.event_date, e.event_time 
        FROM attendance a
        JOIN events e ON a.event_id = e.event_id
        ORDER BY a.check_in DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAttendanceById($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT a.*, e.event_name, e.event_date, e.event_time 
        FROM attendance a
        JOIN events e ON a.event_id = e.event_id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createAttendance($data) {
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO attendance 
        (event_id, student_id, student_name, check_in, status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['event_id'],
        $data['student_id'],
        $data['student_name'],
        $data['check_in'],
        $data['status']
    ]);
}

function updateAttendance($id, $data) {
    global $conn;
    $stmt = $conn->prepare("
        UPDATE attendance SET
        event_id = ?,
        student_id = ?,
        student_name = ?,
        check_in = ?,
        status = ?
        WHERE id = ?
    ");
    return $stmt->execute([
        $data['event_id'],
        $data['student_id'],
        $data['student_name'],
        $data['check_in'],
        $data['status'],
        $id
    ]);
}

function deleteAttendance($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
    return $stmt->execute([$id]);
}

function generateQRCode($text) {
    require_once __DIR__ . '/../vendor/autoload.php';

    $qrCode = new \Endroid\QrCode\QrCode($text); // v1/v2 style

    // Set size in pixels if available
    $qrCode->setEncoding('UTF-8');
    $qrCode->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::HIGH);
    $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
    $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);

    $qrCodePath = __DIR__ . '/../assets/qrcodes/' . uniqid() . '.png';

    if (!file_exists(dirname($qrCodePath))) {
        mkdir(dirname($qrCodePath), 0777, true);
    }

    $qrCode->writeFile($qrCodePath);

    return 'assets/qrcodes/' . basename($qrCodePath);
}
