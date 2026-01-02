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

function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function handleUpload($inputName = 'pdf')
{
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir))
        mkdir($uploadDir, 0777, true);

    if (!isset($_FILES[$inputName])) {
        throw new Exception("No file uploaded for key: $inputName");
    }

    $files = $_FILES[$inputName];
    $uploadedPaths = [];

    // Handle array of files (e.g. for merge)
    if (is_array($files['name'])) {
        foreach ($files['name'] as $key => $name) {
            $path = $uploadDir . uniqid() . '_' . basename($name);
            if (!move_uploaded_file($files['tmp_name'][$key], $path)) {
                throw new Exception("Failed to upload file: $name");
            }
            $uploadedPaths[] = $path;
        }
    } else {
        $path = $uploadDir . uniqid() . '_' . basename($files['name']);
        if (!move_uploaded_file($files['tmp_name'], $path)) {
            throw new Exception("Failed to upload file");
        }
        $uploadedPaths = [$path];
    }

    return $uploadedPaths;
}

function getOutputDir()
{
    $outputDir = __DIR__ . '/output/';
    if (!file_exists($outputDir))
        mkdir($outputDir, 0777, true);
    return $outputDir;
}

function getJobDir()
{
    $jobId = uniqid();
    $jobDir = getOutputDir() . $jobId . '/';
    if (!file_exists($jobDir))
        mkdir($jobDir, 0777, true);
    return [$jobDir, $jobId];
}

function outputUrl($jobId, $filename)
{
    return 'http://localhost:8000/api/output/' . $jobId . '/' . basename($filename);
}
