<?php

use PHPUnit\Framework\TestCase;
use ImalH\PDFLib\Drivers\ChromeHeadlessDriver;
use ImalH\PDFLib\PDF;

class ChromeDriverTest extends TestCase
{
    private static $_DATA_FOLDER = "tests/data";
    private static $_OUTPUT_FILE = "tests/data/chrome_test.pdf";
    // Adjust this if running in an environment without Chrome
    private static $_CHROME_BIN = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!file_exists(self::$_DATA_FOLDER)) {
            mkdir(self::$_DATA_FOLDER, 0777, true);
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        if (file_exists(self::$_OUTPUT_FILE)) {
            unlink(self::$_OUTPUT_FILE);
        }
    }

    public function testConvertFromHtml(): void
    {
        if (!file_exists(self::$_CHROME_BIN)) {
            $this->markTestSkipped('Chrome binary not found. Skipping Chrome tests.');
        }

        $html = "<html><body><h1>Test HTML to PDF</h1></body></html>";

        $driver = new ChromeHeadlessDriver(self::$_CHROME_BIN);
        $result = $driver->convertFromHtml($html, self::$_OUTPUT_FILE);

        $this->assertTrue($result, "Conversion returned false");
        $this->assertFileExists(self::$_OUTPUT_FILE);
        $this->assertGreaterThan(0, filesize(self::$_OUTPUT_FILE));
    }

    public function testFacadeIntegration(): void
    {
        if (!file_exists(self::$_CHROME_BIN)) {
            $this->markTestSkipped('Chrome binary not found. Skipping Chrome tests.');
        }

        $output = self::$_DATA_FOLDER . "/facade_chrome.pdf";

        // We need to inject the driver instance with the binary path
        $driver = new ChromeHeadlessDriver(self::$_CHROME_BIN);

        $pdf = new PDF($driver);
        $result = $pdf->convertFromHtml("<h1>Facade Test</h1>", $output);

        $this->assertTrue($result);
        $this->assertFileExists($output);
    }
}
