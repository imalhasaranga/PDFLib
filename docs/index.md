# Introduction

**PDFLib** is a modern, driver-based PHP library for manipulating PDF files. It provides a unified, fluent API to handle complex PDF operations by leveraging powerful underlying tools like Ghostscript, Google Chrome, OpenSSL, and PDFtk.

## Why PDFLib?

The PHP ecosystem for PDF manipulation is fragmented:
- **Generation**: `dompdf` or `wkhtmltopdf` (legacy).
- **Manipulation**: `fpdip` or `tcpdf` (complex, legacy codebases).
- **Previews**: `spatie/pdf-to-image` (limited scope).

**PDFLib v3** aims to solve this by being the **"One API to rule them all"**.
It decouples the *Interface* (Fluent API) from the *Implementation* (Drivers), allowing you to swap backends without changing your code structure.

## Core Features

*   **HTML to PDF**: High-fidelity rendering using Chrome/Chromium.
*   **Manipulation**: Merge, Split, Rotate, Watermark, Compress, Encrypt (via Ghostscript).
*   **Digital Signatures**: Sign PDFs securely with X.509 certs (via OpenSSL/TCPDF).
*   **Interactive Forms**: Fill and Flatten AcroForms (via PDFtk).
*   **Modern DX**: PHP 8.1+, Strict Types, Facade-based or Dependency Injection usage.

[Get Started with Installation →](installation.md)

## Driver Documentation

*   **[Ghostscript Driver](drivers/ghostscript.md)** - Logic for Conversion, Merge, Split, etc.
*   **[Chrome Headless Driver](drivers/chrome.md)** - HTML to PDF Conversion.
*   **[OpenSSL Driver](drivers/openssl.md)** - Digital Signatures.
*   **[PDFtk Driver](drivers/pdftk.md)** - Form Filling and Flattening.
