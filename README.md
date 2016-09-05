# PDFBox
PDFBox is Currently the fastest Php wrapper enables you to interact with PDFs, Current Release provide you methods to convert PDF to Images as well as Images to PDF, future releases will included more functions to interact with PDF files

This project was done by a sofware company called Treinetic (Pvt) Ltd, Sri Lanka. You will find more info about us and what we do [on our website](http://www.treinetic.com).

## Requirements

You should have [Ghostscript](http://www.ghostscript.com/) installed and configured.

## Install

The package can be installed via composer:
``` bash
$ composer require spatie/pdf-to-image
```

## Usage

Converting a PDF to set of Images.

```php

$pdfBox = new ImalH\PDFBox\PDFBox();
$pdfBox->setPdfPath($pdf_file_path);
$pdfBox->setOutputPath($folder_path_for_images);
$pdfBox->setImageQuality(95);
$pdfBox->setDPI(300);
$pdfBox->setPageRange(1,$pdfBox->getNumberOfPages());
$pdfBox->convert();

```

Making a PDF from set of Images

```php

$pdfBox = new ImalH\PDFBox\PDFBox();
$imagePaths = ["images-1.jpg","images-2.jpg"];
$pdfBox->makePDF($destination_pdf_file_path,$imagePaths);

```

If in anycase code throws '**** Unable to open the initial device, quitting.' this type of error that means program can't create temporary files because of a permission problem, this error only comes in the Linux or Mac Oparating systems so Please check the apache log and provide necessay permissions

## Other Usefull Methods
You can get the total number of pages in the pdf:
```php

$pdfBox->getNumberOfPages(); //returns the number of pages in the pdf
$pdfBox->setPageRange(1,2); // allows you to convert only few pages in the PDF Document
$pdfBox->setImageQuality(95); // allows you to tell the quality you expect in the output Jpg file
$pdfBox->setDPI(300); //setting the DPI (Dots per inch) of output files

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
