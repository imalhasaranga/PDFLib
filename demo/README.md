# PDFLib Interactive Demo

This is a complete, interactive web demonstration for the `imalhasaranga/pdf-lib` PHP library. It includes a Modern React Frontend and a PHP Backend to showcase all library features live.

## 🚀 Features Demonstrated

*   **Conversion**: PDF to Images, Version Conversion, PDF/A
*   **Manipulation**: Merge, Split, Rotate, Form Flattening
*   **Optimization**: Compression (Ghostscript)
*   **Security**: Encryption (User/Owner passwords)
*   **Metadata**: Read/Write PDF metadata
*   **OCR**: Perform OCR on scanned documents (requires Tesseract)

## 📦 Installation

### 1. Requirements

*   **PHP 7.4+**
*   **Node.js 16+** (for building the frontend)
*   **Composer**
*   **Ghostscript** (Required by the core library)

### 2. Setup

Place this `demo` folder in the root of your `PDFLib` project (or standalone).

**Backend Setup:**
```bash
cd demo
# Install the PDFLib library dependencies
composer require imalhasaranga/pdf-lib:dev-master
# Ensure permissions for upload/output directories
mkdir -p api/uploads api/output
chmod 777 api/uploads api/output
```

**Frontend Setup:**
```bash
# Install Node.js dependencies
npm install
```

## 🏃‍♂️ Running the Demo

You need to run two servers: one for the PHP API and one for the React Frontend.

**1. Start PHP Backend**
```bash
# In the /demo directory
php -S localhost:8000
```

**2. Start Frontend**
```bash
# Open a new terminal in /demo
npm run dev
```

**3. Access the Demo**
Open your browser and visit:
[http://localhost:5173](http://localhost:5173)

## 📁 Directory Structure

*   `src/`: React Source Code (UI, Logic)
*   `api/`: PHP Endpoints (Bridge to PDFLib)
*   `public/`: Static Assets

## 🔍 Integration Note

If you are including this in the main `PDFLib` repository, the `composer.json` in this demo folder is set up to require the library from Packagist. 

If you want to run it against the **local** source code of the library (without requiring it from Packagist), you can modify `demo/api/utils.php` or `demo/index.php` (if entry point exists) to include the library's local `autoload.php` from the parent directory:

```php
// In api/utils.php, instead of vendor/autoload.php:
require_once __DIR__ . '/../../vendor/autoload.php'; 
```
