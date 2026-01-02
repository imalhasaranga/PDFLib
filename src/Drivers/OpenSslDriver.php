<?php

namespace ImalH\PDFLib\Drivers;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Exceptions\NotSupportedException;
use Symfony\Component\Process\Process;
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
     * Sign PDF using TCPDF
     */
    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool
    {
        if (!class_exists('TCPDF')) {
            throw new \RuntimeException("TCPDF is required. Run 'composer require tecnickcom/tcpdf'.");
        }

        // Create new TCPDF instance
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('PDFLib v3.0');
        $pdf->SetAuthor('OpenSslDriver');
        $pdf->SetTitle('Digitally Signed Document');

        // Configure Signature
        // TCPDF expects the certificate content, not just path in some versions, but standard usage is:
        // $pdf->setSignature($certificate, $privateKey, $password, '', 2, $info);

        $password = $options['password'] ?? '';
        $info = $options['info'] ?? [];

        // Check file access
        if (!file_exists($certificate))
            throw new \RuntimeException("Cert not found: $certificate");
        if (!file_exists($privateKey))
            throw new \RuntimeException("Key not found: $privateKey");

        // Read file contents as TCPDF often needs the actual data stream
        $certContent = 'file://' . realpath($certificate);
        $keyContent = 'file://' . realpath($privateKey);

        $pdf->setSignature($certContent, $keyContent, $password, '', 2, $info);

        // Import content from source? 
        // Without FPDI, we can't easily import existing PDF pages.
        // For this Phase 2b MVP, if we can't import, we'll add a visual note.
        // "Original Content Placeholder"
        // Real-world usage MUST require FPDI.

        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, 'This document has been digitally signed via PDFLib OpenSslDriver.');
        $pdf->Ln(10);
        $pdf->Write(0, 'Note: Import of existing PDF content requires FPDI (Phase 3).');

        // Visible Signature
        $pdf->setSignatureAppearance(180, 60, 15, 15);
        $pdf->addEmptySignatureAppearance(180, 80, 15, 15);

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
            // Check windows path or assume valid if in Path
            // 'where' command?
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
}
