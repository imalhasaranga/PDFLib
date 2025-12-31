<?php

require_once __DIR__ . '/vendor/autoload.php';

use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\PdftkDriver;

echo "Testing PdftkDriver...\n";

// 1. Create a simple PDF Form using TCPDF
$templatePath = __DIR__ . '/tests/data/form_template.pdf';
$outputPath = __DIR__ . '/tests/data/form_filled.pdf';

if (!file_exists(dirname($templatePath)))
    mkdir(dirname($templatePath), 0777, true);

if (!class_exists('TCPDF')) {
    die("TCPDF not installed. Run composer require tecnickcom/tcpdf\n");
}

$pdf = new TCPDF();
$pdf->SetCreator('PDFLib Test');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Name:', 0, 1);
$pdf->TextField('full_name', 50, 10);
$pdf->Ln(20);
$pdf->Cell(0, 10, 'Date:', 0, 1);
$pdf->TextField('date', 50, 10);
$pdf->Output($templatePath, 'F');

echo "Created Template: $templatePath\n";

// 2. Test Inspection
try {
    echo "Inspecting Fields...\n";
    $fields = PDF::init()->driver(PDF::DRIVER_PDFTK)->getFormFields($templatePath);
    print_r($fields);
} catch (\Exception $e) {
    echo "Inspection Limit: " . $e->getMessage() . "\n";
}

// 3. Test Filling
try {
    echo "Filling Form...\n";
    PDF::init()
        ->driver(PDF::DRIVER_PDFTK)
        ->from($templatePath)
        ->fillForm([
            'full_name' => 'Imal Perera',
            'date' => '2025-01-01'
        ], $outputPath);

    if (file_exists($outputPath)) {
        echo "SUCCESS: Form filled at $outputPath\n";
    } else {
        echo "FAILURE: Output not created.\n";
    }

} catch (\Exception $e) {
    echo "Execution Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'pdftk') !== false) {
        echo "(Expected if pdftk is not installed)\n";
    }
}
