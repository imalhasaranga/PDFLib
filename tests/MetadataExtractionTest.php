<?php

namespace Tests;

use ImalH\PDFLib\PDF;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class MetadataExtractionTest extends TestCase
{
    protected string $sourcePdf;
    protected bool $hasPdfinfo = false;
    protected bool $hasPdftk = false;

    protected function setUp(): void
    {
        // Check for pdfinfo
        $p1 = new Process(['pdfinfo', '-v']);
        $p1->run();
        $this->hasPdfinfo = $p1->isSuccessful();

        // Check for pdftk
        $p2 = new Process(['pdftk', '--version']);
        $p2->run();
        $this->hasPdftk = $p2->isSuccessful();

        // Windows fallback check for pdftk if needed? 
        // We assume standard path or alias.

        $this->sourcePdf = __DIR__ . '/meta_test.pdf';

        if (class_exists('TCPDF')) {
            $pdf = new \TCPDF();
            $pdf->SetCreator('PDFLib Test Suite');
            $pdf->SetAuthor('Test Author');
            $pdf->SetTitle('Test Metadata Title');
            $pdf->SetSubject('Test Subject');
            $pdf->SetKeywords('test, metadata, pdf');

            $pdf->AddPage();
            $pdf->Write(10, 'Metadata Test Content');
            $pdf->Output($this->sourcePdf, 'F');
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->sourcePdf))
            @unlink($this->sourcePdf);
    }

    public function test_ghostscript_metadata_extraction()
    {
        if (!$this->hasPdfinfo) {
            // Note: GhostscriptDriver uses pdfinfo for metadata extraction implementation
            $this->markTestSkipped('pdfinfo (poppler-utils) not found');
        }

        $meta = PDF::init()
            ->driver(PDF::DRIVER_GHOSTSCRIPT)
            ->from($this->sourcePdf)
            ->getMetadata();

        $this->assertIsArray($meta);
        $this->assertArrayHasKey('Title', $meta);
        $this->assertEquals('Test Metadata Title', $meta['Title']);
        $this->assertArrayHasKey('Author', $meta);
        $this->assertEquals('Test Author', $meta['Author']);

        // pdfinfo output keys are often "Title", "Author" etc.
    }

    public function test_pdftk_metadata_extraction()
    {
        if (!$this->hasPdftk) {
            $this->markTestSkipped('pdftk not found');
        }

        $meta = PDF::init()
            ->driver(PDF::DRIVER_PDFTK)
            ->from($this->sourcePdf)
            ->getMetadata();

        $this->assertIsArray($meta);
        // pdftk keys might be different casing or structure?
        // We implemented parser to handle "InfoKey: Title" -> "Title"

        $this->assertArrayHasKey('Title', $meta);
        $this->assertEquals('Test Metadata Title', $meta['Title']);
        $this->assertArrayHasKey('Author', $meta);
        $this->assertEquals('Test Author', $meta['Author']);
    }
}
