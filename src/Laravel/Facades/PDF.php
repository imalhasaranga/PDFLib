<?php

namespace ImalH\PDFLib\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class PDF extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pdflib';
    }
}
