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
    | This option controls the default values.
    | You may change them as you want.
    |
    */
    'defaults' => [
        'response_type' => 'json', //currently supported types are [json]
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
            | The type of response.
            | currently suported types are: json
            |
            | default is json
            */
            'response_type' => 'json',

            /*
            |--------------------------------------------------------------------------
            | data_access_keys
            |--------------------------------------------------------------------------
            |
            | This value is used to access to the client-related-data.
            | Use dot notation format for multidimensional array.
            | e.g. if the user-related-data is in response[data][user]
            | then data_access_keys[user] would be data.user
            |
            | default would be the name of the client
            */
            'data_access_keys' => [
                'client' => 'data',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | path_to_decorators
    |--------------------------------------------------------------------------
    |
    | This value is used to access to decorators.
    |
    */
    'path_to_decorators'   => 'Mtrn\\ApiService\\Services\\ApiService\\Decorators\\',
    'path_to_apiproviders' => 'Mtrn\ApiService\\Services\\ApiService\\ApiProviders\\',

];
