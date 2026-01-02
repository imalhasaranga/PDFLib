# PDFLib Roadmap: Path to "The Best"

## 🚀 Vision: "The Laravel of PDF Libraries"
To become the undeniable #1 PHP PDF library by solving the ecosystem's fragmentation. currently, developers use `dompdf` for generation, `fpdi` for merging, and `spatie/pdf-to-image` for previews helping. **PDFLib v3** will unify these into a single, modern, driver-based fluent API.

**The "Killer" Value Proposition:**
> *"One API to Generate, Manipulate, Convert, and Sign PDFs, powered by the best underlying tools (Ghostscript, Chrome, LibreOffice) without the headache."*

## 🏗 Phase 1: The Modern Foundation (v3.0)
*Focus: Stabilization, Modernization, and Decoupling.*

### 1.1 Core Modernization
- **Drop PHP 5.x Support**: Target **PHP 8.1+** exclusively.
- **Type Safety**: Implement `strict_types=1`, native property typing, and return types.
- **PSR Compliance**: strictly adhere to PSR-4 and PSR-12 coding standards.
- **Exception Handling**: Replace generic `\Exception` with specific exceptions (e.g., `BinaryNotFoundException`, `MalformedPdfException`, `ProcessFailedException`).

### 1.2 Architecture Overhaul (The Driver Pattern)
Decouple the library from `exec()` and `ghostscript` to support multiple backends.
- **`DriverInterface`**: Abstract the underlying binary calls.
- **Drivers**:
    - `GhostscriptDriver` (Default, robust manipulation).
    - `HtmldocDriver` / `ChromeHeadlessDriver` (For HTML generation).
    - `OpenSslDriver` (For signatures).
- **Process Management**: Use `symfony/process` for robust command execution, timeouts, and error handling instead of `shell_exec`.

### 1.3 Tooling & Quality
- **Testing**: Upgrade to **PHPUnit 10/11**.
- **Static Analysis**: Integrate **PHPStan** (Level max) and **Pint** for code style.
- **CI/CD**: GitHub Actions for multi-platform tests (Ubuntu, macOS, Windows).

---

## ✨ Phase 2: Feature Expansion (v3.x)
*Focus: Filling the Gaps to beat Competitors.*

### 2.1 HTML to PDF Generation
The #1 requested feature in PHP. 
- Implement `FromHtml` feature using a headless browser adapter (e.g., local Chrome binary or Puppeteer bridge).
- Why: Ghostscript cannot render HTML/CSS. This bridges the gap with `dompdf`/`snappy`.

### 2.2 Digital Signatures & Security
Enterprise-grade security features.
- **Digital Signing**: Sign PDFs with PFX/P12 certificates using OpenSSL.
- **Validation**: Verify existing signatures.
- **Redaction**: Permanently remove sensitive text/images (blackout) using coordinate-based redaction.

### 2.3 Interactive Forms
- **Form Filling**: Inject data into AcroForms (FDF merging).
- **Form Extraction**: Read values from filled PDF forms.

### 2.4 Visual & Content Manipulation
- **Overlay/Template Engine**: Import a PDF page as a background and write text/images on top (Watermarking 2.0).
- **Native Text Extraction**: Extract text content directly (comparable to `pdftotext`) without OCR.
- **Search & Highlight**: Find text coordinates and apply highlight annotations.

---

## 🛠 Phase 3: Developer Experience (DX)
*Focus: Usability and Ecosystem.*

- **Laravel Wrapper**: Official `pdf-lib-laravel` package with Facades and configuration publishing.
- **Fluent API 2.0**:
  ```php
  PDF::load('doc.pdf')
     ->sign($certificate)
     ->watermark('CONFIDENTIAL')
     ->save('signed.pdf');
  ```
- **Validation API**: `PDF::validate('doc.pdf')->isPdfA();`

---

## 📊 Competitive Landscape & Gap Analysis

| Feature | **PDFLib v3 (Goal)** | **TCPDF** | **Spatie/pdf-to-image** | **Dompdf / mPDF** | **Snappy (wkhtmltopdf)** |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Primary Focus** | **All-in-One** | Generation (Legacy) | Conversion | HTML Generation | HTML Generation |
| **Architecture** | **Driver-Based** (Modular) | Monolithic Class | Wrapper (Imagick) | Monolithic Class | Wrapper (Legacy Bin) |
| **HTML Rendering** | **⭐⭐⭐⭐⭐ (Chrome)** | ⭐⭐ (Basic) | ❌ | ⭐⭐⭐⭐ (Good) | ⭐⭐⭐⭐ (Good) |
| **Manipulation** | **⭐⭐⭐⭐⭐ (Ghostscript)** | ⭐⭐ (FPDI Addon) | ❌ | ❌ | ❌ |
| **DX / API** | **Fluent (Modern)** | Complex / Legacy | Fluent | Average | Simple |
| **Digital Signatures**| **✅ (OpenSSL)** | ✅ (Complex) | ❌ | ❌ | ❌ |
| **OCR** | **✅ (Tesseract)** | ❌ | ❌ | ❌ | ❌ |
| **Performance** | **High** (Async Processes) | Low (Memory Heavy) | Medium | Low (Memory Heavy)| Medium |

## ⚠️ Breaking Changes & Migration Strategy
Yes, **v3.0** will introduce breaking changes to enable modernization. We will strictly follow **Semantic Versioning**.

### Breaking Changes:
1.  **PHP Requirement**: Minimum PHP version bumped to **8.1**.
2.  **Strict Types**: Code relying on loose type casting might fail.
3.  **Exception Handling**: `\Exception` will be replaced by specific exceptions.

### Mitigation Strategy (The "Legacy Facade"):
To minimize pain, we will implement a **Facade Pattern** that mimics the v2.x API but uses the new v3.0 Driver system internally.
- **Old way (still supported via Facade):**
  ```php
  $pdf = new PDFLib(); // Internal wrapper
  $pdf->convert();
  ```
- **New way (Recommended):**
  ```php
  $pdf = PDF::init()->driver(PDF::DRIVER_GHOSTSCRIPT)->convert();
  ```
- **Migration Guide**: A comprehensive upgrade guide will be provided.
- **v2.x Long Term Support (LTS)**: We will continue to apply security fixes to v2.x for 12 months after v3.0 release.
