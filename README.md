# PDFlib 1.2.2
PDFlib is a project which enables you to interact with PDFs, Current Release provide you methods to convert PDF to Images as well as Images to PDF, future releases will included more functions to interact with PDF files

This project is an initiative of [Treinetic (Pvt) Ltd](http://www.treinetic.com), Sri Lanka. 
contact us via info@treinetic.com and get your project done by the experts.

![Issues](https://img.shields.io/github/issues/imalhasaranga/PDFBox.svg)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
![Forks](https://img.shields.io/github/forks/imalhasaranga/PDFBox.svg)


## Requirements

You should have [Ghostscript](http://www.ghostscript.com/) >= 9.16 installed and configured.

## Install

The package can be installed via composer:
``` bash
$ composer require imal-h/pdf-box
```

## Usage

Converting a PDF to set of images.

```php

$pdflib = new ImalH\PDFLib\PDFLib();
$pdflib->setPdfPath($pdf_file_path);
$pdflib->setOutputPath($folder_path_for_images);
$pdflib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG);
$pdflib->setDPI(300);
$pdflib->setPageRange(1,$pdflib->getNumberOfPages());
$pdflib->setFilePrefix('page-'); // Optional
$pdflib->convert();

```
Alternatively using chaining:

```php

$files = (new ImalH\PDFLib\PDFLib())
    ->setPdfPath($pdf_file_path)
    ->setOutputPath($folder_path_for_images)
    ->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG)
    ->setDPI(300)
    ->setPageRange(1,$pdflib->getNumberOfPages())
    ->setFilePrefix('page-') // Optional
    ->convert();

```

Making a PDF from set of images

```php

$pdflib = new ImalH\PDFLib\PDFLib();
$imagePaths = ["images-1.jpg","images-2.jpg"];
$pdflib->makePDF($destination_pdf_file_path,$imagePaths);

```

If in anycase code throws '**** Unable to open the initial device, quitting.' this type of error that means program can't create temporary files because of a permission problem, this error only comes in the Linux or Mac Oparating systems so Please check the apache log and provide necessay permissions

## Other useful methods
You can get the total number of pages in the pdf:
```php

$pdfBox->getNumberOfPages(); //returns the number of pages in the pdf
$pdfBox->setPageRange(1,2); // allows you to convert only few pages in the PDF Document
$pdfBox->setImageQuality(95); // allows you to tell the quality you expect in the output Jpg file (only jpg)
$pdfBox->setDPI(300); //setting the DPI (Dots per inch) of output files
$pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG,$dDownScaleFactor=null);   //this will set the output image format, default it is jpg, but I recommend using pdf to png because it seems it is faster
/*
$dDownScaleFactor=integer
This causes the internal rendering to be scaled down by the given (integer <= 8) factor before being output. For example, the following will produce a 200dpi output png from a 600dpi internal rendering:
    
    gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o tiger.png\examples/tiger.png

Read More : http://ghostscript.com/doc/current/Devices.htm
*/
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Tests

- Make sure to run all the tests and see all of them are pssing before sumbitting a pull requests

### How to run Tests ? 
    composer install
    vendor/bin/phpunit


## Credits

- [Imal Hasaranga Perera](https://github.com/imalhasaranga)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
