<?php

use PHPUnit\Framework\TestCase;
use ImalH\PDFLib\Drivers\PdftkDriver;

class PdftkDriverReproductionTest extends TestCase
{
    public function testParsePdftkFieldsMalformed()
    {
        // Reflection to access private method
        $driver = new PdftkDriver();
        $reflection = new ReflectionClass($driver);
        $method = $reflection->getMethod('parsePdftkFields');
        $method->setAccessible(true);

        // Malformed output missing '---' separator
        $mockOutput = "FieldType: Text
FieldName: first_name
FieldFlags: 0
FieldValue: 
FieldType: Text
FieldName: last_name
FieldFlags: 0
FieldValue: ";

        $fields = $method->invoke($driver, $mockOutput);

        // Debug output to see what is happening
        // print_r($fields);

        // We expect 2 fields, but due to bug we might get 1 or merged field
        $this->assertCount(2, $fields, "Should have parsed 2 distinct fields, but got " . count($fields));

        $this->assertEquals('first_name', $fields[0]['name']);
        $this->assertEquals('last_name', $fields[1]['name']);
    }
}
