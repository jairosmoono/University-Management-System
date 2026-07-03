<?php

return [
    'name' => env('APP_NAME', 'University Management System'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'Africa/Lusaka',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
    ])->toArray(),
    'aliases' => Illuminate\Support\Facades\Facade::defaultAliases()->merge([
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Image' => Intervention\Image\Laravel\Facades\Image::class,
        'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
    ])->toArray(),
    'university' => [
        'name' => env('UNIVERSITY_NAME', 'Zambia University'),
        'short_name' => env('UNIVERSITY_SHORT_NAME', 'ZU'),
        'address' => env('UNIVERSITY_ADDRESS', 'Lusaka, Zambia'),
        'phone' => env('UNIVERSITY_PHONE', '+260 211 000000'),
        'email' => env('UNIVERSITY_EMAIL', 'info@university.com'),
        'website' => env('UNIVERSITY_WEBSITE', 'https://university.com'),
    ],
];
