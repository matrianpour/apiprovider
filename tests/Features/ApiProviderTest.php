<?php 
namespace Mtrn\ApiService\Tests\Features;

use Mtrn\ApiService\ApiProviders\GoogleApiProvider;
use Mtrn\ApiService\Tests\TestCase;

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

    
}