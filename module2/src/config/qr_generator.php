<?php
// This file contains the reusable QR code generation function
require_once __DIR__ . '/../../../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Generates a QR code for an event and returns the file path
 * 
 * @param int $event_id Event ID
 * @param string $event_name Event name (for filename)
 * @return string Relative path to the generated QR code image
 */
function generateEventQR($event_id, $event_name = 'event') {
    // Create QR code directory if not exists
    $qrDir = __DIR__ . '/qrcodes/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0755, true);
    }
    
    // Generate URL to event details page
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $domain = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    $event_url = "$protocol://$domain$path/event_details.php?event_id=$event_id";
    
    // Clean filename
    $clean_name = preg_replace('/[^a-z0-9]+/', '-', strtolower($event_name));
    $clean_name = substr($clean_name, 0, 30);
    $qrFilename = $qrDir . $clean_name . '_' . $event_id . '.png';

    // Generate QR code
    $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data($event_url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(400)
        ->margin(15)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();

    $qrCode->saveToFile($qrFilename);
    
    return 'qrcodes/' . $clean_name . '_' . $event_id . '.png';
}
?>