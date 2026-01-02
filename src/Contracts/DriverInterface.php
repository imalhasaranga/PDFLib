<?php

namespace ImalH\PDFLib\Contracts;

interface DriverInterface
{
    /**
     * Set the path to the PDF file to process
     */
    public function setSource(string $path): self;

    /**
     * Set the output path directory or file
     */
    public function setOutput(string $path): self;

    /**
     * Set configuration option (dpi, quality, etc)
     */
    public function setOption(string $key, mixed $value): self;

    /**
     * Merge multiple PDFs
     * @param string[] $files Array of file paths
     */
    public function merge(array $files, string $destination): bool;

    /**
     * Compress PDF
     */
    public function compress(string $source, string $destination, string $level = 'screen'): bool;

    /**
     * Digitally sign the PDF
     * @param string $certificate Path to .crt file
     * @param string $privateKey Path to .pem key file
     * @param array $options Optional params (password, info, etc)
     */
    public function sign(string $certificate, string $privateKey, string $destination, array $options = []): bool;

    /**
     * Split PDF to extract specific pages
     * @param string|int $page Page number or range (e.g., "1-5")
     */
    public function split($page, string $destination): bool;

    /**
     * Encrypt PDF
     */
    public function encrypt(string $userPassword, string $ownerPassword, string $destination): bool;

    /**
     * Add text watermark
     */
    public function watermark(string $text, string $destination): bool;

    /**
     * Generate thumbnail of first page
     */
    public function thumbnail(string $destination, int $width): bool;

    /**
     * Set PDF Metadata
     * @param array $metadata Key-value pairs
     */
    public function setMetadata(array $metadata, string $destination): bool;

    /**
     * Fill PDF Forms (AcroForms)
     * @param array $data Associative array of field names and values
     */
    public function fillForm(array $data, string $destination): bool;

    /**
     * Get list of form fields in the PDF
     * @return array List of field names
     */
    public function getFormFields(string $source): array;

    /**
     * Rotate all pages
     */
    public function rotate(int $degrees, string $destination): bool;

    /**
     * Flatten PDF Forms
     */
    public function flatten(string $destination): bool;

    /**
     * Get number of pages in PDF
     */
    public function getNumberOfPages(string $source): int;

    /**
     * Convert Images to PDF
     * @param string[] $images Array of image paths
     */
    public function makePDF(array $images, string $destination): bool;

    /**
     * Convert PDF to Images
     * @return string[] Array of generated image paths
     */
    public function convert(): array;

    /**
     * Convert HTML content or URL to PDF
     * @param string $source HTML content or URL
     * @param string $destination Output PDF path
     */
    public function convertFromHtml(string $source, string $destination): bool;

    /**
     * Validate PDF Digital Signature
     * @return bool True if valid, False if invalid
     */
    public function validate(string $source): bool;

    /**
     * Perform OCR and save text to destination
     */
    public function ocr(string $destination): bool;

    /**
     * Redact text from PDF (Draw black rectangle over text)
     */
    public function redact(string $text, string $destination): bool;

    /**
     * Get PDF Metadata
     */
    public function getMetadata(string $source): array;
}
