<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection used to store the A/B testing information.
    |
    */

    'connection' => 'mysql',

    /*
    |--------------------------------------------------------------------------
    | Experiments
    |--------------------------------------------------------------------------
    |
    | A list of experiment identifiers.
    |
    | Example: ['big-logo', 'small-buttons']
    |
    */

    'experiments' => [],

    /*
    |--------------------------------------------------------------------------
    | Goals
    |--------------------------------------------------------------------------
    |
    | A list of goals. This list can contain urls, route names or custom goals.
    |
    | Example: ['pricing/order', 'signup']
    |
    */

    'goals' => [],

);
