![Banner](readme_banner.jpg)

Try out before you actually use it 
```bash
docker run --pull always -p 9090:80 treineticprojects/demo_opensource:latest
```

# PDFLib v3.0 (Alpha)

![Issues](https://img.shields.io/github/issues/imalhasaranga/PDFLib.svg)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
![Forks](https://img.shields.io/github/forks/imalhasaranga/PDFLib.svg)

**The most advanced, driver-based PDF manipulation library for PHP.**

PDFLib v3.0 has been completely re-architected to be modular and driver-based, allowing you to switch between powerful backends like Ghostscript, Chrome Headless (coming soon), and OpenSSL for different tasks, all under a single, beautiful fluent API.

👉 **[Try the Interactive Demo](demo/README.md)** | 📚 **[Read the Documentation](docs/index.md)**

---

## 🚀 What's New in v3.0?

- **Fluent API**: Modern, chainable interface (`PDF::init()->from()->to()->convert()`).
- **Driver Support**: Decoupled architecture using `DriverInterface`. Default driver is `GhostscriptDriver`.
- **PHP 8.1+**: Fully typed, strict, and modern codebase.
- **Legacy Compatibility**: Full backward compatibility for v2.x code via a transparent Facade.

## 📦 Requirements

*   **PHP** >= 8.1
*   **Ghostscript** >= 9.16 (for GhostscriptDriver)
*   **Google Chrome** or **Chromium** (for HTML to PDF)
*   **pdftk** (PDF Toolkit) (for Form Filling)

## 🔧 Installation

```bash
composer require imal-h/pdf-box
```

## ✨ Features

| Feature | Description | Driver |
| :--- | :--- | :--- |
| **HTML to PDF** | Generate PDF from HTML/CSS | [Chrome](docs/drivers/chrome.md) |
| **Digital Sign** | Sign PDFs with X.509 Certs | [OpenSSL](docs/drivers/openssl.md) |
| **Fill Forms** | Fill AcroForms (FDF) | [PDFtk](docs/drivers/pdftk.md) |
| **Inspect Forms** | Get Field Names | [PDFtk](docs/drivers/pdftk.md) |
| **Convert** | PDF to Images (PNG/JPG) | [Ghostscript](docs/drivers/ghostscript.md) |
| **Merge** | Combine multiple PDFs | [Ghostscript](docs/drivers/ghostscript.md) |
| **Split** | Extract pages or ranges | [Ghostscript](docs/drivers/ghostscript.md) |
| **Compress** | Optimize PDF file size | [Ghostscript](docs/drivers/ghostscript.md) |
| **Encrypt** | Password protection and permissions | [Ghostscript](docs/drivers/ghostscript.md) |
| **Watermark** | Overlay text on pages | [Ghostscript](docs/drivers/ghostscript.md) |
| **Rotation** | Rotate pages 90/180/270° | [Ghostscript](docs/drivers/ghostscript.md) |
| **Metadata** | Edit Title, Author, Keywords | [Ghostscript](docs/drivers/ghostscript.md) |
| **Flatten** | Burn forms into content | Ghostscript |

## 📖 Usage

### HTML to PDF (New in v3.0)

Generate PDFs from HTML content or URLs using Chrome Headless.

```php
use ImalH\PDFLib\PDF;

// From HTML String
PDF::init()
    ->driver(PDF::DRIVER_CHROME)
    ->convertFromHtml('<h1>Hello World</h1>', 'output.pdf');

// From URL (Coming Soon)
// PDF::init()->driver(PDF::DRIVER_CHROME)->fromUrl('https://google.com')->save('output.pdf');
```

### Digital Signatures (New in v3.0)

Digitally sign PDFs using OpenSSL (requires `tecnickcom/tcpdf`).

```php
use ImalH\PDFLib\PDF;

PDF::init()
    ->driver(PDF::DRIVER_OPENSSL)
    ->from('contract.pdf')
    ->sign('certificate.crt', 'private_key.pem', 'signed_contract.pdf', [
        'info' => [
            'Name' => 'John Doe',
            'Location' => 'Colombo, LK',
            'Reason' => 'Digital Contract Signature'
        ]
    ]);
```

> **Note:** If you use a self-signed certificate (like in testing), PDF viewers will show "Signature Validity Unknown". For a green "Trusted" checkmark, use a certificate issued by a recognized Certificate Authority (CA) or explicitly trust your self-signed certificate in the viewer's settings.

### Interactive Forms (New in v3.0)

Fill PDF forms programmatically using `pdftk`.

```php
use ImalH\PDFLib\PDF;

// 1. Inspect Fields (Optional)
$fields = PDF::init()->driver(PDF::DRIVER_PDFTK)->getFormFields('form_template.pdf');
// returns ['full_name', 'date', ...]

// 2. Fill Form
PDF::init()
    ->driver(PDF::DRIVER_PDFTK)
    ->from('form_template.pdf')
    ->fillForm([
        'full_name' => 'Imal Perera',
        'date' => '2025-01-01'
    ], 'filled_form.pdf');
```

### The Modern Way (Fluent API)

```php
use ImalH\PDFLib\PDF;

// Convert PDF Page 1 to JPEG
PDF::init()
    ->driver(PDF::DRIVER_GHOSTSCRIPT)
    ->from('document.pdf')
    ->to('output_folder')
    ->convert();
```

### The Legacy Way (v2.x Facade)

Existing code continues to work without changes, but is marked as deprecated.

```php
use ImalH\PDFLib\PDFLib; // Legacy Class

$pdfLib = new PDFLib();
$pdfLib->setPdfPath("document.pdf")
       ->setOutputPath("output_folder")
       ->convert();
```

### Example: Advanced Chain

```php
PDF::init()
    ->from('source.pdf')
    ->from('source.pdf')
    ->encrypt('userPass', 'ownerPass', 'processed.pdf');
```

(Note: Current driver operations like `encrypt`, `rotate`, and `watermark` are immediate and require a destination path. Fully stateful chaining for these methods is planned for v3.1)
```

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING](CONTRIBUTING.md) for details on our new coding standards (Pint, PHPStan) and architecture.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md).

---
*Initiative of [Treinetic (Pvt) Ltd](http://www.treinetic.com).*
