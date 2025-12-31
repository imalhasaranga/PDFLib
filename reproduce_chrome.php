<?php

require_once __DIR__ . '/vendor/autoload.php';

use ImalH\PDFLib\PDF;

echo "Testing ChromeHeadlessDriver...\n";

$html = "<html><body><h1 style='color:red;'>Hello from Chrome Headless!</h1><p>Date: " . date('Y-m-d H:i:s') . "</p></body></html>";
$output = __DIR__ . '/chrome_test.pdf';
$chromeBin = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'; // Adjust if needed

try {
    // We need to inject the binary path. 
    // The Facade init() uses default constructor.
    // We need to pass the driver instance manually or rely on default 'google-chrome' being in PATH.
    // Let's manually instantiate for this test.

    $driver = new \ImalH\PDFLib\Drivers\ChromeHeadlessDriver($chromeBin);
    $pdf = new PDF($driver);

    $pdf->convertFromHtml($html, $output);

    if (file_exists($output)) {
        echo "SUCCESS: PDF created at $output (" . filesize($output) . " bytes)\n";
    } else {
        echo "FAILURE: File not created.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
