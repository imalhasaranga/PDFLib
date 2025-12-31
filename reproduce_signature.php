<?php

require_once __DIR__ . '/vendor/autoload.php';

use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\OpenSslDriver;

echo "Testing OpenSslDriver...\n";

// 1. Generate Self-Signed Cert for Testing
$dn = [
    "countryName" => "LK",
    "stateOrProvinceName" => "Western",
    "localityName" => "Colombo",
    "organizationName" => "PDFLib Test",
    "organizationalUnitName" => "IT",
    "commonName" => "PdfLib Test CA",
    "emailAddress" => "test@example.com"
];

$privkey = openssl_pkey_new([
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
]);

$csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
$x509 = openssl_csr_sign($csr, null, $privkey, $days = 365, array('digest_alg' => 'sha256'));

// Save Certs
$certFile = __DIR__ . '/tests/data/test_cert.crt';
$keyFile = __DIR__ . '/tests/data/test_key.pem';
$outputPdf = __DIR__ . '/tests/data/signed_output.pdf';

if (!is_dir(__DIR__ . '/tests/data'))
    mkdir(__DIR__ . '/tests/data', 0777, true);

openssl_x509_export_to_file($x509, $certFile);
openssl_pkey_export_to_file($privkey, $keyFile);

echo "Generated Cert: $certFile\n";
echo "Generated Key: $keyFile\n";

// 2. Run Test
try {
    // For this test, we are using a dummy source file because our OpenSslDriver implementation
    // currently creates a NEW PDF rather than strictly importing (until FPDI is checked).
    // So 'source.pdf' is just for validation of the property, not used content-wise in the naive driver logic.
    $dummySource = __DIR__ . '/tests/data/dummy.pdf';
    file_put_contents($dummySource, 'dummy pdf content');

    // Test direct Driver usage
    $driver = new OpenSslDriver();
    $driver->setSource($dummySource); // required by strict check

    echo "Signing PDF...\n";
    $success = $driver->sign($certFile, $keyFile, $outputPdf, ['info' => ['Name' => 'Tester', 'Reason' => 'Unit Test']]);

    if ($success && file_exists($outputPdf)) {
        echo "SUCCESS: Signed PDF created at $outputPdf (" . filesize($outputPdf) . " bytes)\n";

        // Bonus: Validate content logic
        $content = file_get_contents($outputPdf);
        if (strpos($content, 'Digitally Signed') !== false) {
            echo "Verified: Content contains signature text.\n";
        }
        if (strpos($content, '/ByteRange') !== false || strpos($content, '/Sig') !== false) {
            echo "Verified: Content contains PDF signature structure.\n";
        }

    } else {
        echo "FAILURE: File not created.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
