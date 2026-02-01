# OpenSSL Driver

The **OpenSslDriver** creates PAdES-compliant digital signatures using X.509 certificates.

## Image Signature (v3.2+)

You can also place a visual signature "stamp" (e.g., a PNG image) on a specific page with custom coordinates.

```php
PDF::init()->driver(PDF::DRIVER_OPENSSL)
   ->from('contract.pdf')
   ->sign($cert, $key, 'output.pdf', [
       'info' => ['Name' => 'John Doe'],
       'image' => '/path/to/signature.png',
       'page' => 1,         // Target page (1-based)
       'x' => 15,           // X position in mm
       'y' => 250,          // Y position in mm
       'w' => 60,           // Width in mm
       'h' => 30            // Height in mm
   ]);
```

### Page Dimensions & Centering

To center a signature or place it relatively, use `getPageDimensions($page)`:

```php
$pdf = PDF::init()->driver(PDF::DRIVER_OPENSSL)->from('doc.pdf');
$dims = $pdf->getPageDimensions(1); // ['w' => 215.9, 'h' => 279.4]

// Center signature at bottom
$w = 60; $h = 30;
$x = ($dims['w'] - $w) / 2;
$y = $dims['h'] - $h - 20; // 20mm padding from bottom

$pdf->sign($cert, $key, 'out.pdf', [
    'image' => 'sig.png',
    'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h
]);
```

## Requirements

*   `composer require tecnickcom/tcpdf`
*   A valid `.crt` (Certificate) and `.pem` (Private Key).

## Usage

```php
use ImalH\PDFLib\PDF;

// Initialize
$pdf = PDF::init()->driver(PDF::DRIVER_OPENSSL);

// Sign
$pdf->from('contract.pdf')
    ->sign(
        'certificate.crt', 
        'private_key.pem', 
        'signed_contract.pdf',
        [
            'info' => [
                'Name' => 'Imal Perera',
                'Location' => 'Colombo',
                'Reason' => 'Approval'
            ]
        ]
    );
            ]
        ]
    );
```

## Validation (Verify Signatures)
Check if a PDF is digitally signed and valid.

```php
$isValid = $pdf->validate('signed_contract.pdf');
if ($isValid) {
    echo "Signature is VALID";
} else {
    echo "Signature is INVALID or Modified";
}
```

## Self-Signed vs Trusted
If using a **Self-Signed Certificate** (generated via openssl CLI), PDF viewers like Adobe Reader will show a yellow warning ("Validity Unknown"). This is normal. For a green "Trusted" checkmark, use a certificate from a recognized CA (e.g., Verisign, DigiCert).
