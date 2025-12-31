# Chrome Headless Driver

The **ChromeHeadlessDriver** converts HTML content into high-quality PDFs by leveraging the rendering engine of Google Chrome.

## Usage

```php
use ImalH\PDFLib\PDF;

$pdf = PDF::init()->driver(PDF::DRIVER_CHROME);
```

## Convert HTML String

```php
$html = '
<html>
    <body>
        <h1>Invoice #1024</h1>
        <p>Total: $50.00</p>
    </body>
</html>
';

$pdf->convertFromHtml($html, 'invoice.pdf');
```

## Convert URL (Planned)

```php
// Coming in v3.1
// $pdf->fromUrl('https://example.com')->save('site.pdf');
```

## Troubleshooting
*   **"Binary not found"**: Ensure `google-chrome` or `chromium-browser` is in your system PATH.
*   **Permissions**: On Linux/Docker, you may need `--no-sandbox` flags if running as root. This driver applies basic flags by default.
