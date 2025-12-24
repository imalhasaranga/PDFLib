<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'archive.pdf';

    $pdfLib = new PDFLib();
    // Wrap in try-catch as PDF/A can fail without proper ICC profiles
    try {
        $pdfLib->convertToPDFA($outputFile, $source);
    } catch (Exception $e) {
        // If strict PDF/A fails, we might return error or existing PDF.
        // For demo, let's bubble the error so user knows GS failed.
        throw new Exception("PDF/A Conversion Failed: " . $e->getMessage());
    }

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
