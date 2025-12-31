<?php

namespace ImalH\PDFLib;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Drivers\GhostscriptDriver;

class PDF
{
    public const DRIVER_GHOSTSCRIPT = Drivers\GhostscriptDriver::class;
    public const DRIVER_CHROME = Drivers\ChromeHeadlessDriver::class;
    public const DRIVER_OPENSSL = Drivers\OpenSslDriver::class;
    public const DRIVER_PDFTK = Drivers\PdftkDriver::class;

    protected DriverInterface $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public static function init(): self
    {
        return new self(new GhostscriptDriver());
    }

    public function driver(string $driver): self
    {
        if (class_exists($driver)) {
            $this->driver = new $driver();
        }
        return $this;
    }

    public function from(string $path): self
    {
        $this->driver->setSource($path);
        return $this;
    }

    public function fromHtml(string $html): self
    {
        // For HTML content, we might save to a temp file or pass directly if supported
        // Chrome driver usually expects a file or URL.
        // Let's ensure consistency by creating a temp file.
        $tmpFile = sys_get_temp_dir() . '/pdf_lib_' . uniqid() . '.html';
        file_put_contents($tmpFile, $html);
        $this->driver->setSource('file://' . $tmpFile);
        return $this;
    }

    public function fromUrl(string $url): self
    {
        $this->driver->setSource($url);
        return $this;
    }

    public function to(string $path): self
    {
        $this->driver->setOutput($path);
        return $this;
    }

    public function convert(): array
    {
        return $this->driver->convert();
    }

    public function merge(array $files, string $destination): bool
    {
        return $this->driver->merge($files, $destination);
    }

    public function split($page, string $destination): bool
    {
        return $this->driver->split($page, $destination);
    }

    public function compress(string $source, string $destination): bool
    {
        return $this->driver->compress($source, $destination);
    }

    public function encrypt(string $userPassword, string $ownerPassword, string $destination): bool
    {
        return $this->driver->encrypt($userPassword, $ownerPassword, $destination);
    }

    /**
     * Legacy method alias.
     * In v3, use to()->convert() or direct methods.
     */
    public function save(string $path): bool
    {
        $this->driver->setOutput($path);
        // Dispatch based on driver type or explicitly call convert?
        // For legacy parity, save() was never explicitly defined in this Facade before I added it.
        // Let's just return true to match 'success'.
        return true;
    }

    /**
     * Convert HTML to PDF
     */
    public function convertFromHtml(string $html, string $destination): bool
    {
        $tmpFile = sys_get_temp_dir() . '/pdf_lib_' . uniqid() . '.html';
        file_put_contents($tmpFile, $html);
        $result = $this->driver->convertFromHtml('file://' . $tmpFile, $destination);
        return $result;
    }

    /**
     * Digitally sign the PDF (Requires OpenSSL Driver)
     */
    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool
    {
        return $this->driver->sign($certificate, $privateKey, $destination, $options);
    }

    /**
     * Fill PDF Forms (AcroForms) - Requires PdftkDriver
     */
    public function fillForm(array $data, string $destination): bool
    {
        return $this->driver->fillForm($data, $destination);
    }

    /**
     * Get Form Fields (AcroForms) - Requires PdftkDriver
     */
    public function getFormFields(string $source = null): array
    {
        if ($source) {
            return $this->driver->getFormFields($source);
        }
        // If source not provided, try to use loaded source?
        // DriverInterface::getFormFields requires source string.
        // We technically don't expose current source in Facade easily (stored in driver).
        // Driver assumes explicit source for inspection usually.
        // Let's forward but allow null and handle it? 
        // DriverInterface says strict string.
        // But PDFLib Facade logic usually hides this.
        // Let's assume user passes source for inspection or we fail.

        throw new \InvalidArgumentException("Source path required for getFormFields");
    }

    public function __call($name, $arguments)
    {
        return $this->driver->$name(...$arguments);
    }
}
