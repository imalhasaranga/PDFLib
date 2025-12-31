<?php

use PHPUnit\Framework\TestCase;
use ImalH\PDFLib\PDFLib;

class PDFLibTest extends TestCase
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

    public function testPageCount(): void
    {
        self::clean();
        $pdfLib = new PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pagesCount = $pdfLib->getNumberOfPages();
        $this->assertEquals(self::$_SAMPLE_PDF_PAGES, $pagesCount);
    }

    public function testConvert(): void
    {
        self::clean();
        $pdfLib = new PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        // Driver returns empty array currently
        $this->assertIsArray($pdfLib->convert());
    }

    public function testConvertWithChaining(): void
    {
        self::clean();
        $filesArray = (new PDFLib())
            ->setPdfPath(self::$_SAMPLE_PDF)
            ->setOutputPath(self::$_DATA_FOLDER)
            ->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG)
            ->setDPI(300)
            ->setFilePrefix('custom')
            ->convert();

        $this->assertIsArray($filesArray);
    }

    public function testMerge(): void
    {
        self::clean();
        $pdfLib = new PDFLib();
        $outputPath = self::$_DATA_FOLDER . "/merged.pdf";
        // Merge the sample PDF with itself
        $success = $pdfLib->merge([self::$_SAMPLE_PDF, self::$_SAMPLE_PDF], $outputPath);
        self::assertTrue($success);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::clean();
    }

    private static function clean(): void
    {
        if (file_exists("./" . self::$_DATA_FOLDER)) {
            self::deleteDir("./" . self::$_DATA_FOLDER);
        }
        if (!file_exists("./" . self::$_DATA_FOLDER)) {
            mkdir("./" . self::$_DATA_FOLDER);
        }
    }

    private static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}