<?php

namespace ImalH\PDFLib\Drivers;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Exceptions\NotSupportedException;
use Symfony\Component\Process\Process;

class ChromeHeadlessDriver implements DriverInterface
{
    protected string $chromeBin;
    protected string $source;
    protected string $output;

    protected array $config = [
        'page_size' => 'A4',
        'margin_top' => '0',
        'margin_right' => '0',
        'margin_bottom' => '0',
        'margin_left' => '0',
        'scale' => 1.0,
    ];

    public function __construct(string $chromeBin = 'google-chrome')
    {
        $this->chromeBin = $chromeBin;
    }

    public function setSource(string $path): self
    {
        $this->source = $path;
        return $this;
    }

    public function setOutput(string $path): self
    {
        $this->output = $path;
        return $this;
    }

    public function setOption(string $key, mixed $value): self
    {
        $this->config[$key] = $value;
        return $this;
    }

    public function convertFromHtml(string $source, string $destination): bool
    {
        // Detect OS and adjust flags if necessary. 
        // Chrome arguments:
        // --headless
        // --disable-gpu
        // --print-to-pdf=output.pdf
        // --no-pdf-header-footer (optional)
        // input_url_or_file

        $command = [
            $this->chromeBin,
            '--headless',
            '--disable-gpu',
            '--print-to-pdf=' . $destination,
            '--no-pdf-header-footer', // Remove default header/footer
            $source
        ];

        $process = new Process($command);
        $process->setTimeout(60);
        $process->mustRun();

        return file_exists($destination);
    }

    // --- Not Supported Methods ---

    public function convert(): array
    {
        throw new NotSupportedException("ChromeDriver does not support PDF-to-Image conversion.");
    }

    public function merge(array $files, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support merging.");
    }

    public function compress(string $source, string $destination, string $level = 'screen'): bool
    {
        throw new NotSupportedException("ChromeDriver does not support compression.");
    }

    public function split($page, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support splitting PDFs.");
    }

    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool
    {
        throw new NotSupportedException("ChromeDriver does not support digital signatures. Use OpenSslDriver.");
    }

    public function fillForm(array $data, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support form filling.");
    }

    public function getFormFields(string $source): array
    {
        throw new NotSupportedException("ChromeDriver does not support form inspection.");
    }


    public function encrypt(string $userPassword, string $ownerPassword, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support encryption.");
    }

    public function watermark(string $text, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support watermarking.");
    }

    public function thumbnail(string $destination, int $width): bool
    {
        throw new NotSupportedException("ChromeDriver does not support thumbnail generation.");
    }

    public function setMetadata(array $metadata, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support metadata editing.");
    }

    public function rotate(int $degrees, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support rotation.");
    }

    public function flatten(string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support flattening.");
    }

    public function makePDF(array $images, string $destination): bool
    {
        throw new NotSupportedException("ChromeDriver does not support Image-to-PDF merging.");
    }

    public function getNumberOfPages(string $source): int
    {
        throw new NotSupportedException("ChromeDriver does not support page counting.");
    }

    public function validate(string $source): bool
    {
        throw new NotSupportedException("ChromeHeadlessDriver does not support signature validation.");
    }

    public function ocr(string $destination): bool
    {
        throw new NotSupportedException("ChromeHeadlessDriver does not support OCR.");
    }

    public function redact(string $text, string $destination): bool
    {
        throw new NotSupportedException("ChromeHeadlessDriver does not support redaction.");
    }

    public function getMetadata(string $source): array
    {
        throw new NotSupportedException("ChromeHeadlessDriver does not support metadata extraction.");
    }
}
