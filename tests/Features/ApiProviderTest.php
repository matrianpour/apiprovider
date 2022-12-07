<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mtrn\ApiService\Services\ApiService\ApiProviders\GoogleApiProvider;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\Models\Client;

class ApiProviderTest extends TestCase
{
    /**
     * @test
     */
    public function request_from_api_using_an_api_provider()
    {
        //arrange
        $googleProvider = new GoogleApiProvider();
        
        //act   
        $response = $googleProvider->requestFromProvider();

        //assert
        $this->assertSame('google', $googleProvider->getConfig('api_name'));
        $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
        $this->assertTrue($response->successful());
    }

        /**
     * @test
     * @return void
     */
    public function extract_data_from_json_api_response()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            'data_access_key' => 'data.user'
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response([
                'data' => [
                    'user' => ['first_name' => 'john'],
                ]
            ], 200)
        ]);

        //act
        $client->requestFromApi($amiName='google', $map=false);
        // $extractedData = $client->getApiBody();
        $extractedData = $client->getApiBody('data.user');

        //assert
        $this->assertSame(['first_name' => 'john'], $extractedData);
    }

    
}