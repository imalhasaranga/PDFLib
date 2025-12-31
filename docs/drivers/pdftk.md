# Pdftk Driver

The **PdftkDriver** is specialized for handling **Interactive Forms (AcroForms)**. It essentially wraps the `pdftk` binary to reliably fill data.

## Requirements
*   **pdftk** binary installed on system.

## Usage

```php
use ImalH\PDFLib\PDF;

$pdf = PDF::init()->driver(PDF::DRIVER_PDFTK);
```

## 1. Inspect Fields
Find out what field names exist in your template.

```php
$fields = $pdf->getFormFields('template.pdf');
print_r($fields); 
// Output: ['full_name', 'email', 'date', ...]
```

## 2. Fill Form
Map your data to the field names.

```php
$data = [
    'full_name' => 'John Doe',
    'email' => 'john@example.com',
    'date' => date('Y-m-d')
];

$pdf->from('template.pdf')
    ->fillForm($data, 'filled_form.pdf');
```

**Note**: The driver automatically "flattens" the form by default, meaning the fields become uneditable in the output PDF.
