<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'flattened.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->flatten($outputFile, $source);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
