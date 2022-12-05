<?php

/**
 * Here you can configure APIs of which the application use.
 */

return [


    /*
    |--------------------------------------------------------------------------
    | API example
    |--------------------------------------------------------------------------
    |
    | This value is used to configure ApiGetter of ExampleModel where:
    |
    | example is the name of the api
    |
    */

    'example' => [
        'url' => 'https://reqres.in/api/users/3',
        /*
        |--------------------------------------------------------------------------
        | API example
        |--------------------------------------------------------------------------
        |
        | This value is used to access to the data you want extract from api.
        | for example if the data is in response[data][user] 
        | the data_access_key would be data.user
        |
        | default is data
        */
        'data_access_key' => 'data'
    ]
];