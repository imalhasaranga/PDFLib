# PDFlib
PDFlib is a project which enables you to interact with PDFs, Current Release provide you methods to convert PDF to Images as well as Images to PDF, future releases will included more functions to interact with PDF files

This project was done by a sofware company called Treinetic (Pvt) Ltd, Sri Lanka. You will find more info about us and what we do [on our website](http://www.treinetic.com).

![Issues](https://img.shields.io/github/issues/imalhasaranga/PDFBox.svg)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
![Forks](https://img.shields.io/github/forks/imalhasaranga/PDFBox.svg)


## Requirements

You should have [Ghostscript](http://www.ghostscript.com/) installed and configured.

## Install

The package can be installed via composer:
``` bash
$ composer require imal-h/pdf-box
```

## Usage

Converting a PDF to set of Images.

```php

$pdflib = new ImalH\PDFLib\PDFLib();
$pdflib->setPdfPath($pdf_file_path);
$pdflib->setOutputPath($folder_path_for_images);
$pdflib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG);
$pdflib->setDPI(300);
$pdflib->setPageRange(1,$pdflib->getNumberOfPages());
$pdflib->convert();

```

Making a PDF from set of Images

```php

$pdflib = new ImalH\PDFLib\PDFLib();
$imagePaths = ["images-1.jpg","images-2.jpg"];
$pdflib->makePDF($destination_pdf_file_path,$imagePaths);

```

If in anycase code throws '**** Unable to open the initial device, quitting.' this type of error that means program can't create temporary files because of a permission problem, this error only comes in the Linux or Mac Oparating systems so Please check the apache log and provide necessay permissions

## Other Usefull Methods
You can get the total number of pages in the pdf:
```php

$pdfBox->getNumberOfPages(); //returns the number of pages in the pdf
$pdfBox->setPageRange(1,2); // allows you to convert only few pages in the PDF Document
$pdfBox->setImageQuality(95); // allows you to tell the quality you expect in the output Jpg file (only jpg)
$pdfBox->setDPI(300); //setting the DPI (Dots per inch) of output files
$pdfLib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_PNG,);   //this will set the output image format, default it is jpg, but I recommend using pdf to png because it seems it is faster

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Imal Hasaranga Perera](https://github.com/imalhasaranga)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
