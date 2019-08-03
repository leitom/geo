<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    |
    | Her you can decide with unit you will be using.
    | Valid options are: m = meters, km = kilometers, mi = miles, ft = feet
    */

    'unit' => 'km',

    /*
    |--------------------------------------------------------------------------
    | Chunk
    |--------------------------------------------------------------------------
    |
    | Here you can set the chunk size that should be using when importing
    | and removing locations from the redis index.
    */

    'chunk' => 500,

];
