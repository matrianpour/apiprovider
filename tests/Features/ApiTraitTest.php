<?php 
namespace Mtrn\ApiService\Tests\Features;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\ExampleModel;

class ApiTraitTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function request_from_api()
    {
        //arrange
        $apiTrait = new ExampleModel();

        //act
        $response = $apiTrait->request($map=false);

        //assert
        $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
        $this->assertSame(true,$response->successful());
    }


    /**
     * @test
     */
    public function api_name_has_been_set()
    {
        //arrange
        $exampleModel = new ExampleModel();

        //act
        $apiName = $exampleModel->apiName;

        //asseert 
        $this->assertSame('examples',$apiName);
    }
}