<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\Client;

class ApiTraitTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function request_from_api()
    {
        //arrange
        $apiTrait = new Client(); //Client that use HasApiGetter

        //act
        $response = $apiTrait->requestFromApi($apiName = 'google',$map=false);

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
        $extractedData = $client->getApiBody();

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
            'url' => 'https://google.com/api/users/3',
            // 'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response(['first_name' => 'john'], 200)
        ]);


        //act
        $client->requestFromApi($amiName='google', $map=false);
        $extractedData = $client->getApiBody();

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
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            // 'data_access_key' => 'data'
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        Http::fake([
            $configs['url'] => Http::response([
                'data' => ['first_name' => 'john']
            ], 200)
        ]);


        //act
        $client->requestFromApi($amiName='google', $map=false);
        $extractedData = $client->getApiBody();

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
            'url' => 'https://google.com/api/users/3',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.google'=> $configs]);

        $client = new Client();

        // stub a json response for api
        $responseArray = [
            'first_name' => 'john',
            'last_name' => 'doe'
        ];
        Http::fake([$configs['url'] => Http::response($responseArray, 200)]);
        
        //act
        $mappedDataObject = $client->requestFromApi($amiName='google', $map=true);

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\Client', $mappedDataObject);
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
        $apiProviderOfClient = $client->getProvider();
        $providerName = $apiProviderOfClient->getConfig('api_name');
        $providerConfigs = $apiProviderOfClient->getConfig();

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\ApiProviders\ApiProvider', $apiProviderOfClient);
        $this->assertSame('google', $providerName);
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
        $configsForGoogleApi = [
            'url' => 'https://google.com/api/users/',
            'response_type' => 'json',
            'data_access_key' => 'data'
        ];

        $configsForGithubApi = [
            'url' => 'https://github/api/users/',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.google'=> $configsForGoogleApi]);
        config(['apiservice.apis.github'=> $configsForGithubApi]);

        $apiClientObject = new Client();

        $googleExpectedData = ['first_name' => 'john'];
        $githubExpectedData = ['surname' => 'sergey'];
        
        Http::fake([
            // stub a json response for google api
            $configsForGoogleApi['url'] => Http::response([
                'data' => $googleExpectedData
            ], 200),

            // stub a json response for other api
            $configsForGithubApi['url'] => Http::response($githubExpectedData, 200),

        ]);

        //act
        //parameter $apiName  passed to requestFromApi is newly added parametr which is used to set api rovider.
        $googleApiResponse = $apiClientObject->requestFromApi($apiName='google', $map=false);
        $googleApiData = $apiClientObject->getApiBody();
        $gitHubApiResponse = $apiClientObject->requestFromApi($apiName='github', $map=false);
        $githubApiData = $apiClientObject->getApiBody();
        

        //assert
        $this->assertTrue($googleApiResponse->successful());
        $this->assertSame($googleExpectedData, $googleApiData);
        $this->assertTrue($gitHubApiResponse->successful());
        $this->assertSame($githubExpectedData, $githubApiData);

    }

    /**
     * @test
     */
    public function support_diffrent_mappers_for_a_client()
    {
        //arrange
        $configsForGoogleApi = [
            'url' => 'https://google.com/api/users/',
            'response_type' => 'json',
            'data_access_key' => 'data'
        ];

        $configsForGithubApi = [
            'url' => 'https://github/api/users/',
            'response_type' => 'json',
            'data_access_key' => ''
        ];

        config(['apiservice.apis.google'=> $configsForGoogleApi]);
        config(['apiservice.apis.github'=> $configsForGithubApi]);

        $apiClientObject = new Client();
        
        Http::fake([
            // stub a json response for google api
            $configsForGoogleApi['url'] => Http::response([
                'data' => ['first_name' => 'john', 'last_name' => 'doe']
            ], 200),

            // stub a json response for other api
            $configsForGithubApi['url'] => Http::response([
                'forename' => 'sergey', 'surname' => 'lazarev'
            ], 200),
        ]);

        //act
        $googleApiMappedData = $apiClientObject->requestFromApi($apiName='google', $map=true);
        $googleMappedData = $googleApiMappedData->getMappedArray();
        $githubApiMappedData = $apiClientObject->requestFromApi($apiName='github', $map=true);
        $githubMappedData = $githubApiMappedData->getMappedArray();
        

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\Client', $googleApiMappedData);
        $this->assertSame(['name' => 'john doe'], $googleMappedData);
        $this->assertInstanceOf('Mtrn\ApiService\Client', $githubApiMappedData);
        $this->assertSame(['name' => 'sergey lazarev'], $githubMappedData);

    }

}