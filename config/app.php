<?php

use Illuminate\Support\Facades\Facade;

return [
    'imageSize' => [
        'w_100' => ['width' => 100, 'height' => 0, 'suffix' => '--w_100'],
        'w_150' => ['width' => 150, 'height' => 0, 'suffix' => '--w_150'],
        'w_300' => ['width' => 300, 'height' => 0, 'suffix' => '--w_300'],
        'w_400' => ['width' => 400, 'height' => 0, 'suffix' => '--w_400'],
        'w_500' => ['width' => 500, 'height' => 0, 'suffix' => '--w_500'],
        'h_500' => ['width' => 0, 'height' => 500, 'suffix' => '--h_500'],
        'e_150x150' => ['width' => 150, 'height' => 150, 'suffix' => '--e_150x150'],
        'origin' => ['suffix' => '--origin']
    ]
];
