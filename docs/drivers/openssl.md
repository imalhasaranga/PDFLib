# OpenSSL Driver

The **OpenSslDriver** creates PAdES-compliant digital signatures using X.509 certificates.

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
```

## Self-Signed vs Trusted
If using a **Self-Signed Certificate** (generated via openssl CLI), PDF viewers like Adobe Reader will show a yellow warning ("Validity Unknown"). This is normal. For a green "Trusted" checkmark, use a certificate from a recognized CA (e.g., Verisign, DigiCert).
