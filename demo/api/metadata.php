<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    $metadata = [
        'Title' => $_POST['title'] ?? 'Updated Title',
        'Author' => $_POST['author'] ?? 'Web User',
        'Creator' => 'PDFLib Demo'
    ];

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'metadata.pdf';

    $pdfLib = new PDFLib();
    $pdfLib->setMetadata($metadata, $outputFile, $source);

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
