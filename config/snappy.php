<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | Uses wkhtmltopdf for PDF generation. Set WKHTML_PDF_BINARY in .env to
    | the path of wkhtmltopdf (e.g. "C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe" on Windows).
    |
    */

    'pdf' => [
        'enabled' => true,
        // On Windows set WKHTML_PDF_BINARY in .env e.g. "C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"
        'binary'  => env('WKHTML_PDF_BINARY', PHP_OS_FAMILY === 'Windows' ? 'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe' : '/usr/bin/wkhtmltopdf'),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            'no-stop-slow-scripts'     => true,
        ],
        'env' => [],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTML_IMG_BINARY', base_path('vendor/bin/wkhtmltoimage')),
        'timeout' => false,
        'options' => [],
        'env' => [],
    ],

];
