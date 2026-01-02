# Ghostscript Driver

The **GhostscriptDriver** is the workhorse of PDFLib. It handles almost all manipulation tasks.

## Usage

```php
use ImalH\PDFLib\PDF;

// Initialize
$pdf = PDF::init()->driver(PDF::DRIVER_GHOSTSCRIPT);
```

## Features

### 1. Convert to Image
Convert a PDF page (default: Page 1) to an image.

```php
$pdf->from('doc.pdf')
    ->to('output.jpg')
    ->convert();
```

### 2. Merge PDFs
Combine multiple PDF files into one.

```php
$pdf->merge([
    'cover.pdf',
    'content.pdf',
    'appendix.pdf'
], 'full_report.pdf');
```

### 3. Encrypt (Password Protect)
Add User/Owner passwords and restrict permissions.

```php
$pdf->from('sensitive.pdf')
    ->encrypt('userPass123', 'ownerRules!', 'locked.pdf');
```

### 4. Compress
Optimize file size using Ghostscript's presets (`screen`, `ebook`, `printer`, `prepress`, `default`).

```php
$pdf->compress('heavy.pdf', 'light.pdf', 'ebook');
```

### 5. Redaction
Permanently blackout sensitive text using coordinate analysis (requires `poppler-utils` for text finding).

```php
$pdf->from('confidential.pdf')
    ->redact('SecretCode', 'clean.pdf');
```

### 6. Metadata
Read and Write PDF metadata (Title, Author, etc).

```php
// Get Metadata
$meta = $pdf->getMetadata('doc.pdf');
// Returns: ['Title' => 'My Doc', 'Author' => 'Imal']

// Set Metadata
$pdf->setMetadata([
    'Title' => 'New Title',
    'Author' => 'New Author'
], 'updated.pdf');
```

### Other Methods
*   `rotate(int $degrees, string $dest)`: Rotate pages.
*   `watermark(string $text, string $dest)`: Add text watermark.
*   `split($range, string $dest)`: Extract pages (e.g., `'1-5'`).
*   `getFormFields` / `fillForm`: **Not Supported** (Use `PdftkDriver`).
