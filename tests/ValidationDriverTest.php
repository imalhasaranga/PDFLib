<?php

namespace Tests;

use ImalH\PDFLib\PDF;
use ImalH\PDFLib\Drivers\OpenSslDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ValidationDriverTest extends TestCase
{
    protected string $unsignedPdf;
    protected string $signedPdf;
    protected string $certPath;
    protected string $keyPath;
    protected bool $hasPdfSig = false;

    protected function setUp(): void
    {
        // Check for pdfsig
        $process = new Process(['pdfsig', '-v']);
        $process->run();
        $this->hasPdfSig = $process->isSuccessful();

        if (!$this->hasPdfSig) {
            $this->markTestSkipped('pdfsig (poppler-utils) not found, skipping validation test.');
        }

        $this->unsignedPdf = __DIR__ . '/validation_unsigned.pdf';
        $this->signedPdf = __DIR__ . '/validation_signed.pdf';
        $this->certPath = __DIR__ . '/validation_cert.crt';
        $this->keyPath = __DIR__ . '/validation_key.pem';

        // 1. Create simple PDF
        if (!class_exists('TCPDF')) {
            $this->markTestSkipped('TCPDF not installed');
        }
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->Write(1, 'Unsigned');
        $pdf->Output($this->unsignedPdf, 'F');

        // 2. Create Certificate
        $this->createSelfSignedCert();

        // 3. Sign it
        $res = PDF::init()
            ->driver(PDF::DRIVER_OPENSSL)
            ->from($this->unsignedPdf)
            ->sign($this->certPath, $this->keyPath, $this->signedPdf, ['password' => 'test']);

        if (!$res) {
            $this->fail("Failed to sign PDF for testing");
        }
    }

    protected function tearDown(): void
    {
        @unlink($this->unsignedPdf);
        @unlink($this->signedPdf);
        @unlink($this->certPath);
        @unlink($this->keyPath);
    }

    protected function createSelfSignedCert()
    {
        $dn = [
            "countryName" => "US",
            "stateOrProvinceName" => "State",
            "localityName" => "City",
            "organizationName" => "Company",
            "organizationalUnitName" => "Unit",
            "commonName" => "Test Signer",
            "emailAddress" => "email@example.com"
        ];
        $privkey = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        $csr = openssl_csr_new($dn, $privkey, ['digest_alg' => 'sha256']);
        $x509 = openssl_csr_sign($csr, null, $privkey, 365, ['digest_alg' => 'sha256'], 1234); // Serial 1234

        openssl_x509_export_to_file($x509, $this->certPath);
        openssl_pkey_export_to_file($privkey, $this->keyPath, "test");
    }

    public function test_validate_valid_signature()
    {
        // Note: self-signed certs might show as "Certificate Validation: Untrusted" 
        // but "Signature Validation: Signature is Valid".
        // Our driver implementation checks for "Signature is Valid." string.

        $isValid = PDF::init()
            ->driver(PDF::DRIVER_OPENSSL)
            ->validate($this->signedPdf);

        $this->assertTrue($isValid, "Signed PDF should be valid (structurally)");
    }

    public function test_validate_unsigned_pdf()
    {
        if (!$this->hasPdfSig) {
            $this->markTestSkipped('pdfsig not found');
        }

        $isValid = PDF::init()
            ->driver(PDF::DRIVER_OPENSSL)
            ->validate($this->unsignedPdf);

        $this->assertFalse($isValid, "Unsigned PDF should return false");
    }

    public function test_throws_exception_if_pdfsig_missing()
    {
        if ($this->hasPdfSig) {
            $this->markTestSkipped('pdfsig IS found, cannot test missing exception');
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Validation requires 'pdfsig' (poppler-utils)");

        PDF::init()
            ->driver(PDF::DRIVER_OPENSSL)
            ->validate($this->unsignedPdf);
    }
}
