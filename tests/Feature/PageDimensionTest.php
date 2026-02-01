<?php

namespace Tests\Feature;

use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\OpenSslDriver;
use PHPUnit\Framework\TestCase;

class PageDimensionTest extends TestCase
{
    protected string $sourcePath;

    protected function setUp(): void
    {
        parent::setUp();
        // Generate a temporary source PDF
        $this->sourcePath = __DIR__ . '/../../temp_dimension_test.pdf';

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage('P', 'A4'); // 210 x 297 mm
        $pdf->Output($this->sourcePath, 'F');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->sourcePath)) {
            unlink($this->sourcePath);
        }
    }

    public function test_can_get_page_dimensions()
    {
        $pdf = new PDF(new OpenSslDriver());
        $pdf->from($this->sourcePath);

        $dimensions = $pdf->getPageDimensions(1);

        // TCPDF Default is often A4 (210 x 297 mm)
        $this->assertIsArray($dimensions, "Dimensions should be an array");
        $this->assertArrayHasKey('w', $dimensions, "Should have width");
        $this->assertArrayHasKey('h', $dimensions, "Should have height");

        // Allow small float variance
        $this->assertEqualsWithDelta(210.0, $dimensions['w'], 1.0, "Width mismatch (Expected A4)");
        $this->assertEqualsWithDelta(297.0, $dimensions['h'], 1.0, "Height mismatch (Expected A4)");

        fwrite(STDERR, "Dimensions: " . json_encode($dimensions) . "\n");
    }
}
