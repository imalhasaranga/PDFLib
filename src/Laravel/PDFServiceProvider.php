<?php

namespace ImalH\PDFLib\Laravel;

use Illuminate\Support\ServiceProvider;
use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\GhostscriptDriver;
use ImalH\PDFLib\Drivers\PdftkDriver;
use ImalH\PDFLib\Drivers\OpenSslDriver;
use ImalH\PDFLib\Drivers\ChromeHeadlessDriver;
use ImalH\PDFLib\Drivers\TesseractDriver;

class PDFServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/pdflib.php',
            'pdflib'
        );

        $this->app->bind('pdflib', function ($app) {
            $config = $app['config']->get('pdflib');
            $driverClass = $this->resolveDriver($config['driver'] ?? 'ghostscript');

            // Instantiate driver with optional binary path from config
            // For now, drivers construct with default or passed string.
            // Ideally we pass config array or bin path.
            // Driver constructors: __construct(string $bin = 'default')

            $bin = $config['binaries'][$config['driver']] ?? null;

            $driver = new $driverClass($bin ?: $this->getDefaultBin($config['driver']));

            return new PDF($driver);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $dest = function_exists('config_path')
                ? config_path('pdflib.php')
                : $this->app->basePath('config/pdflib.php');

            $this->publishes([
                __DIR__ . '/../../config/pdflib.php' => $dest,
            ], 'pdflib-config');
        }
    }

    protected function resolveDriver(string $driverName): string
    {
        return match ($driverName) {
            'ghostscript' => GhostscriptDriver::class,
            'pdftk' => PdftkDriver::class,
            'openssl' => OpenSslDriver::class,
            'chrome' => ChromeHeadlessDriver::class,
            'tesseract' => TesseractDriver::class,
            default => GhostscriptDriver::class,
        };
    }

    protected function getDefaultBin($driver)
    {
        return match ($driver) {
            'ghostscript' => 'gs',
            'pdftk' => 'pdftk',
            'chrome' => 'google-chrome',
            'tesseract' => 'tesseract',
            default => null
        };
    }
}
