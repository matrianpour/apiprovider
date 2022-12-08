<?php

use Illuminate\Support\Facades\Route;
use Mtrn\ApiService\Models\Client;

/**
 * Some example routes :).
 */
Route::get('/get-api-response', function () {
    $client = new Client();
    $response = $client->requestFromApi('google', false);

    return $response;
});

Route::get('/get-mapped-data', function () {
    $client = new Client();
    $mappedData = $client->requestFromApi('google', true);
    dump($mappedData);

    return $mappedData;
});

Route::get('/get-mapped-array', function () {
    $client = new Client();
    $client->requestFromApi('google', true);
    $mappedArray = $client->getMappedArray();
    dump($mappedArray);

    return $mappedArray;
});
