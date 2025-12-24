<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    // Expecting 'pdf[]' upload (files are images)
    // We reuse handleUpload logic, it works for images too if we pass the key 'pdf'
    // Frontend sends 'pdf[]' which PHP maps to $_FILES['pdf']
    $paths = handleUpload('pdf');

    if (count($paths) < 1) {
        throw new Exception("Please upload at least 1 image.");
    }

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'images.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->makePDF($outputFile, $paths);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
