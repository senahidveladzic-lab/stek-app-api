<?php

return [
    'available' => explode(',', env('APP_AVAILABLE_LOCALES', 'bs,en')),
    'default' => env('APP_LOCALE', 'bs'),
    'formats' => [
        'bs' => [
            'date' => 'd.m.Y',
            'date_short' => 'd.m.',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'KM',
            'currency_position' => 'after',
        ],
        'en' => [
            'date' => 'Y-m-d',
            'date_short' => 'M d',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'BAM',
            'currency_position' => 'before',
        ],
    ],
];
