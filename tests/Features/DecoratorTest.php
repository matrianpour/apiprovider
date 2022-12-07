<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\Models\Client;

class DecoratorTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function has_provider()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();
        
        Http::fake([
            // stub a json response for google api
            $configs['url'] => Http::response(['first_name' => 'john'], 200),

        ]);

        
        //act
        $client->requestFromApi($apiName='google', $map=false);

        $decorator = $client->getDecorator();
        $providerName = $decorator->getProvider()->getConfig('api_name');
        $providerConfigs = $decorator->getProvider()->getConfig();

        //assert
        $this->assertInstanceOf(config('apiservice.path_to_apiproviders').'ApiProvider', $decorator->provider);
        $this->assertSame('google', $providerName);
        foreach ($configs as $key => $value) {
            $this->assertArrayHasKey($key, $providerConfigs);
            $this->assertSame($value, $providerConfigs[$key]);
        }
    }


    /**
     * @test
     * @return void
     */
    public function extract_client_related_data_from_data_body()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            'data_access_keys' => [
                'client' => 'data.user'
            ]
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        $dataBody = [
            'data' => [
                'user' => ['first_name' => 'john'],
            ]
        ];
        // stub a json response for api
        Http::fake([$configs['url'] => Http::response($dataBody, 200)]);

        //act
        $client->requestFromApi($amiName='google', $map=false);
        $getClientRelatedData = $client->getDecorator()->getClientRelatedDataFromDataApiBody();

        //assert
        $this->assertSame(['first_name' => 'john'], $getClientRelatedData);
    }

    /**
     * 
     * @test
     * @return void
     */
    public function consider_whole_responsebody_is_related_to_client_if_no_data_access_key_is_configd_for_client()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            // 'data_access_keys' => ['client'=>'']
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response(['first_name' => 'john'], 200)
        ]);


        //act
        $client->requestFromApi($amiName='google', $map=false);
        $getClientRelatedData = $client->getDecorator()->getClientRelatedDataFromDataApiBody();

        //assert
        $this->assertSame(['first_name' => 'john'], $getClientRelatedData);
    }


    /**
     * @test
     * @return void
     */
    public function map_client_related_data()
    {
        //arrange
        $configs = [
            'url' => 'https://google.com/api/post/3',
            'response_type' => 'json',
            'data_access_keys' => [
                'client' => 'data.user'
            ]
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        $dataBody = [
            'data' => [
                'user' => ['first_name' => 'john', 'last_name' => 'doe'],
                'post' => ['title' => 'lablabla'],
            ]
        ];
        // stub a json response for api
        Http::fake([$configs['url'] => Http::response($dataBody, 200)]);

        //act
        $client->requestFromApi($amiName='google', $map=true);

        //assert
        $this->assertSame(['name' => 'john doe'], $client->getMappedArray());
    }
}