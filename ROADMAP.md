# 🗺️ Project Roadmap

This document outlines the future direction of **PDFLib**. We follow [Semantic Versioning](http://semver.org/).

---

## ✅ Completed (v3.0 Alpha)

The foundation has been laid with a modular, driver-based architecture.

- [x] **Core Architecture**: `DriverInterface` and Factory Pattern.
- [x] **Ghostscript Driver**: Robust manipulation (Convert, Merge, Split, Compress, Encrypt, Watermark).
- [x] **Chrome Driver**: High-fidelity HTML-to-PDF generation.
- [x] **OpenSSL Driver**: Digital Signatures (X.509).
- [x] **PDFtk Driver**: Interactive Form Filling and Inspection.
- [x] **Fluent API**: Modern `PDF::init()->...` syntax.
- [x] **CI/CD**: Cross-platform testing (Ubuntu, Windows, macOS).

---

## 🔮 Upcoming (v3.1+)

### 3.1 Advanced Chaining & State
*Goal: True stateful manipulation without intermediate file handling.*

- [ ] **Stateful Chaining**: Allow multiple operations in one chain without specifying intermediate paths.
  ```php
  PDF::from('doc.pdf')
      ->rotate(90)     // In-memory/temp state
      ->watermark('DRAFT')
      ->save('final.pdf');
  ```
- [ ] **Smart Pipeline**: Optimize driver calls (e.g., combine multiple Ghostscript operations into a single command where possible).

### 3.2 Ecosystem & Wrappers
*Goal: Making it easiest to use in frameworks.*

- [ ] **Laravel Wrapper**: Official `pdf-lib-laravel` package.
  - Facades (`PDF::...`)
  - Config publishing
  - Service Provider integration
- [ ] **Digital Signature Validation**: Verify signatures and check validity (Green checkmark logic).

### 3.3 New Drivers & Features
*Goal: Expanding capabilities.*

- [ ] **OCR Support**: `TesseractDriver` for extracting text from scanned PDFs.
- [ ] **Redaction**: Coordinate-based blackout/redaction for sensitive data.
- [ ] **Metadata Extraction**: Unified API to read PDF metadata.
- [ ] **Metadata Extraction**: Unified API to read PDF metadata.

### 3.4 Future (v3.2+)
- [ ] **S3 / Cloud Storage Support**: Stream drivers directly to/from cloud storage adapters (Flysystem).

### 4.0 Long Term
- [ ] **Pure PHP Driver**: A native PHP implementation (like `fpdi` or `dompdf`) for environments where binaries cannot be installed.

---

## 💡 Have an idea?
[Open a Discussion](https://github.com/imalhasaranga/PDFLib/discussions) to suggest new features!
