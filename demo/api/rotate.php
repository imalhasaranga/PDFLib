<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    $degrees = intval($_POST['degrees'] ?? 90);

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'rotated.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->rotateAll($degrees, $outputFile, $source);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
