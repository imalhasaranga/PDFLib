# Installation

## Requirements

*   **PHP**: 8.1 or higher
*   **Composer**

### Driver Dependencies
PDFLib acts as a bridge to powerful system binaries. You only need to install the tools for the features you intend to use.

| feature | Driver | Requirement |
| :--- | :--- | :--- |
| **Manipulation** (Merge, Split, Convert) | `GhostscriptDriver` | **Ghostscript** (v9.16+) |
| **HTML to PDF** | `ChromeHeadlessDriver` | **Google Chrome** or **Chromium** |
| **Digital Signatures** | `OpenSslDriver` | **PHP OpenSSL** & `tecnickcom/tcpdf` |
| **Form Filling** | `PdftkDriver` | **pdftk** (PDF Toolkit) |

---

## 1. Install via Composer

```bash
composer require imal-h/pdf-box
```

## 2. Install System Binaries

### macOS (Homebrew)
```bash
# Ghostscript
brew install ghostscript

# Chrome (usually already installed, or install Chromium)
brew install --cask google-chrome

# PDFtk
brew install pdftk-java
```

### Ubuntu / Debian
```bash
# Ghostscript
sudo apt-get update
sudo apt-get install ghostscript

# Chrome (install stable)
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install ./google-chrome-stable_current_amd64.deb

# PDFtk
sudo apt-get install pdftk
```

### Windows
*   **Ghostscript**: Download installer from [ghostscript.com](https://www.ghostscript.com/download.html). Add `gswin64c.exe` to your PATH.
*   **Chrome**: Standard installation.
*   **PDFtk**: Download from [pdflabs.com](https://www.pdflabs.com/tools/pdftk-server/).

---

## 3. Configuration

By default, PDFLib attempts to autodetect binaries in common paths. If your environment uses custom paths, pass them to the Driver constructor.

```php
use ImalH\PDFLib\Drivers\GhostscriptDriver;
use ImalH\PDFLib\PDF;

// Custom Path Example
$driver = new GhostscriptDriver('/custom/path/to/gs');
$pdf = new PDF($driver);
```
