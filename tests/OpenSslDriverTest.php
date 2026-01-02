<?php

use PHPUnit\Framework\TestCase;
use ImalH\PDFLib\Drivers\OpenSslDriver;
use ImalH\PDFLib\PDF;

class OpenSslDriverTest extends TestCase
{
    private static $_DATA_FOLDER = __DIR__ . "/data";
    private static $_OUTPUT_FILE = __DIR__ . "/data/signed_output.pdf";
    private static $_CERT_FILE = __DIR__ . "/data/test_cert.crt";
    private static $_KEY_FILE = __DIR__ . "/data/test_key.pem";

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!file_exists(self::$_DATA_FOLDER)) {
            mkdir(self::$_DATA_FOLDER, 0777, true);
        }

        // Generate Keys if not exist
        $dn = [
            "countryName" => "LK",
            "stateOrProvinceName" => "Western",
            "localityName" => "Colombo",
            "organizationName" => "PDFLib Test",
            "commonName" => "PdfLib Test CA",
            "emailAddress" => "test@example.com"
        ];

        $privkey = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        $csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
        $x509 = openssl_csr_sign($csr, null, $privkey, $days = 365, array('digest_alg' => 'sha256'));

        openssl_x509_export_to_file($x509, self::$_CERT_FILE);
        openssl_pkey_export_to_file($privkey, self::$_KEY_FILE);

        // Dummy Source
        file_put_contents(self::$_DATA_FOLDER . '/dummy.pdf', 'dummy content');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        // Cleanup keys usually, but keep for debug if needed
        // unlink(self::$_OUTPUT_FILE);
    }

    public function testSignPdfDirectly(): void
    {
        $driver = new OpenSslDriver();
        $driver->setSource(self::$_DATA_FOLDER . '/dummy.pdf');

        $result = $driver->sign(
            self::$_CERT_FILE,
            self::$_KEY_FILE,
            self::$_OUTPUT_FILE,
            ['info' => ['Reason' => 'PHPUnit Test']]
        );

        $this->assertTrue($result, "Signing returned false");
        $this->assertFileExists(self::$_OUTPUT_FILE);
        $this->assertGreaterThan(0, filesize(self::$_OUTPUT_FILE));

        $content = file_get_contents(self::$_OUTPUT_FILE);
        $this->assertStringContainsString('/Sig', $content, "Missing Signature Dictionary");
    }

    public function testFacadeIntegration(): void
    {
        $output = self::$_DATA_FOLDER . "/facade_signed.pdf";

        // We use the Facade to pick the driver
        $pdf = PDF::init()->driver(PDF::DRIVER_OPENSSL);
        $pdf->from(self::$_DATA_FOLDER . '/dummy.pdf');

        $result = $pdf->sign(
            self::$_CERT_FILE,
            self::$_KEY_FILE,
            $output,
            ['info' => ['Name' => 'Facade User']]
        );

        $this->assertTrue($result);
        $this->assertFileExists($output);
    }
}
