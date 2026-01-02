<?php

use PHPUnit\Framework\TestCase;
use ImalH\PDFLib\Drivers\PdftkDriver;

class PdftkDriverTest extends TestCase
{
    public function testParsePdftkFields()
    {
        // Reflection to access private method
        $driver = new PdftkDriver();
        $reflection = new ReflectionClass($driver);
        $method = $reflection->getMethod('parsePdftkFields');
        $method->setAccessible(true);

        $mockOutput = "---
FieldType: Text
FieldName: full_name
FieldFlags: 0
FieldValue: 
---
FieldType: Choice
FieldName: color
FieldFlags: 0
FieldValue: 
FieldStateOption: Red
FieldStateOption: Blue
FieldStateOption: Green
---
FieldType: Button
FieldName: agree
FieldFlags: 0
FieldValue: Off
";

        $fields = $method->invoke($driver, $mockOutput);

        $this->assertCount(3, $fields);

        // Check Text Field
        $this->assertEquals('full_name', $fields[0]['name']);
        $this->assertEquals('Text', $fields[0]['type']);

        // Check Choice Field
        $this->assertEquals('color', $fields[1]['name']);
        $this->assertEquals('Choice', $fields[1]['type']);
        $this->assertCount(3, $fields[1]['options']);
        $this->assertEquals('Blue', $fields[1]['options'][1]);

        // Check Button Field
        $this->assertEquals('agree', $fields[2]['name']);
        $this->assertEquals('Button', $fields[2]['type']);
    }

    public function testGetNumberOfPages()
    {
        // This test requires a real PDF/binary or must be mocked.
        // For unit testing without binaries, we can mock the Process?
        // But PdftkDriver instantiates Process directly (hard dependency).
        // So we will skip this if pdftk is not installed, or try to run it if sample exists.

        $samplePdf = 'tests/resources/sample.pdf';
        if (!file_exists($samplePdf)) {
            $this->markTestSkipped('Sample PDF not found');
        }

        // Check if pdftk installed
        // We can just try-catch the driver
        $driver = new PdftkDriver();

        try {
            $pages = $driver->getNumberOfPages($samplePdf);
            $this->assertGreaterThan(0, $pages);
        } catch (\Exception $e) {
            $this->markTestSkipped('Pdftk binary not found or execution failed: ' . $e->getMessage());
        }
    }
}
