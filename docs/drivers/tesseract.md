# Tesseract Driver

The **TesseractDriver** enables **OCR (Optical Character Recognition)** capabilities, allowing you to extract text from scanned PDFs and Images.

## Requirements
*   **Tesseract OCR** binary installed on system (`v4.0+` recommended).
    *   **Ubuntu**: `sudo apt install tesseract-ocr`
    *   **macOS**: `brew install tesseract`
    *   **Windows**: Download installer or use Chocolatey.
    *   **Ghostscript**: Required for converting PDF pages to images prior to OCR.

## Usage

```php
use ImalH\PDFLib\PDF;

// Initialize
$pdf = PDF::init()->driver(PDF::DRIVER_TESSERACT);

// Extract Text
$success = $pdf->from('scanned_receipt.pdf')
               ->ocr('extracted_text.txt');

if ($success) {
    echo file_get_contents('extracted_text.txt');
}
```

## How it Works
1.  **PDF Input**: If input is a PDF, the driver uses `Ghostscript` to convert the first page to a high-resolution JPEG (300 DPI).
2.  **Image Input**: If input is already an image (JPG/PNG), it skips conversion.
3.  **OCR Execution**: Runs `tesseract input_image output_base`.
4.  **Cleanup**: Deletes temporary images.

## Methods
*   `ocr(string $destination)`: Main method. Destination should be a path (e.g., `output.txt`). Note that Tesseract often appends `.txt` automatically; the driver handles renaming this to your desired destination.
