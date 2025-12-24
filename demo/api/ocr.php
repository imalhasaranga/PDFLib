<?php
require_once 'utils.php';
use ImalH\PDFLib\PDFLib;

try {
    $paths = handleUpload('pdf');
    $source = $paths[0];

    // Default language 'eng'. In a real app, this could be a dropdown.
    $language = $_POST['language'] ?? 'eng';

    list($jobDir, $jobId) = getJobDir();
    $outputFile = $jobDir . 'ocr_result.pdf';

    $pdfLib = new PDFLib();

    // Attempt OCR
    // This might take significantly longer than other operations
    set_time_limit(300); // 5 minutes

    try {
        $pdfLib->ocr($language, $outputFile, $source);
    } catch (Exception $e) {
        // Enhance the error message for the demo user
        $msg = $e->getMessage();
        if (strpos($msg, 'pdfocr8') !== false || strpos($msg, 'undefined') !== false) {
            throw new Exception("OCR Failed: Ghostscript 'pdfocr8' device invalid. Please install Tesseract and ensure Ghostscript is linked correctly. Original Error: " . $msg);
        }
        throw $e;
    }

    jsonResponse([
        'success' => true,
        'pdf_url' => outputUrl($jobId, $outputFile)
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
