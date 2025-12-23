<?php

class PDFLibAdvancedTest extends PHPUnit_Framework_TestCase
{
    private static $_DATA_FOLDER = "tests/data";
    private static $_SAMPLE_PDF = "tests/resources/sample.pdf";
    private static $_SAMPLE_PDF_PAGES = 5;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::clean();
    }

    public function testSplit()
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
    }

    public function testSplitRange()
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/split_range.pdf";

        // Split pages 1-2
        $pdfLib->split("1-2", $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));

        $pdfLibCheck = new \ImalH\PDFLib\PDFLib();
        $pdfLibCheck->setPdfPath($outputPath);
        self::assertEquals(2, $pdfLibCheck->getNumberOfPages());
    }

    public function testEncrypt()
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/encrypted.pdf";
        $password = "secret";

        $pdfLib->encrypt($password, $password, $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));

        // Try to get page count (should fail or return error if password needed and not provided)
        // Note: standard getNumberOfPages might fail or return 0 for encrypted files if -sPDFPassword is not sent.
        // For now, just checking file existence and maybe that it's different from original
        self::assertNotEquals(filesize(self::$_SAMPLE_PDF), filesize($outputPath));
    }

    public function testThumbnail()
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

    public function testWatermark()
    {
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/watermarked.pdf";

        $pdfLib->addWatermarkText("CONFIDENTIAL", $outputPath, self::$_SAMPLE_PDF);

        self::assertTrue(file_exists($outputPath));
        $pdfLibCheck = new \ImalH\PDFLib\PDFLib();
        $pdfLibCheck->setPdfPath($outputPath);
        self::assertEquals(self::$_SAMPLE_PDF_PAGES, $pdfLibCheck->getNumberOfPages());
    }

    private static function clean()
    {
        if (!file_exists("./" . self::$_DATA_FOLDER)) {
            mkdir("./" . self::$_DATA_FOLDER);
        }
    }
}
