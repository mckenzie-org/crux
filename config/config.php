<?php

return [
    /*
     * Path for storing model definition files.
     */
    'definitions_path'=>'resources/definitions',

    /*
     *   Prefix and middleware for dashboard routes.
     */
    'web'=>[
        'prefix'=>'dashboard',
        'middleware'=>['web','auth']
    ],

    /*
     *  Prefix and middleware for api routes.
     */
    'api'=>[
        'prefix'=>'api',
        'middleware'=>['web']
    ],

    /*
     * Routes to add for web and api.
     * For e.g.
     * 'users'=>[
     *      'controller'=>UserController::class
     * ]
     */

    'routes'=>[
        'web'=>[],
        'api'=>[]
    ]
];