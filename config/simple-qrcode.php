<?php

return [
    'default' => env('SIMPLE_QRCODE_GENERATOR', 'svg'),
    
    'generators' => [
        'svg' => [
            'format'        => 'svg',
            'charactersLimit' => 1000,
        ]
    ]
];
