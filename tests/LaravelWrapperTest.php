<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class LaravelWrapperTest extends TestCase
{
    public function test_files_exist()
    {
        $this->assertFileExists(__DIR__ . '/../src/Laravel/PDFServiceProvider.php');
        $this->assertFileExists(__DIR__ . '/../src/Laravel/Facades/PDF.php');
        $this->assertFileExists(__DIR__ . '/../config/pdflib.php');
    }

    public function test_service_provider_instantiatable_if_laravel_present()
    {
        if (!class_exists('Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('Laravel Framework not installed (Illuminate\Support\ServiceProvider missing)');
        }

        $provider = new \ImalH\PDFLib\Laravel\PDFServiceProvider(null);
        $this->assertInstanceOf(\Illuminate\Support\ServiceProvider::class, $provider);
    }
}
