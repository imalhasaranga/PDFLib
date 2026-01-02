<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'thumbnail.jpg';

    $pdfLib = new PDFLib();
    $pdfLib->createThumbnail($outputFile, 200, $source);

    jsonResponse([
        'success' => true,
        'image_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
