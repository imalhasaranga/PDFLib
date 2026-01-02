<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    // Expecting 'pdf[]' upload
    $paths = handleUpload('pdf');

    if (count($paths) < 2) {
        throw new Exception("Please upload at least 2 PDFs to merge.");
    }

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'merged.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->merge($paths, $outputFile);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
