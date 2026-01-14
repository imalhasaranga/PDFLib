# Changelog

### [v3.1.7] - 2026-01-14
- **Fixed:** `Convert to Image` silent failure by throwing exceptions on process error in `GhostscriptDriver`.
- **Fixed:** `OCR` demo failure by adding language support and error handling in `TesseractDriver`.
- **Fixed:** "Unexpected end of JSON" errors in demo API by hardening `jsonResponse` against UTF-8 encoding issues.
- **Fixed:** Ghostscript binary detection logic refined for Windows/Docker.

### [v3.1.6] - 2026-01-13
- **Fixed:** Redaction logic now supports multi-word phrases (e.g., "Secret Code") by calculating union bounding boxes.
- **Fixed:** Added `extra.laravel.providers` to `composer.json` for automatic Laravel package discovery.
- **Fixed:** Robust binary detection for `ChromeHeadlessDriver` and `OpenSslDriver` on Windows/macOS/Linux.
- **Added:** New `RedactionLogicTest` to prevent regression of split-word redaction issues.

### [v3.1.4] - 2026-01-02
- **Fixed:** Critical bug in `PdftkDriver::fillForm` where the command array was overwritten, causing form flattening to fail.
- **Fixed:** `PdftkDriver::getFormFields` now correctly parses field types and options (dropdowns).
- **Added:** Implemented `getNumberOfPages` for `PdftkDriver`.

All Significant changes to `PDFlib` will be documented in here

### Page Range Conversion Bug Fix
- there was a issue in the page range conversion, this was resolved by a help of a pull requests, and added test cases
- convert() method will now return the converted image names

## 1.2.0 - 2016-09-05

### Project Renamed to PDFBox to PDFlib
- an issue opend, requesting to rename it

### PNG support added
- setImageFormat() function added allowing output format to be set

## 1.1.0 - 2016-09-05

### Repo Created
- This is the first release of PDFBox :)

## 1.0.0 - 2016-09-05

