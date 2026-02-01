<?php

namespace Tests\Feature;

use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\OpenSslDriver;
use PHPUnit\Framework\TestCase;

class ImageSignatureTest extends TestCase
{
    protected string $outputPath;
    protected string $certPath;
    protected string $keyPath;
    protected string $imagePath;
    protected string $sourcePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->outputPath = __DIR__ . '/../../output_signed_with_image.pdf';
        $this->certPath = __DIR__ . '/../resources/test.crt';
        $this->keyPath = __DIR__ . '/../resources/test.key';
        $this->imagePath = __DIR__ . '/../resources/signature.png';

        // Generate a temporary source PDF using TCPDF
        $this->sourcePath = __DIR__ . '/../../temp_source.pdf';
        if (file_exists($this->sourcePath)) {
            unlink($this->sourcePath);
        }

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage('P', 'Letter'); // Force Letter size for test consistency
        $pdf->Write(0, 'Temporary Source PDF for Testing');
        $pdf->Output($this->sourcePath, 'F');

        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->sourcePath)) {
            unlink($this->sourcePath);
        }
    }

    public function test_can_sign_with_image()
    {
        // Ensure resources exist
        $this->assertFileExists($this->certPath, "Certificate missing");
        $this->assertFileExists($this->keyPath, "Key missing");
        $this->assertFileExists($this->imagePath, "Signature image missing");
        $this->assertFileExists($this->sourcePath, "Source PDF missing");

        $pdf = new PDF(new OpenSslDriver());
        $pdf->from($this->sourcePath); // Load first to get dimensions

        // Get Dimensions of Page 1
        $dims = $pdf->getPageDimensions(1);

        // Calculate Center
        $w = 60;
        $h = 30;
        $x = ($dims['w'] - $w) / 2;
        $y = ($dims['h'] - $h) / 2;

        $result = $pdf->to($this->outputPath)
            ->sign($this->certPath, $this->keyPath, $this->outputPath, [
                'password' => '',
                'image' => $this->imagePath,
                'x' => $x,
                'y' => $y,
                'w' => $w,
                'h' => $h,
                'page' => 1
            ]);

        $this->assertTrue($result, "Signing failed");
        $this->assertFileExists($this->outputPath, "Output PDF not created");

        // Basic size check to ensure content is there
        $this->assertGreaterThan(1000, filesize($this->outputPath));
    }
}
