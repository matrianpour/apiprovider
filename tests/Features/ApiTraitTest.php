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
        $response = $apiTrait->requestFromApi($apiName = 'example',$map=false);

        //assert
        $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
        $this->assertSame(true,$response->successful());
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
        $exampleModel->requestFromApi($amiName='example', $map=false);
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
        $exampleModel->requestFromApi($amiName='example', $map=false);
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
        $exampleModel->requestFromApi($amiName='example', $map=false);
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
        $mappedDataObject = $exampleModel->requestFromApi($amiName='example', $map=true);

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\ExampleModel', $mappedDataObject);
        $this->assertSame(['name' => 'john doe'], $mappedDataObject->getMappedArray());
        
    }

    /**
     * @test
     * @return void
     */
    public function set_provider_for_trait()
    {
        //arrange
        $configs = [
            'url' => 'https://reqres.in/api/users/3',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.example'=> $configs]);

        $exampleModel = new ExampleModel();
        $exampleModel->requestFromApi($apiName='example', $map=false);

        

        //act
        $apiProviderOfExampleModel = $exampleModel->getProvider();
        $providerName = $apiProviderOfExampleModel->getConfig('api_name');
        $providerConfigs = $apiProviderOfExampleModel->getConfig();

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\ApiProviders\ApiProvider', $apiProviderOfExampleModel);
        $this->assertSame('example', $providerName);
        foreach ($configs as $key => $value) {
            $this->assertArrayHasKey($key, $providerConfigs);
            $this->assertSame($value, $providerConfigs[$key]);
        }
    }


    /**
     * @test
     */
    public function support_diffrent_api_providers()
    {
        //arrange
        $configsForExampleApi = [
            'url' => 'https://example-api/users/',
            'response_type' => 'json',
            'data_access_key' => 'data'
        ];

        $configsForOtherApi = [
            'url' => 'https://other-api/users/',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.example'=> $configsForExampleApi]);
        config(['apiservice.apis.other'=> $configsForOtherApi]);

        $apiClientObject = new ExampleModel();

        $exampleExpectedData = ['first_name' => 'john'];
        $otherExpectedData = ['surname' => 'sergey'];
        
        Http::fake([
            // stub a json response for example api
            $configsForExampleApi['url'] => Http::response([
                'data' => $exampleExpectedData
            ], 200),

            // stub a json response for other api
            $configsForOtherApi['url'] => Http::response($otherExpectedData, 200),

        ]);

        //act
        //parameter $apiName  passed to requestFromApi is newly added parametr which is used to set api rovider.
        $exampleApiResponse = $apiClientObject->requestFromApi($apiName='example', $map=false);
        $exampleApiData = $apiClientObject->getData();
        $otherApiResponse = $apiClientObject->requestFromApi($apiName='other', $map=false);
        $otherApiData = $apiClientObject->getData();
        

        //assert
        $this->assertTrue($exampleApiResponse->successful());
        $this->assertSame($exampleExpectedData, $exampleApiData);
        $this->assertTrue($otherApiResponse->successful());
        $this->assertSame($otherExpectedData, $otherApiData);

    }

}