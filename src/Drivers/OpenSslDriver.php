<?php

namespace ImalH\PDFLib\Drivers;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Exceptions\NotSupportedException;
use Symfony\Component\Process\Process;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;

/**
 * Driver for handling Digital Signatures via OpenSSL/TCPDF
 */
class OpenSslDriver implements DriverInterface
{
    protected string $source;
    protected string $output;
    protected array $config = [];

    // --- Core Interface Implementation ---

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

    /**
     * Sign PDF using TCPDF/FPDI
     */
    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool
    {
        if (!class_exists('setasign\Fpdi\Tcpdf\Fpdi')) {
            throw new \RuntimeException("FPDI is required. Run 'composer require setasign/fpdi'.");
        }

        if (!class_exists('TCPDF')) {
            throw new \RuntimeException("TCPDF is required. Run 'composer require tecnickcom/tcpdf'.");
        }

        // Create new FPDI instance
        $pdf = new Fpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('PDFLib v3.0');
        $pdf->SetAuthor('OpenSslDriver');
        $pdf->SetTitle('Digitally Signed Document');

        // Configure Signature
        $password = $options['password'] ?? '';
        $info = $options['info'] ?? [];

        // Check file access
        if (!file_exists($certificate))
            throw new \RuntimeException("Cert not found: $certificate");
        if (!file_exists($privateKey))
            throw new \RuntimeException("Key not found: $privateKey");

        // Read file contents
        $certContent = 'file://' . realpath($certificate);
        $keyContent = 'file://' . realpath($privateKey);

        $pdf->setSignature($certContent, $keyContent, $password, '', 2, $info);

        // Prepare Image Options
        $img = $options['image'] ?? null;
        $x = $options['x'] ?? 15;
        $y = $options['y'] ?? 15;
        $w = $options['w'] ?? 50;
        $h = $options['h'] ?? 15;
        $userPage = $options['page'] ?? null;

        // Import content from source using FPDI
        $pageCount = 0;
        if (file_exists($this->source)) {
            $pageCount = $pdf->setSourceFile($this->source);

            // Determine target page
            // If user provided page, use it. Default to last page.
            $targetPage = $userPage ?? $pageCount;
            if ($targetPage < 1)
                $targetPage = 1;

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Add page with original size/orientation
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Inject Signature if this is the target page
                if ($pageNo == $targetPage) {
                    if ($img && file_exists($img)) {
                        $pdf->Image($img, $x, $y, $w, $h);
                        $pdf->setSignatureAppearance($x, $y, $w, $h, $pageNo);
                    } else {
                        // Default appearance
                        $pdf->setSignatureAppearance(180, 60, 15, 15, $pageNo);
                    }
                }
            }
        } else {
            // Fallback if no source (create blank page)
            $pdf->AddPage();
            $pdf->Write(0, 'Digitally signed via PDFLib (No source content).');
            // Add signature to this single page
            if ($img && file_exists($img)) {
                $pdf->Image($img, $x, $y, $w, $h);
                $pdf->setSignatureAppearance($x, $y, $w, $h, 1);
            } else {
                $pdf->setSignatureAppearance(180, 60, 15, 15, 1);
            }
        }

        // Output
        $pdf->Output($destination, 'F');

        return file_exists($destination);
    }

    // --- Not Supported ---

    public function convert(): array
    {
        throw new NotSupportedException("OpenSslDriver does not support Image Conversion");
    }
    public function convertFromHtml(string $s, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support HTML Conversion");
    }
    public function merge(array $f, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support merging");
    }
    public function compress(string $s, string $d, string $l = 'screen'): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support compression");
    }
    public function split($p, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support splitting");
    }
    public function encrypt(string $u, string $o, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support encryption");
    }
    public function watermark(string $t, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support watermarking");
    }
    public function thumbnail(string $d, int $w): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support thumbnails");
    }
    public function setMetadata(array $m, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support metadata");
    }
    public function rotate(int $deg, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support rotation");
    }
    public function flatten(string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support flattening");
    }
    public function makePDF(array $i, string $d): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support makePDF");
    }
    public function getNumberOfPages(string $s): int
    {
        throw new NotSupportedException("OpenSslDriver does not support page counting");
    }
    public function fillForm(array $d, string $dest): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support form filling");
    }
    public function getFormFields(string $s): array
    {
        throw new NotSupportedException("OpenSslDriver does not support form inspection");
    }

    /**
     * Validate Digital Signature using pdfsig (Poppler)
     */
    public function validate(string $source): bool
    {
        // simplistic check for pdfsig binary
        // In a real app we'd inject this dependency or path
        $pdfsig = 'pdfsig';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Check if pdfsig is in path using 'where'
            $process = new Process(['where', 'pdfsig']);
            $process->run();
            if ($process->isSuccessful()) {
                $lines = explode("\n", trim($process->getOutput()));
                $pdfsig = trim($lines[0]);
            }
        }

        $process = new Process([$pdfsig, '-v']);
        $process->run();
        if (!$process->isSuccessful()) {
            // pdfsig not installed
            throw new \RuntimeException("Validation requires 'pdfsig' (poppler-utils) to be installed.");
        }

        $command = [$pdfsig, $source];
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            // Error running pdfsig on file
            return false;
        }

        $output = $process->getOutput();

        // Output format of pdfsig:
        // Digital Signature Info for: signed.pdf
        // Signature #1:
        //   - Signer Certificate Common Name: ...
        //   - Signer full Distinguished Name: ...
        //   - Signing Time: ...
        //   - Signing Hash Algorithm: ...
        //   - Signature Type: ...
        //   - Signed Ranges: ...
        //   - Total Document Signed: ...
        //   - Signature Validation: Signature is Valid.
        //   - Certificate Validation: ...

        // We look for "Signature is Valid."
        // And ensure at least one signature exists

        if (strpos($output, 'Signature is Valid.') !== false) {
            return true;
        }

        return false;
    }

    public function ocr(string $destination): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support OCR.");
    }

    public function redact(string $text, string $destination): bool
    {
        throw new NotSupportedException("OpenSslDriver does not support redaction.");
    }

    public function getMetadata(string $source): array
    {
        throw new NotSupportedException("OpenSslDriver does not support metadata extraction.");
    }

    public function getPageDimensions(string $source, int $page): array
    {
        if (!class_exists('setasign\Fpdi\Tcpdf\Fpdi')) {
            throw new \RuntimeException("FPDI is required. Run 'composer require setasign/fpdi'.");
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($source);

        if ($page > $pageCount || $page < 1) {
            throw new \InvalidArgumentException("Page $page not found. Document has $pageCount pages.");
        }

        $templateId = $pdf->importPage($page);
        $size = $pdf->getTemplateSize($templateId);

        return [
            'w' => $size['width'],
            'h' => $size['height']
        ];
    }
}
