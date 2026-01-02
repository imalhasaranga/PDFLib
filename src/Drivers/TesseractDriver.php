<?php

namespace ImalH\PDFLib\Drivers;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Exceptions\NotSupportedException;
use Symfony\Component\Process\Process;

class TesseractDriver implements DriverInterface
{
    protected string $source;
    protected string $output;
    protected string $bin;

    public function __construct(string $bin = 'tesseract')
    {
        $this->bin = $bin;
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
        return $this;
    }

    /**
     * OCR the PDF and save text to destination
     */
    public function ocr(string $destination): bool
    {
        // Tesseract appends .txt automatically usually.
        // usage: tesseract input.pdf output_base

        $outputBase = preg_replace('/\.txt$/i', '', $destination);
        $ocrSource = $this->source;
        $tempImage = null;

        // Check if source is PDF
        $ext = strtolower(pathinfo($this->source, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            // Use GhostscriptDriver to convert to image
            $tempImage = sys_get_temp_dir() . '/tess_temp_' . uniqid() . '.jpg';

            if (class_exists(\ImalH\PDFLib\Drivers\GhostscriptDriver::class)) {
                $gs = new \ImalH\PDFLib\Drivers\GhostscriptDriver();
                $gs->setSource($this->source);
                $gs->setOption('resolution', 300); // High res for OCR
                $gs->setOption('format', 'jpeg');
                $gs->setOutput(sys_get_temp_dir());

                // Use thumbnail to generate single high-res image of first page
                $gs->thumbnail($tempImage, 2480);

                if (file_exists($tempImage)) {
                    $ocrSource = $tempImage;
                }
            }
        }

        $command = [$this->bin, $ocrSource, $outputBase];

        $process = new Process($command);
        $process->run();

        // Cleanup temp image
        if ($tempImage && file_exists($tempImage)) {
            unlink($tempImage);
        }

        if (!$process->isSuccessful()) {
            // Exit code 127 (Linux) or 'not recognized' (Windows)
            $error = strtolower($process->getErrorOutput());
            if (
                $process->getExitCode() === 127 ||
                strpos($error, 'not found') !== false ||
                strpos($error, 'not recognized') !== false
            ) {
                throw new \RuntimeException("Tesseract not found.");
            }
            return false;
        }

        // Tesseract adds .txt
        $actualFile = $outputBase . '.txt';
        if (file_exists($actualFile)) {
            if ($actualFile !== $destination) {
                rename($actualFile, $destination);
            }
            return true;
        }

        return false;
    }

    // --- Stubs ---
    public function convert(): array
    {
        throw new NotSupportedException();
    }
    public function convertFromHtml(string $s, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function validate(string $s): bool
    {
        throw new NotSupportedException();
    }
    public function merge(array $f, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function compress(string $s, string $d, string $l = 'screen'): bool
    {
        throw new NotSupportedException();
    }
    public function split($p, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function encrypt(string $u, string $o, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function watermark(string $t, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function thumbnail(string $d, int $w): bool
    {
        throw new NotSupportedException();
    }
    public function setMetadata(array $m, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function rotate(int $d, string $dest): bool
    {
        throw new NotSupportedException();
    }
    public function flatten(string $d): bool
    {
        throw new NotSupportedException();
    }
    public function makePDF(array $i, string $d): bool
    {
        throw new NotSupportedException();
    }
    public function getNumberOfPages(string $s): int
    {
        throw new NotSupportedException();
    }
    public function sign(string $c, string $k, string $d, array $o = []): bool
    {
        throw new NotSupportedException();
    }
    public function fillForm(array $d, string $dest): bool
    {
        throw new NotSupportedException();
    }
    public function getFormFields(string $s): array
    {
        throw new NotSupportedException();
    }
    public function redact(string $text, string $destination): bool
    {
        throw new NotSupportedException();
    }
    public function getMetadata(string $source): array
    {
        throw new NotSupportedException();
    }
}
