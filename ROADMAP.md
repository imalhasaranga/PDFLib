# 🗺️ Project Roadmap

This document outlines the future direction of **PDFLib**. We follow [Semantic Versioning](http://semver.org/).

---

## ✅ Completed (v3.1 Stable)

The v3.1 release (current stable) introduced advanced stateful operations, security, and major integrations.

- [x] **Stateful Chaining**: `rotate()->watermark()->save()` pipeline.
- [x] **Laravel Wrapper**: Official `PDFServiceProvider` and `PDF` Facade.
- [x] **Digital Signature Validation**: Verify X.509 signatures (`validate()`).
- [x] **OCR Support**: Extract text via Tesseract (`ocr()`).
- [x] **Redaction**: Coordinate-based text blackout (`redact()`).
- [x] **Metadata**: Read/Write PDF metadata (`getMetadata()`).
- [x] **Core Architecture**: `DriverInterface` with Ghostscript, OpenSSL, PDFtk, Chrome, Tesseract drivers.

---

## 🔮 Upcoming (v4.0)

### Theme: Cloud & Enterprise Scalability

The next major version will focus on running PDFLib in serverless and distributed environments.

#### 1. Cloud Drivers (AWS/S3)
- [ ] Implement `S3Driver` to handle input/output directly from AWS S3 streams without local temp files if possible (using stream wrappers).
- [ ] Support for AWS Lambda execution (Serverless PDF manipulation).

#### 2. Asynchronous Processing
- [ ] Integrate with Laravel Queues for background processing of heavy tasks (OCR, Compression).
- [ ] Webhook callbacks for long-running jobs.

#### 3. Advanced Editing (FPDI Integration)
- [ ] Deep integration with FPDI to allow:
    - [ ] Template importing (Letterheads).
    - [ ] Page rearrangement/deletion within PHP (no external binary needed).
    - [ ] Advanced text overlay with native font embedding.

#### 4. HTML Generation Improvements
- [ ] Integration with `spatie/browsershot` (Puppeteer) as a robust alternative to `ChromeHeadlessDriver`.
- [ ] Support for complex CSS Paged Media features.

#### 5. Containerization
- [ ] Official Docker Image `imalh/pdflib` pre-installed with `gs`, `pdftk`, `tesseract`, `poppler-utils`, `chrome`.

---

## 💡 Have an idea?
[Open a Discussion](https://github.com/imalhasaranga/PDFLib/discussions) to suggest new features!
