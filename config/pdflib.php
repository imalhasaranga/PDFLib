<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "ghostscript", "pdftk", "openssl", "chrome", "tesseract"
    |
    */
    'driver' => env('PDFLIB_DRIVER', 'ghostscript'),

    /*
    |--------------------------------------------------------------------------
    | Binary Paths
    |--------------------------------------------------------------------------
    |
    | Optional paths to binaries if not in system PATH.
    |
    */
    'binaries' => [
        'ghostscript' => env('PDFLIB_GS_BIN', 'gs'),
        'pdftk' => env('PDFLIB_PDFTK_BIN', 'pdftk'),
        'chrome' => env('PDFLIB_CHROME_BIN', 'google-chrome'),
        'tesseract' => env('PDFLIB_TESSERACT_BIN', 'tesseract'),
    ],
];
