<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    // Get range from POST, default to page 1
    $range = $_POST['range'] ?? '1';

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'split.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->split($range, $outputFile, $source);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
