<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\Models\Client;
use Mtrn\ApiService\Traits\IsApiClient;

class DecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function execute_strategy_to_choose_decorator():void
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

        $client->requestFromApi('google', false);
        $decorator = $client->getDecorator();

        $this->assertInstanceOf(config('apiservice.path_to_decorators').'Decorator', $decorator);
        $this->assertSame('GoogleClientDecorator', class_basename($decorator));
    }

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

}