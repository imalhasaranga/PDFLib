<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

use ImalH\PDFLib\PDFLib;

$uploadDir = __DIR__ . '/uploads/';
$outputDir = __DIR__ . '/output/';

if (!file_exists($uploadDir))
    mkdir($uploadDir, 0777, true);
if (!file_exists($outputDir))
    mkdir($outputDir, 0777, true);

function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    if (!isset($_FILES['pdf'])) {
        throw new Exception('No PDF file uploaded');
    }

    $file = $_FILES['pdf'];
    $filename = uniqid() . '_' . basename($file['name']);
    $inputPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $inputPath)) {
        throw new Exception('Failed to upload file');
    }

    $pdfLib = new PDFLib();
    $pdfLib->setPdfPath($inputPath);

    // Create a specific folder for this conversion
    $jobId = uniqid();
    $jobOutputDir = $outputDir . $jobId;
    if (!file_exists($jobOutputDir))
        mkdir($jobOutputDir, 0777, true);

    $pdfLib->setOutputPath($jobOutputDir);
    $pdfLib->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG);
    $pdfLib->setDPI(150);
    $pdfLib->convert();

    // Scan for generated images
    $images = glob($jobOutputDir . '/*.png');
    $imageUrls = [];
    foreach ($images as $img) {
        $imageUrls[] = 'http://localhost:8000/api/output/' . $jobId . '/' . basename($img);
    }

    jsonResponse(['success' => true, 'images' => $imageUrls]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
