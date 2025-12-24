# PDFlib 2.0.1

![Issues](https://img.shields.io/github/issues/imalhasaranga/PDFLib.svg)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
![Forks](https://img.shields.io/github/forks/imalhasaranga/PDFLib.svg)

A robust PHP wrapper for Ghostscript, designed to make PDF manipulation easy. 
Convert PDFs to images, create PDFs from images, optimize file sizes, and merge documents with simple, fluent APIs.

This project is an initiative of [Treinetic (Pvt) Ltd](http://www.treinetic.com), Sri Lanka.

## Feature Highlights

| Basic Operations | Manipulation (v1.5) | Advanced (v2.0) |
| :--- | :--- | :--- |
| [PDF to Images](#1-convert-pdf-to-images) | [Split PDF](#5-split-pdf-new-in-v15) | [OCR (Text Recognition)](#14-ocr-optical-character-recognition-new-in-v20) |
| [Images to PDF](#2-create-pdf-from-images) | [Encrypt / Protect](#6-encrypt-pdf-new-in-v15) | [PDF/A Archiving](#13-pdfa-conversion-new-in-v20) |
| [Compress / Optimize](#3-compress--optimize-pdf-new-in-v14) | [Watermark](#7-watermarking-new-in-v15) | [Page Rotation](#11-page-rotation-new-in-v20) |
| [Merge PDFs](#4-merge-pdfs-new-in-v14) | [Thumbnail](#8-thumbnail-new-in-v15) | [Metadata Editing](#10-metadata-management-new-in-v20) |
| | [Version Convert](#9-version-conversion-new-in-v15) | [Form Flattening](#12-form-flattening-new-in-v20) |

## Requirements

*   **PHP** >= 5.5.0
*   **Ghostscript** >= 9.16 installed and configured on your system.

### Installing Ghostscript

**Ubuntu / Debian**
```bash
sudo apt-get update
sudo apt-get install ghostscript
```

**MacOS (Homebrew)**
```bash
brew install ghostscript
```

**Windows**
1.  Download the **Ghostscript AGPL Release** installer from the [official website](https://www.ghostscript.com/download/gsdnld.html).
2.  Run the installer.
3.  **Important**: Add the `bin` and `lib` directories of your Ghostscript installation (e.g., `C:\Program Files\gs\gs9.54.0\bin`) to your system's `PATH` environment variable.
4.  Verify by running `gswin64c -v` in Command Prompt.

## Installation

Install via Composer:

```bash
composer require imal-h/pdf-box
```

## Quick Start
Convert a PDF page to an image in just a few lines:

```php
use ImalH\PDFLib\PDFLib;

$pdfLib = new PDFLib();
$pdfLib->setPdfPath("document.pdf")
       ->setOutputPath("output_folder")
       ->convert();
```

## Features

### 1. Convert PDF to Images
Convert specific pages or the entire document to PNG or JPEG.

```php
$pdfLib = new PDFLib();
$pdfLib->setPdfPath('my_document.pdf')
       ->setOutputPath('output_images')
       ->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG) // or $IMAGE_FORMAT_JPEG
       ->setDPI(300)
       ->setPageRange(1, 5) // Optional: convert only pages 1-5
       ->convert();
```

### 2. Create PDF from Images
Combine a list of images into a single PDF file.

```php
$pdfLib = new PDFLib();
$images = ['page1.jpg', 'page2.jpg', 'page3.jpg'];
$pdfLib->makePDF('combined_output.pdf', $images);
```

### 3. Compress / Optimize PDF (New in v1.4)
Reduce file size using Ghostscript's optimization presets.

```php
$pdfLib = new PDFLib();
// Levels: screen (72dpi), ebook (150dpi), printer (300dpi), prepress (300dpi, color)
$pdfLib->compress('large.pdf', 'optimized.pdf', PDFLib::$COMPRESSION_EBOOK);
```

### 4. Merge PDFs (New in v1.4)
Combine multiple PDF documents into one.

```php
$pdfLib = new PDFLib();
$files = ['part1.pdf', 'part2.pdf'];
$pdfLib->merge($files, 'merged_complete.pdf');
```

### 5. Split PDF (New in v1.5)
Extract a specific page or a range of pages.

```php
$pdfLib = new PDFLib();

// Extract just Page 1
$pdfLib->split(1, 'page_one.pdf', 'source.pdf');

// Extract Pages 1 to 5
$pdfLib->split('1-5', 'chapter_one.pdf', 'source.pdf');
```

### 6. Encrypt PDF (New in v1.5)
Protect your PDF with passwords and disable printing/copying.

```php
$pdfLib = new PDFLib();
// args: user_password, owner_password, output_path, input_path
$pdfLib->encrypt('open123', 'admin123', 'protected_doc.pdf', 'source.pdf');
```

### 7. Watermarking (New in v1.5)
Add a text watermark to every page (overlay).

```php
$pdfLib = new PDFLib();
$pdfLib->addWatermarkText('CONFIDENTIAL', 'watermarked.pdf', 'source.pdf');
```

### 8. Thumbnail (New in v1.5)
Generate a thumbnail image (JPEG) of the first page.

```php
$pdfLib = new PDFLib();
// args: output_image, width (approx), input_pdf
$pdfLib->createThumbnail('thumbnail.jpg', 200, 'source.pdf');
```

### 9. Version Conversion (New in v1.5)
Convert a PDF to a specific PDF version (e.g., 1.4 for compatibility).

```php
$pdfLib = new PDFLib();
$pdfLib->convertToVersion('1.4', 'compatible.pdf', 'source.pdf');
```

### 10. Metadata Management (New in v2.0)
Set PDF properties like Title, Author, etc.

```php
$pdfLib = new PDFLib();
$metadata = [
    'Title' => 'Financial Report 2024',
    'Author' => 'Finance Dept',
    'Keywords' => 'finance, 2024, report'
];
$pdfLib->setMetadata($metadata, 'tagged.pdf', 'source.pdf');
```

### 11. Page Rotation (New in v2.0)
Rotate all pages by 90, 180, or 270 degrees.

```php
$pdfLib = new PDFLib();
// Rotate 90 degrees clockwise
$pdfLib->rotateAll(90, 'rotated.pdf', 'source.pdf');
```

### 12. Form Flattening (New in v2.0)
Burn interactive form fields into the page content (prevent editing).

```php
$pdfLib = new PDFLib();
$pdfLib->flatten('flat.pdf', 'form.pdf');
```

### 13. PDF/A Conversion (New in v2.0)
Convert to PDF/A-1b standard for archival (requires valid color profiles in Ghostscript).

```php
$pdfLib = new PDFLib();
try {
    $pdfLib->convertToPDFA('archive.pdf', 'source.pdf');
} catch (Exception $e) {
    echo "PDF/A conversion failed: " . $e->getMessage();
}
```

### 14. OCR (Optical Character Recognition) (New in v2.0)
Convert scanned PDFs to searchable text. **Requires Ghostscript >= 9.53 with Tesseract/OCR devices.**

```php
$pdfLib = new PDFLib();
try {
    // Language code: 'eng', 'deu', 'spa', etc.
    $pdfLib->ocr('eng', 'searchable.pdf', 'scanned.pdf');
} catch (Exception $e) {
    echo "OCR failed (check if Tesseract is installed): " . $e->getMessage();
}
```

## Configuration

Fine-tune the behavior of the library:

*   **`setNumberOfRenderingThreads(int $threads)`**: Speed up conversion by using multiple threads (Default: 4).
*   **`setDPI(int $dpi)`**: Set output image resolution (Default: 300).
*   **`setImageQuality(int $quality)`**: Set JPEG quality (0-100).
*   **`setFilePrefix(string $prefix)`**: Custom prefix for output images (Default: "page-").

```php
$pdfLib->setNumberOfRenderingThreads(8)
       ->setDPI(150)
       ->setFilePrefix('slide-');
```

## Troubleshooting

**Error: `**** Unable to open the initial device, quitting.`**
*   **Cause**: Ghostscript cannot create temporary files due to permission issues.
*   **Fix**: Ensure your web server (e.g., Apache/Nginx user) has write permissions to the system's temporary directory, or check your server logs for specific path errors.

**Ghostscript Issues**
*   Ensure the `gs` command is available in your system path.
*   On Windows, ensure `gswin64c.exe` or `gswin32c.exe` is in your PATH.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits
- [Imal Hasaranga Perera](https://github.com/imalhasaranga)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
