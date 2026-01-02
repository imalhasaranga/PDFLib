<?php

namespace Tests;

use ImalH\PDFLib\PDF;
use PHPUnit\Framework\TestCase;

class StatefulChainingTest extends TestCase
{
    protected string $sourcePdf;
    protected string $outputPdf;

    protected function setUp(): void
    {
        $this->sourcePdf = __DIR__ . '/simple.pdf';
        $this->outputPdf = __DIR__ . '/chained_output.pdf';

        // Create a simple PDF using TCPDF if not exists
        if (!file_exists($this->sourcePdf)) {
            $pdf = new \TCPDF();
            $pdf->AddPage();
            $pdf->Write(1, 'Hello World');
            $pdf->Output($this->sourcePdf, 'F');
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->sourcePdf)) {
            unlink($this->sourcePdf);
        }
        if (file_exists($this->outputPdf)) {
            unlink($this->outputPdf);
        }
    }

    public function test_stateful_chaining_execution()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping on Windows for now due to path complexity in test setup');
        }

        // 1. Init
        // 2. Rotate 90
        // 3. Watermark
        // 4. Save

        $result = PDF::init()
            ->driver(PDF::DRIVER_GHOSTSCRIPT)
            ->from($this->sourcePdf)
            ->rotate(90)
            ->watermark('CONFIDENTIAL')
            ->save($this->outputPdf);

        $this->assertTrue($result, 'Chain execution failed');
        $this->assertFileExists($this->outputPdf);
        $this->assertGreaterThan(0, filesize($this->outputPdf));
    }

    public function test_legacy_execution_still_works()
    {
        // rotate with destination should execute immediately
        $result = PDF::init()
            ->driver(PDF::DRIVER_GHOSTSCRIPT)
            ->from($this->sourcePdf)
            ->rotate(90, $this->outputPdf);

        $this->assertTrue($result);
        $this->assertFileExists($this->outputPdf);
    }
}
