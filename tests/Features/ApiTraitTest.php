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
        $apiTrait = new ExampleModel(); //exampleModel that use HasApiGetter

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

        config(['apiservice.apis.example'=> $configs]);

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

        config(['apiservice.apis.example'=> $configs]);

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

        config(['apiservice.apis.example'=> $configs]);

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

        /**
     * @test
     */
    public function get_mapped_data_from_json_api_response()
    {
        //arange
        $configs = [
            'url' => 'https://reqres.in/api/users/3',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.example'=> $configs]);

        $exampleModel = new ExampleModel();

        // stub a json response for api
        $responseArray = [
            'first_name' => 'john',
            'last_name' => 'doe'
        ];
        Http::fake([$configs['url'] => Http::response($responseArray, 200)]);
        
        //act
        $mappedDataObject = $exampleModel->request($map=true);

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\ExampleModel', $mappedDataObject);
        $this->assertSame(['name' => 'john doe'], $mappedDataObject->getMappedArray());
        
    }

    // /**
    //  * @test
    //  */
    // public function support_more_than_one_api_provider_for_single_mapper()
    // {
    //     //arrange
    //     $exampleModel = new ExampleModel(); //exampleModel that use HasApiGetter

    //     //act
    //     // $response = $exampleModel->request('google', $map=false);
    //     $response = $exampleModel->requestFromApi('google', $map=false);

    //     //assert
    //     $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
    //     $this->assertSame(true,$response->successful());
    // }

}