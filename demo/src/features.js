export const features = [
  {
    id: 1,
    title: "Convert PDF to Images",
    description: "Convert specific pages or the entire document to PNG or JPEG with high fidelity.",
    icon: "Image",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/convert.php",
    accept: ".pdf",
    buttonText: "Convert to Images",
    code: `$pdfLib = new PDFLib();
$pdfLib->setPdfPath('doc.pdf')
    ->setOutputPath('output')
    ->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG)
    ->setDPI(300)
    ->convert();`
  },
  {
    id: 2,
    title: "Create PDF from Images",
    description: "Combine a list of images into a single, optimized PDF file.",
    icon: "FilePlus",
    demoType: "upload-multi", // Now supported
    endpoint: "http://localhost:8000/api/images-to-pdf.php",
    accept: "image/*", // IMPORTANT: Accept images
    buttonText: "Create PDF (Select Images)",
    code: `$pdfLib = new PDFLib();
$images = ['img1.jpg', 'img2.jpg'];
$pdfLib->makePDF('output.pdf', $images);`
  },
  {
    id: 3,
    title: "Compress PDF",
    description: "Reduce file size significantly using Ghostscript optimization presets.",
    icon: "Minimize2",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/compress.php",
    accept: ".pdf",
    buttonText: "Compress PDF",
    code: `$pdfLib = new PDFLib();
// Levels: screen, ebook, printer, prepress
$pdfLib->compress('large.pdf', 'opt.pdf', PDFLib::$COMPRESSION_EBOOK);`
  },
  {
    id: 4,
    title: "Merge PDFs",
    description: "Combine multiple PDF documents into a single unified file.",
    icon: "Merge",
    demoType: "upload-multi", 
    endpoint: "http://localhost:8000/api/merge.php",
    accept: ".pdf",
    buttonText: "Merge PDFs (Upload 2+)",
    code: `$pdfLib = new PDFLib();
$files = ['part1.pdf', 'part2.pdf'];
$pdfLib->merge($files, 'merged.pdf');`
  },
  {
    id: 5,
    title: "Split PDF",
    description: "Extract specific pages or ranges into new PDF files.",
    icon: "Scissors",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/split.php",
    accept: ".pdf",
    buttonText: "Split PDF (Page 1)",
    code: `$pdfLib = new PDFLib();
// Extract pages 1-5
$pdfLib->split('1-5', 'chapter1.pdf', 'source.pdf');`
  },
  {
    id: 6,
    title: "Encrypt & Protect",
    description: "Secure your PDFs with passwords and permission restrictions.",
    icon: "Lock",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/encrypt.php",
    accept: ".pdf",
    buttonText: "Encrypt PDF",
    code: `$pdfLib = new PDFLib();
$pdfLib->encrypt(
    'userPass', 
    'ownerPass', 
    'protected.pdf', 
    'source.pdf'
);`
  },
  {
    id: 7,
    title: "Watermarking",
    description: "Add text watermarks to pages for branding or security.",
    icon: "Stamp",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/watermark.php",
    accept: ".pdf",
    buttonText: "Add Watermark",
    code: `$pdfLib = new PDFLib();
$pdfLib->addWatermarkText(
    'CONFIDENTIAL', 
    'watermarked.pdf', 
    'source.pdf'
);`
  },
  {
    id: 8,
    title: "Thumbnail Generation",
    description: "Generate preview thumbnails of the first page.",
    icon: "FileImage",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/thumbnail.php",
    accept: ".pdf",
    buttonText: "Generate Thumbnail",
    code: `$pdfLib = new PDFLib();
$pdfLib->createThumbnail('thumb.jpg', 200, 'source.pdf');`
  },
  {
    id: 9,
    title: "Version Conversion",
    description: "Convert PDFs to specific versions for compatibility.",
    icon: "RefreshCw",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/version.php",
    accept: ".pdf",
    buttonText: "Convert to v1.4",
    code: `$pdfLib = new PDFLib();
$pdfLib->convertToVersion('1.4', 'compat.pdf', 'source.pdf');`
  },
  {
    id: 10,
    title: "Metadata Management",
    description: "Read and write PDF metadata properties.",
    icon: "Tag",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/metadata.php",
    accept: ".pdf",
    buttonText: "Update Metadata",
    code: `$pdfLib = new PDFLib();
$pdfLib->setMetadata([
    'Title' => 'Report',
    'Author' => 'Me'
], 'meta.pdf', 'source.pdf');`
  },
  {
    id: 11,
    title: "Page Rotation",
    description: "Rotate pages by 90, 180, or 270 degrees.",
    icon: "RotateCw",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/rotate.php",
    accept: ".pdf",
    buttonText: "Rotate 90°",
    code: `$pdfLib = new PDFLib();
$pdfLib->rotateAll(90, 'rotated.pdf', 'source.pdf');`
  },
  {
    id: 12,
    title: "Form Flattening",
    description: "Make interactive form fields permanent and non-editable.",
    icon: "Layout",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/flatten.php",
    accept: ".pdf",
    buttonText: "Flatten Forms",
    code: `$pdfLib = new PDFLib();
$pdfLib->flatten('flat.pdf', 'form.pdf');`
  },
  {
    id: 13,
    title: "PDF/A Conversion",
    description: "Convert to PDF/A standards for long-term archiving.",
    icon: "Archive",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/pdfa.php",
    accept: ".pdf",
    buttonText: "Convert to PDF/A",
    code: `$pdfLib = new PDFLib();
$pdfLib->convertToPDFA('archive.pdf', 'source.pdf');`
  },
  {
    id: 14,
    title: "OCR",
    description: "Extract text from scanned documents using Tesseract.",
    icon: "ScanText",
    demoType: "upload",
    endpoint: "http://localhost:8000/api/ocr.php",
    accept: ".pdf",
    buttonText: "Perform OCR (English)",
    code: `$pdfLib = new PDFLib();
$pdfLib->ocr('eng', 'searchable.pdf', 'scanned.pdf');`
  }
];
