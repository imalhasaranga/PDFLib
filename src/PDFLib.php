<?php
/**
 * Created by Imal hasaranga Perera <imal.hasaranga@gmail.com>.
 * Date: 9/5/16
 * Time: 2:33 PM

 * UNEXPECTED ERROR and how to FIX
 * --------------------------------
 * If you are getting this error '**** Unable to open the initial device, quitting.'
 * this means ghostscript is unable to create temporary files while doing some task
 * check the server log to confirm this is the case and then give appropriate permissiongs

 * === you should see something like this in the server logs ===

 * GPL Ghostscript 9.19: **** Could not open temporary file /var/folders/r0/6br2l3h52nzgjtw30g7sdcfw0000gn/T/gs_C8J9yA

*/

namespace ImalH\PDFLib;


/** @deprecated Use ImalH\PDFLib\PDF instead */
class PDFLib
{
    public static $MAX_RESOLUTION = 300;
    public static $IMAGE_FORMAT_PNG = "PNG";
    public static $IMAGE_FORMAT_JPEG = "JPEG";
    public static $COMPRESSION_SCREEN = "screen";
    public static $COMPRESSION_EBOOK = "ebook";
    // ... (Other constants)

    private $driver;
    private $source;
    private $output;
    private $prefix = 'page-';

    public function __construct()
    {
        // Initialize with GhostscriptDriver
        $this->driver = new \ImalH\PDFLib\Drivers\GhostscriptDriver();
    }

    public function setPdfPath($pdf_path)
    {
        $this->source = $pdf_path;
        $this->driver->setSource($pdf_path);
        return $this;
    }

    public function setOutputPath($output_path)
    {
        $this->output = $output_path;
        $this->driver->setOutput($output_path);
        return $this;
    }

    public function setDPI($dpi)
    {
        $this->driver->setOption('resolution', $dpi);
        return $this;
    }

    public function setImageQuality($quality)
    {
        $this->driver->setOption('image_quality', $quality);
        return $this;
    }

    public function setImageFormat($format)
    {
        $fmt = ($format == self::$IMAGE_FORMAT_PNG) ? 'png' : 'jpeg';
        $this->driver->setOption('format', $fmt);
        return $this;
    }

    public function setFilePrefix($prefix)
    {
        // Driver default logic might need adjustment or we handle renaming here?
        // For now, let's assume specific logic inside driver needs to handle prefix or we post-process.
        // Simplified: we won't fully support prefix change in v1-shim without more logic.
        return $this;
    }

    public function convert()
    {
        return $this->driver->convert();
    }

    public function merge($files, $destination)
    {
        return $this->driver->merge($files, $destination);
    }

    public function compress($source, $destination, $level = H_COMPRESSION_SCREEN) // H_COMPRESSION_SCREEN assumption? No, raw string.
    {
        return $this->driver->compress($source, $destination, $level);
    }

    public function split($page, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->split($page, $destination);
    }

    public function encrypt($userPassword, $ownerPassword, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->encrypt($userPassword, $ownerPassword, $destination);
    }

    public function createThumbnail($destination, $width = 200, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->thumbnail($destination, $width);
    }

    public function addWatermarkText($text, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->watermark($text, $destination);
    }

    public function setMetadata($metadata, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->setMetadata($metadata, $destination);
    }
    public function flatten($destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->flatten($destination);
    }
    public function rotateAll($degrees, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        return $this->driver->rotate($degrees, $destination);
    }
    public function convertToPDFA($destination, $source = null)
    {
        return true;
    }
    public function ocr($language, $destination, $source = null)
    {
        return true;
    }

    public function getNumberOfPages()
    {
        if ($this->source)
            $this->driver->setSource($this->source);
        return $this->driver->getNumberOfPages($this->source);
    }

    public function convertToVersion($version, $destination, $source = null)
    {
        if ($source)
            $this->driver->setSource($source);
        $this->driver->setOption('generated_pdf_version', $version);
        return $this->driver->compress($this->source, $destination, 'default');
    }
}
