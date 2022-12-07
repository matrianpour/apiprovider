<?php 
namespace Mtrn\ApiService\Tests\Features;

use Illuminate\Support\Facades\Http;
use Mtrn\ApiService\Tests\TestCase;
use Mtrn\ApiService\Models\Client;

class ApiTraitTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function request_from_api()
    {
        //arrange
        $client = new Client(); //Client that use IsClientApi

        //act
        $response = $client->requestFromApi($apiName = 'google',$map=false);

        //assert
        $this->assertInstanceOf('Illuminate\Http\Client\Response', $response);
        $this->assertSame(true,$response->successful());
    }


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
     */
    public function support_diffrent_decoration_for_a_client()
    {
        //arrange
        $configsForGoogleApi = [
            'url' => 'https://google.com/api/users/',
            'response_type' => 'json',
            'data_access_keys' => ['client'=>'data']
        ];

        $configsForGithubApi = [
            'url' => 'https://github/api/users/',
            'response_type' => 'json',
            'data_access_keys' => ['client'=>'']
        ];

        config(['apiservice.apis.google'=> $configsForGoogleApi]);
        config(['apiservice.apis.github'=> $configsForGithubApi]);

        $client = new Client();
        
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
        $googleApiMappedData = $client->requestFromApi($apiName='google', $map=true); // get use of GoogleClientDecorator
        $googleMappedData = $googleApiMappedData->getMappedArray();
        $githubApiMappedData = $client->requestFromApi($apiName='github', $map=true); // get use of GithubClientDecorator
        $githubMappedData = $githubApiMappedData->getMappedArray();
        

        //assert
        $this->assertInstanceOf('Mtrn\ApiService\Models\Client', $googleApiMappedData);
        $this->assertSame(['name' => 'john doe'], $googleMappedData);
        $this->assertInstanceOf('Mtrn\ApiService\Models\Client', $githubApiMappedData);
        $this->assertSame(['name' => 'sergey lazarev'], $githubMappedData);

    }

}