# Comprehensive Installation Guide

This guide details how to install the necessary system dependencies for PDFLib v3.1 on **macOS**, **Ubuntu/Debian**, and **Windows**.

## 1. PHP & Extensions

**Requirement**: PHP >= 8.1

### macOS (Homebrew)
```bash
brew install php
```

### Ubuntu / Debian
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-mbstring php8.1-xml php8.1-zip
```

### Windows
1. Download the **Non-Thread Safe (NTS)** version from [windows.php.net](https://windows.php.net/download/).
2. Extract to `C:\php`.
3. Add `C:\php` to your System PATH environment variable.

---

## 2. Driver Dependencies

You only need to install the tools for the drivers you intend to use.

| Feature | Driver | Requirement |
| :--- | :--- | :--- |
| **Manipulation** | `GhostscriptDriver` | [Ghostscript](#21-ghostscript) |
| **HTML to PDF** | `ChromeHeadlessDriver` | [Google Chrome](#22-google-chrome--chromium) |
| **Form Filling** | `PdftkDriver` | [PDFtk](#23-pdftk-server) |
| **OCR** | `TesseractDriver` | [Tesseract](#24-tesseract-ocr) |
| **Signatures** | `OpenSslDriver` | OpenSSL (Built-in to PHP) |

### 2.1 Ghostscript
*Required for: Merge, Split, Convert, Compress, Flatten, Redact.*

**Version**: 9.16 or higher

#### macOS
```bash
brew install ghostscript
```

#### Ubuntu / Debian
```bash
sudo apt install ghostscript
```

#### Windows
1. Download `Ghostscript AGPL Release` from [ghostscript.com](https://www.ghostscript.com/releases/gsdnld.html).
2. Install the package.
3. **Critical**: Add the `bin` and `lib` folders to your PATH (e.g., `C:\Program Files\gs\gs9.55.0\bin`).
4. PDFLib looks for `gswin64c.exe` automatically.

---

### 2.2 Google Chrome / Chromium
*Required for: HTML to PDF conversion.*

#### macOS
```bash
brew install --cask google-chrome
# OR
brew install chromium
```

#### Ubuntu / Debian
```bash
# Install stable Chrome
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install ./google-chrome-stable_current_amd64.deb
```

#### Windows
Standard installation of Google Chrome is sufficient. PDFLib will auto-detect it in standard `Program Files` locations.

---

### 2.3 PDFtk Server
*Required for: Form Filling and Inspection.*

#### macOS
```bash
brew install pdftk-java
```

#### Ubuntu / Debian
```bash
sudo apt install pdftk
```

#### Windows
1. Download **PDFtk Server** from [pdflabs.com](https://www.pdflabs.com/tools/pdftk-server/).
2. Run the installer.
3. Ensure "Add application to your system path" is checked during installation.

---

### 2.4 Tesseract OCR
*Required for: Text Extraction (OCR).*

#### macOS
```bash
brew install tesseract
```

#### Ubuntu / Debian
```bash
sudo apt install tesseract-ocr
```

#### Windows
1. Download the installer from the [UB-Mannheim Tesseract Wiki](https://github.com/UB-Mannheim/tesseract/wiki).
2. Install and add the install directory to your PATH.

---

## 3. Configuration (Optional)

PDFLib tries to find these binaries automatically. If you have installed them in non-standard locations, you can explicitly configure paths in `config/pdflib.php` (for Laravel) or pass them to the Driver.

```php
// Manual Driver Configuration
$driver = new \ImalH\PDFLib\Drivers\GhostscriptDriver(
    binaryPath: '/custom/path/to/gs'
);
```
