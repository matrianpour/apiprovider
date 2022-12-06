<?php 
namespace Mtrn\ApiService\Tests\Features;

use Mtrn\ApiService\ApiProviders\ExampleApiProvider;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\ApiProviders\ApiProvider;
use Mtrn\ApiService\ApiProviders\OtherApiProvider;
use Mtrn\ApiService\ExampleModel;

class ApiProviderTest extends TestCase
{
    /**
     * @test
     */
    public function request_from_api_using_an_api_provider()
    {
        //arrange
        $exampleProvider = new ExampleApiProvider();
        
        //act
        $response = $exampleProvider->requestFromProvider();

        //assert
        $this->assertSame('example', $exampleProvider->configs['api_name']);
        $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
        $this->assertTrue($response->successful());
    }

    
}