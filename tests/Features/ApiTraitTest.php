<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
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
     * @return void
     */
    public function api_name_has_been_set()
    {
        //arrange
        $exampleModel = new ExampleModel();

        //act
        $apiName = $exampleModel->apiName;

        //asseert 
        $this->assertSame('example',$apiName);
    }

    /**
     * @test
     * @return void
     */
    public function extract_data_from_json_api_response()
    {
        //arrange
        $configs = [
            'url' => 'https://reqres.in/api/users/3',
            'response_type' => 'json',
            'data_access_key' => 'data.user'
        ];

        config(['apiservice.example'=> $configs]);

        $exampleModel = new ExampleModel();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response([
                'data' => [
                    'user' => ['first_name' => 'john'],
                ]
            ], 200)
        ]);

        //act
        $exampleModel->request($map=false);
        $extractedData = $exampleModel->getData();

        //assert
        $this->assertSame(['first_name' => 'john'], $extractedData);
    }

    /**
     * 
     * @test
     * @return void
     */
    public function consider_default_api_response_type_as_json()
    {
        //arrange
        $configs = [
            'url' => 'https://reqres.in/api/users/3',
            // 'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.example'=> $configs]);

        $exampleModel = new ExampleModel();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response(['first_name' => 'john'], 200)
        ]);


        //act
        $exampleModel->request($map=false);
        $extractedData = $exampleModel->getData();

        //assert
        $this->assertIsArray($extractedData);
        $this->assertSame(['first_name' => 'john'], $extractedData);
    }

    /**
     * 
     * @test
     * @return void
     */
    public function consider_default_api_data_access_key_as_data()
    {
        //arrange
        $configs = [
            'url' => 'https://reqres.in/api/users/3',
            'response_type' => 'json',
            // 'data_access_key' => 'data'
        ];

        config(['apiservice.example'=> $configs]);

        $exampleModel = new ExampleModel();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response([
                'data' => ['first_name' => 'john']
            ], 200)
        ]);


        //act
        $exampleModel->request($map=false);
        $extractedData = $exampleModel->getData();

        //assert
        $this->assertIsArray($extractedData);
        $this->assertSame(['first_name' => 'john'], $extractedData);
    }

    

}