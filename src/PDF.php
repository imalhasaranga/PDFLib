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
    public const DRIVER_TESSERACT = Drivers\TesseractDriver::class;

    protected DriverInterface $driver;
    protected array $pipeline = [];
    protected array $tempFiles = [];
    protected string $originalSource = '';

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
        $this->originalSource = $path;
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

    public function rotate(int $degrees, string $destination = null): self|bool
    {
        if ($destination) {
            return $this->driver->rotate($degrees, $destination);
        }
        $this->pipeline[] = ['method' => 'rotate', 'args' => [$degrees]];
        return $this;
    }

    public function watermark(string $text, string $destination = null): self|bool
    {
        if ($destination) {
            return $this->driver->watermark($text, $destination);
        }
        $this->pipeline[] = ['method' => 'watermark', 'args' => [$text]];
        return $this;
    }

    public function encrypt(string $userPassword, string $ownerPassword, string $destination = null): self|bool
    {
        if ($destination) {
            return $this->driver->encrypt($userPassword, $ownerPassword, $destination);
        }
        $this->pipeline[] = ['method' => 'encrypt', 'args' => [$userPassword, $ownerPassword]];
        return $this;
    }

    public function flatten(string $destination = null): self|bool
    {
        if ($destination) {
            return $this->driver->flatten($destination);
        }
        $this->pipeline[] = ['method' => 'flatten', 'args' => []];
        return $this;
    }

    /**
     * Execute the pipeline and save to destination
     */
    public function save(string $path): bool
    {
        if (empty($this->pipeline)) {
            if (isset($this->driver)) {
                // Copy original source to destination if no ops
                if ($this->originalSource && file_exists($this->originalSource)) {
                    return copy($this->originalSource, $path);
                }
                return true;
            }
            return false;
        }

        $currentSource = $this->originalSource;

        foreach ($this->pipeline as $index => $job) {
            $isLast = $index === count($this->pipeline) - 1;
            $targetPath = $isLast ? $path : $this->createTempFile();

            // Set the source for the driver
            $this->driver->setSource($currentSource);

            // Execute the method
            $args = array_merge($job['args'], [$targetPath]);
            $method = $job['method'];

            if (!method_exists($this->driver, $method)) {
                throw new \BadMethodCallException("Driver does not support method: {$method}");
            }

            $result = $this->driver->$method(...$args);

            if (!$result) {
                $this->cleanup();
                return false;
            }

            // If this wasn't the last step, update source for next step
            if (!$isLast) {
                $currentSource = $targetPath;
                $this->tempFiles[] = $targetPath;
            }
        }

        $this->cleanup();
        return true;
    }

    protected function createTempFile(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pdflib_pipe_' . uniqid() . '.pdf';
    }

    protected function cleanup(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
        $this->pipeline = [];
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

    /**
     * Validate Digital Signature
     */
    public function validate(string $source): bool
    {
        return $this->driver->validate($source);
    }

    /**
     * Perform OCR
     */
    public function ocr(string $destination): bool
    {
        return $this->driver->ocr($destination);
    }

    /**
     * Redact Text
     */
    public function redact(string $text, string $destination): bool
    {
        return $this->driver->redact($text, $destination);
    }

    /**
     * Get Metadata
     */
    public function getMetadata(string $source = null): array
    {
        $target = $source ?? $this->originalSource;
        if (!$target && isset($this->driver)) {
            // Can't easily get source from driver if not exposed.
            // Assuming user provides it or we stored it.
            // We stored originalSource.
        }
        if (!$target) {
            throw new \InvalidArgumentException("Source file required for metadata.");
        }
        return $this->driver->getMetadata($target);
    }

    public function __call($name, $arguments)
    {
        return $this->driver->$name(...$arguments);
    }
}
