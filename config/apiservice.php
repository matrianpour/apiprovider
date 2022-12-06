<?php

/**
 * Here you can configure APIs of which the application use.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | APIService Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default values of the trait.
    | You may change them as you want.
    |
    */
    'defaults' => [
        'response_type' => 'json', //currently supported types are [json]
        'data_access_key' => 'data',
    ],


    /*
    |--------------------------------------------------------------------------
    | APIs
    |--------------------------------------------------------------------------
    |
    | Array of all apis that use HasApiTrait as their intractor.
    | Keys of this array are api-names provided by any object that get use of the trait.
    |
    */
    'apis' => [
        'google' => [
            'url' => 'https://reqres.in/api/users/3',

            /*
            |--------------------------------------------------------------------------
            | response_type
            |--------------------------------------------------------------------------
            |
            | This value is used to access to the data you want extract from api.
            | for example if the data is in response[data][user] 
            | the data_access_key would be data.user
            |
            | default is json
            */
            'response_type' => 'json',

            /*
            |--------------------------------------------------------------------------
            | data_access_key
            |--------------------------------------------------------------------------
            |
            | This value is used to access to the data you want to extract from api.
            | Use dot notation format for multidimensional array.
            | e.g. if the data is in response[data][user] 
            | the data_access_key would be data.user
            |
            | default is data
            */
            'data_access_key' => 'data'
        ]
    ],
];