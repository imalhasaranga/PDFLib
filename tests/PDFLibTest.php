<?php

/**
 * Created by PhpStorm.
 * User: imal365
 * Date: 8/19/17
 * Time: 1:56 PM
 */
class PDFLibTest extends PHPUnit_Framework_TestCase
{
    private static $_DATA_FOLDER = "tests/data";
    private static $_SAMPLE_PDF = "tests/resources/sample.pdf";
    private static $_SAMPLE_PDF_PAGES = 5;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::clean();
    }

    public function testPageCount()
    {
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pagesCount = $pdfLib->getNumberOfPages();
        self::assertTrue($pagesCount == self::$_SAMPLE_PDF_PAGES);
    }

    public function testConvertWithChaining(){
        self::clean();
        $filesArray = (new \ImalH\PDFLib\PDFLib())
            ->setPdfPath(self::$_SAMPLE_PDF)
            ->setOutputPath(self::$_DATA_FOLDER)
            ->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG)
            ->setDPI(300)
            ->setFilePrefix('custom')
            ->convert()
            ;
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER,"custom");
        self::assertSame($fileCount, self::$_SAMPLE_PDF_PAGES);
    }


    public function testConvertToPngWithCustomPrefix(){
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG);
        $pdfLib->setDPI(300);
        $pdfLib->setFilePrefix('custom');
        $filesArray = $pdfLib->convert();
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER,"custom");
        self::assertSame($fileCount, self::$_SAMPLE_PDF_PAGES);
    }

    public function testConvertToPng(){
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG);
        $pdfLib->setDPI(300);
        $filesArray = $pdfLib->convert();
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER,"page-");
        self::assertSame($fileCount, self::$_SAMPLE_PDF_PAGES);
    }

    public function testConvertToJPG(){
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_JPEG);
        $pdfLib->setDPI(300);
        $pdfLib->setImageQuality(95);
        $filesArray = $pdfLib->convert();
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER,"page-");
        self::assertTrue($fileCount == self::$_SAMPLE_PDF_PAGES);
    }

    public function testConversionOfRangePNG()
    {
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG);
        $pdfLib->setDPI(300);
        $pdfLib->setPageRange(2, 4);
        $filesArray = $pdfLib->convert();
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER, "page-");
        self::assertTrue($fileCount == count($filesArray));
    }

    public function testConversionOfRangeJPG()
    {
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_JPEG);
        $pdfLib->setDPI(300);
        $pdfLib->setImageQuality(95);
        $pdfLib->setPageRange(2, 4);
        $filesArray = $pdfLib->convert();
        $fileCount = self::countFilesNameStartsWith(self::$_DATA_FOLDER, "page-");
        self::assertTrue($fileCount == count($filesArray));
    }

    public function testImageToPdf(){
        self::clean();
        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath(self::$_SAMPLE_PDF);
        $pdfLib->setOutputPath(self::$_DATA_FOLDER);
        $pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_JPEG);
        $pdfLib->setDPI(300);
        $pdfLib->setImageQuality(95);
        $pdfLib->setPageRange(2, 4);
        $filesArray = $pdfLib->convert();
        $fullpaths = [];
        foreach ($filesArray as $item){
            $fullpaths[] = self::$_DATA_FOLDER."/".$item;
        }
        $pdfFileName = self::$_DATA_FOLDER."/from_images.pdf";
        if(file_exists($pdfFileName)){
            unlink($pdfFileName);
        }
        $pdfLib->makePDF($pdfFileName,$fullpaths);

        $pdfLib = new \ImalH\PDFLib\PDFLib();
        $pdfLib->setPdfPath($pdfFileName);
        self::assertTrue($pdfLib->getNumberOfPages() == count($fullpaths));
    }



    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        self::clean();
    }

    /*------------------------ helper functions below ----------------------------*/

    private static function clean()
    {
        if (file_exists("./" . self::$_DATA_FOLDER)) {
            self::deleteDir("./" . self::$_DATA_FOLDER);

        }
        mkdir("./" . self::$_DATA_FOLDER);
    }

    private static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
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

    private static function countFilesNameStartsWith($dir_path, $name)
    {
        $counter = 0;
        $dir = new DirectoryIterator($dir_path);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                if (strpos($fileinfo->getFilename(), $name) !== false) {
                    ++$counter;
                }
            }
        }
        return $counter;
    }

}