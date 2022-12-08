<?php

namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mtrn\ApiService\Models\Client;
use Mtrn\ApiService\Tests\TestCase;

class ApiProviderTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function consider_default_api_response_type_as_json()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/users/3',
            // 'response_type' => 'json',
            'data_access_keys' => ['client'=>''],
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response(['first_name' => 'john'], 200),
        ]);

        //act
        $client->requestFromApi($amiName = 'google', $map = false);
        $extractedData = $client->getApiBody();

        //assert
        $this->assertIsArray($extractedData);
        $this->assertSame(['first_name' => 'john'], $extractedData);
    }

    /**
     * @test
     *
     * @return void
     */
    public function extract_data_from_json_api_response()
    {
        //arrange
        $configs = [
            'url'           => 'https://google.com/api/users/3',
            'response_type' => 'json',
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        $dataBody = [
            'data' => [
                'user' => ['first_name' => 'john'],
            ],
        ];
        // stub a json response for api
        Http::fake([$configs['url'] => Http::response($dataBody, 200)]);

        //act
        $client->requestFromApi($amiName = 'google', $map = false);
        $extractedData = $client->getApiBody();

        //assert
        $this->assertSame($dataBody, $extractedData);
    }
}
