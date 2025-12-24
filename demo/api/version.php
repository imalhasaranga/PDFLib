<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    $version = $_POST['version'] ?? '1.4';

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'converted_version.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->convertToVersion($version, $outputFile, $source);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
