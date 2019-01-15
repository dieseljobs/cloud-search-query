<?php return [
    /*
    |--------------------------------------------------------------------------
    | Service Endpoint
    |--------------------------------------------------------------------------
    |
    | This is the endpoint for cloudsearch
    |
    */

    'endpoint' => 'http://<name>.<region>.cloudsearch.amazonaws.com',

    'key'    => env('AWS_KEY', null),
    
    'secret' => env('AWS_SECRET', null),
];
