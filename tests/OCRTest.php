<?php

namespace Tests;

use ImalH\PDFLib\PDF;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class OCRTest extends TestCase
{
    protected string $sourcePdf;
    protected string $outputTxt;
    protected bool $hasTesseract = false;

    protected function setUp(): void
    {
        // Check for tesseract
        $process = new Process(['tesseract', '--version']);
        $process->run();
        $this->hasTesseract = $process->isSuccessful();

        $this->sourcePdf = __DIR__ . '/ocr_test.pdf';
        $this->outputTxt = __DIR__ . '/ocr_output.txt'; // Tesseract appends .txt

        // Create a simple PDF (with text, though image-based PDF is better for real OCR test).
        if (!file_exists($this->sourcePdf)) {
            if (class_exists('TCPDF')) {
                $pdf = new \TCPDF();
                $pdf->AddPage();
                $pdf->SetFont('helvetica', '', 14);
                $pdf->Write(5, 'OCR Test Content');
                $pdf->Output($this->sourcePdf, 'F');
            }
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->sourcePdf))
            @unlink($this->sourcePdf);
        if (file_exists($this->outputTxt))
            @unlink($this->outputTxt);
        if (file_exists($this->outputTxt . '.txt'))
            @unlink($this->outputTxt . '.txt');
    }

    public function test_ocr_extraction()
    {
        if (!$this->hasTesseract) {
            $this->markTestSkipped('Tesseract not found');
        }

        $result = PDF::init()
            ->driver(PDF::DRIVER_TESSERACT)
            ->from($this->sourcePdf)
            ->ocr($this->outputTxt);

        $this->assertTrue($result, "OCR should return true on success");
        // Tesseract output might be named with .txt appended
        $expectedFile = $this->outputTxt;
        // Our driver tries to rename it back to exact destination if it differs

        $this->assertFileExists($expectedFile);
        $content = file_get_contents($expectedFile);
        $this->assertStringContainsString('OCR', $content);
    }

    public function test_throws_exception_if_tesseract_missing()
    {
        if ($this->hasTesseract) {
            $this->markTestSkipped('Tesseract IS found, cannot test missing exception');
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Tesseract not found');

        PDF::init()
            ->driver(PDF::DRIVER_TESSERACT)
            ->from($this->sourcePdf)
            ->ocr($this->outputTxt);
    }
}
