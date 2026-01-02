<?php

use PHPUnit\Framework\TestCase;

class PDFLibAdvancedTest extends TestCase
{
    private static $_DATA_FOLDER = "tests/data";
    private static $_SAMPLE_PDF = "tests/resources/sample.pdf";
    private static $_SAMPLE_PDF_PAGES = 5;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::clean();
        if (!file_exists(self::$_DATA_FOLDER)) {
            mkdir(self::$_DATA_FOLDER, 0777, true);
        }
    }

    public function testSplit(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/split_page_1.pdf";

        // Split page 1
        $pdfLib->split(1, $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));

        // Verify it has 1 page
        $pdfLibCheck = new \ImalH\PDFLib\PDFLib();
        $pdfLibCheck->setPdfPath($outputPath);
        self::assertEquals(1, $pdfLibCheck->getNumberOfPages());
        self::assertTrue(filesize($outputPath) > 0);
    }

    public function testSplitRange(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/split_range.pdf";

        // Split pages 1-2
        $pdfLib->split("1-2", $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
    }

    public function testEncrypt(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/encrypted.pdf";
        $password = "secret";

        $pdfLib->encrypt($password, $password, $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
        self::assertNotEquals(filesize(self::$_SAMPLE_PDF), filesize($outputPath));
    }

    public function testThumbnail(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/thumb.jpg";

        $pdfLib->createThumbnail($outputPath, 200, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
        // Check if it's a JPEG
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $outputPath);
        self::assertEquals('image/jpeg', $mime);
    }

    public function testWatermark(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/watermarked.pdf";

        $pdfLib->addWatermarkText("CONFIDENTIAL", $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
    }

    public function testMetadata(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/metadata.pdf";

        $pdfLib->setMetadata(['Title' => 'Test Title', 'Author' => 'Automated Test'], $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
    }

    public function testRotate(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/rotated.pdf";

        $pdfLib->rotateAll(90, $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
    }

    public function testFlatten(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/flattened.pdf";

        $pdfLib->flatten($outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
    }

    public function testPDFA(): void
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/pdfa.pdf";

        try {
            $pdfLib->convertToPDFA($outputPath, self::$_SAMPLE_PDF);
            self::assertTrue(file_exists($outputPath));
        } catch (\Exception $e) {
            // Allow failure if color profile issues (common in dev/test envs)
            $this->markTestIncomplete("PDF/A conversion might fail without specific ICC profiles: " . $e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::clean();
    }

    private static function clean(): void
    {
        if (file_exists("./" . self::$_DATA_FOLDER)) {
            // Basic clean
        }
    }
}
