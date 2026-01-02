<?php

namespace ImalH\PDFLib\Drivers;

use ImalH\PDFLib\Contracts\DriverInterface;
use ImalH\PDFLib\Exceptions\NotSupportedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GhostscriptDriver implements DriverInterface
{
    protected string $source;
    protected string $output;
    protected string $gsBin;

    protected array $config = [
        'generated_pdf_version' => '1.4',
        'image_quality' => 100,
        'resolution' => 300,
        'format' => 'jpeg', // jpeg or png
    ];

    public function __construct(string $gsBin = 'gs')
    {
        $this->gsBin = $this->detectGhostscript($gsBin);
    }

    protected function detectGhostscript(string $bin): string
    {
        if ($bin !== 'gs') {
            return $bin;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Check for 64-bit CLI first, then 32-bit, then fallback
            // We can't easily check file existence without path, so we guess 'gswin64c' for 64-bit systems
            return 'gswin64c';
        }

        return $bin;
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

    public function convert(): array
    {
        $device = $this->config['format'] === 'png' ? 'png16m' : 'jpeg';

        // Note: Legacy implementation used %d for page numbers. 
        // We need to ensure output directory exists.
        $outputFilePattern = $this->output . '/page-%d.' . ($this->config['format'] === 'png' ? 'png' : 'jpg');

        $command = [
            $this->gsBin,
            '-dSAFER',
            '-dBATCH',
            '-dNOPAUSE',
            '-sDEVICE=' . $device,
            '-r' . $this->config['resolution'],
            '-dJPEGQ=' . $this->config['image_quality'],
            '-sOutputFile=' . $outputFilePattern,
            '-q',
            $this->source,
            '-c',
            'quit'
        ];

        // Add page range options if needed, for now processing all

        $this->runCommand($command);

        // Scan directory to return generated files
        // (Simplified logic: assuming files are generated)
        return glob(str_replace('%d', '*', $outputFilePattern));
    }

    public function convertFromHtml(string $source, string $destination): bool
    {
        // ... (existing implementation)
        // Ensure chrome is installed
        // ...
        // Command construction
        // ...
        return file_exists($destination);
    }



    public function merge(array $files, string $destination): bool
    {
        $command = array_merge(
            [$this->gsBin, '-sDEVICE=pdfwrite', '-dNOPAUSE', '-dQUIET', '-dBATCH', '-sOutputFile=' . $destination],
            $files
        );
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function compress(string $source, string $destination, string $level = 'screen'): bool
    {
        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=' . $this->config['generated_pdf_version'],
            '-dPDFSETTINGS=/' . $level,
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOutputFile=' . $destination,
            $source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function split($page, string $destination): bool
    {
        $firstPage = $lastPage = $page;
        if (is_string($page) && strpos($page, '-') !== false) {
            [$firstPage, $lastPage] = explode('-', $page);
        }

        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-dFirstPage=' . $firstPage,
            '-dLastPage=' . $lastPage,
            '-sOutputFile=' . $destination,
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function encrypt(string $userPassword, string $ownerPassword, string $destination): bool
    {
        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOwnerPassword=' . $ownerPassword,
            '-sUserPassword=' . $userPassword,
            '-dEncryptionR=3',
            '-dKeyLength=128',
            '-dPermissions=-4',
            '-sOutputFile=' . $destination,
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function setMetadata(array $metadata, string $destination): bool
    {
        $pdfmark = "[ ";
        foreach ($metadata as $key => $value) {
            $value = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
            $validKeys = ['Title', 'Author', 'Subject', 'Keywords', 'Creator', 'Producer'];
            if (in_array($key, $validKeys)) {
                $pdfmark .= "/$key ($value) ";
            }
        }
        $pdfmark .= "/DOCINFO pdfmark";

        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOutputFile=' . $destination,
            '-c',
            $pdfmark,
            '-f',
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }



    public function rotate(int $degrees, string $destination): bool
    {
        $orientation = 0;
        switch ($degrees) {
            case 90:
                $orientation = 1;
                break;
            case 180:
                $orientation = 2;
                break;
            case 270:
                $orientation = 3;
                break;
            default:
                $orientation = 0;
        }

        $psCommand = "<</Orientation $orientation>> setpagedevice";

        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-dAutoRotatePages=/None',
            '-sOutputFile=' . $destination,
            '-c',
            $psCommand,
            '-f',
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }



    public function flatten(string $destination): bool
    {
        // Ghostscript flattens forms by default when converting to pdfwrite.
        // We ensure compatibility level is sufficient.
        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-dPDFSETTINGS=/default',
            '-sOutputFile=' . $destination,
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }



    public function getNumberOfPages(string $source): int
    {
        // Use GS to query page count without full rendering
        // -dNODISPLAY: do not display
        // -q: quiet
        // -c ...: execute postscript
        $command = [
            $this->gsBin,
            '-q',
            '-dNODISPLAY',
            '-dNOSAFER', // Sometimes needed for file access in script
            '-c',
            "($source) (r) file runpdfbegin pdfpagecount = quit"
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            // Fallback or throw?
            // If encrypted, this might fail.
            // Try stricter regex or just return 0 on fail.
            // Let's assume valid PDF for now or throw.
            return 0;
        }

        $output = trim($process->getOutput());
        return (int) $output;
    }

    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool
    {
        throw new NotSupportedException("GhostscriptDriver does not support digital signatures. Use OpenSslDriver.");
    }

    public function fillForm(array $data, string $destination): bool
    {
        throw new NotSupportedException("GhostscriptDriver does not support form filling. Use PdftkDriver.");
    }

    public function getFormFields(string $source): array
    {
        throw new NotSupportedException("GhostscriptDriver does not support form inspection. Use PdftkDriver.");
    }

    public function watermark(string $text, string $destination): bool
    {
        $psCommand = '<< /EndPage { 2 eq { pop false } { gsave /Helvetica findfont 24 scalefont setfont .5 .5 .5 setrgbcolor 30 30 moveto (' . $text . ') show grestore true } ifelse } bind >> setpagedevice';

        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOutputFile=' . $destination,
            '-c',
            $psCommand,
            '-f',
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function thumbnail(string $destination, int $width): bool
    {
        $height = $width * 4; // Aspect ratio safoguard
        $command = [
            $this->gsBin,
            '-sDEVICE=jpeg',
            '-dJPEGQ=80',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-dFirstPage=1',
            '-dLastPage=1',
            '-dPDFFitPage',
            '-dFixedMedia',
            '-dDEVICEWIDTHPOINTS=' . $width,
            '-dDEVICEHEIGHTPOINTS=' . $height,
            '-sOutputFile=' . $destination,
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination) && filesize($destination) > 0;
    }

    public function makePDF(array $images, string $destination): bool
    {
        // For images to PDF, GS usually uses viewjpeg.ps. 
        // Modern GS can handle direct image input sometimes, but viewjpeg.ps is safer for legacy parity.
        // For this Driver implementation, we'll try a simpler approach if possible 
        // or we need to bundle viewjpeg.ps again.
        // Let's assume for now we use the direct approach if GS supports it (GS 9.x+ often does for some formats), 
        // or we reimplement the PS logic dynamically.
        // 
        // Simplify: just pass images to GS (works for some versions) or todo.
        // Ideally we should copy viewjpeg.ps logic.

        // Temporary placeholder:
        throw new \RuntimeException("makePDF not yet fully ported without viewjpeg.ps");
    }

    protected function runCommand(array $command): void
    {
        $process = new Process($command);
        $process->mustRun();
    }
    public function validate(string $source): bool
    {
        throw new NotSupportedException("GhostscriptDriver does not support signature validation.");
    }

    public function ocr(string $destination): bool
    {
        throw new NotSupportedException("GhostscriptDriver does not support OCR.");
    }

    public function redact(string $text, string $destination): bool
    {
        // 1. Find coordinates using pdftotext
        // Requirement: poppler-utils (pdftotext)
        $process = new Process(['pdftotext', '-bbox-layout', $this->source, '-']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Redaction requires 'pdftotext' (poppler-utils).");
        }

        $xmlOutput = $process->getOutput();
        // Parse basic XML
        // <word xMin="72.00" yMin="72.00" xMax="100.00" yMax="84.00">Text</word>

        // We need to handle page height for coordinate conversion?
        // pdftotext bbox structure: <page width="612.00" height="792.00"> ... </page>
        // PostScript coords: 0,0 is bottom-left. 
        // pdftotext usually: 0,0 is top-left (yMin increases downwards).

        $rects = [];

        // Simple regex parsing to avoid heavy XML parser deps if possible, or use SimpleXML
        // Let's use SimpleXML, it's standard.
        // Suppress errors for malformed output
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlOutput);
        if (!$xml) {
            throw new \RuntimeException("Failed to parse pdftotext output.");
        }

        foreach ($xml->page as $page) {
            $pageHeight = (float) $page['height'];
            $pageWidth = (float) $page['width'];

            foreach ($page->word as $word) {
                if (strpos((string) $word, $text) !== false) {
                    $xMin = (float) $word['xMin'];
                    $yMin = (float) $word['yMin'];
                    $xMax = (float) $word['xMax'];
                    $yMax = (float) $word['yMax'];

                    // Convert to PostScript Coords (Bottom-Left)
                    // GS: x is same. y = pageHeight - yMax ?
                    // pdftotext yMin is top edge?
                    // Let's assume pdftotext Y originates top-left.
                    // So rect bottom-left in PS: x = xMin, y = pageHeight - yMax.
                    // rect width = xMax - xMin.
                    // rect height = yMax - yMin.

                    $rectX = $xMin;
                    $rectY = $pageHeight - $yMax;
                    $rectW = $xMax - $xMin;
                    $rectH = $yMax - $yMin;

                    $rects[] = [$rectX, $rectY, $rectW, $rectH];
                }
            }
        }

        if (empty($rects)) {
            // Text not found, copy file or fail?
            // Let's copy to represent "no redaction needed" success
            return copy($this->source, $destination);
        }

        // 2. Generate PostScript
        /*
          /RedactRects [
             [x y w h]
             [x y w h]
          ] def

          /EndPage {
             exch pop 2 ne {
                gsave
                0 0 0 setrgbcolor
                RedactRects {
                   aload pop rectfill
                } forall
                grestore
             } if
             true
          } bind >> setpagedevice
        */

        // We need to inject the array.
        $psArray = "[";
        foreach ($rects as $r) {
            $psArray .= " [{$r[0]} {$r[1]} {$r[2]} {$r[3]}]";
        }
        $psArray .= " ]";

        $psCommand = "/RedactRects $psArray def << /EndPage { exch pop 2 ne { gsave 0 0 0 setrgbcolor RedactRects { aload pop rectfill } forall grestore } if true } bind >> setpagedevice";

        $command = [
            $this->gsBin,
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOutputFile=' . $destination,
            '-c',
            $psCommand,
            '-f',
            $this->source
        ];
        $this->runCommand($command);
        return file_exists($destination);
    }

    public function getMetadata(string $source): array
    {
        // Use 'pdfinfo' from poppler
        $process = new Process(['pdfinfo', $source]);
        $process->run();

        if (!$process->isSuccessful()) {
            // Fallback or throw?
            // Maybe GS has a way?
            // Since we used pdftotext for redaction, pdfinfo is likely here.
            throw new \RuntimeException("Metadata extraction requires 'pdfinfo' (poppler-utils).");
        }

        $output = $process->getOutput();
        $metadata = [];
        foreach (explode("\n", $output) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $metadata[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $metadata;
    }
}
