<?php 
namespace Mtrn\ApiService\Services\ApiService\Decorators;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;
// use Mtrn\ApiService\Traits\IsApiClient AS Client;
use Mtrn\ApiService\Models\Client;

abstract class Decorator
{
    protected Client $client;
    public ApiProvider $provider;
    public array $configs;

    /**
     * @param Client $client
     * @param ApiProvider $provider
     */
    public function __construct($client, $provider)
    {
        $this->client = $client;
        $this->provider = $provider;
        $this->setConfig() ;
    }

    /**
     * @param array $data
     * @return Client
     */
    abstract public function mapApiData(array $data):Client;

    /**
    * @return void
    */
    private function setConfig(): void
    {
        $provider = $this->getProvider();
        $configs['api_name'] = $provider->getConfig('api_name');
        $client = $this->getClient();
        $configs['client_name'] = $client->getClientName();

        $data_access_key = $provider->getConfig('data_access_keys.'.$configs['client_name']) ?? '';
        
        $configs['data_access_key'] = $data_access_key;
        
        $this->configs = $configs;
    }

    /**
     * @return mixed
     */
    public function getConfig($key=null): mixed
    {
        if( $key === null )
            return $this->configs;
        
        return Arr::get($this->configs, $key);
    }


    /**
     * @return Response
     */
    public function requestFromProvider(): Response
    {
        return $this->getProvider()->requestFromProvider();
    }

    /**
     * @return Client
     */
    public function getMappedApiData(): Client
    {
        $clientRelatedData = $this->getClientRelatedDataFromDataApiBody();
        return $this->mapApiData($clientRelatedData);
    }

    /**
     * @return ApiProvider
     */
    public function getProvider(): ApiProvider
    {
        return $this->provider;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getApiBody(): array
    {
        return $this->getProvider()->getData();
    }

    /**
     * @return array 
     */
    public function getClientRelatedDataFromDataApiBody(): array
    {
        $dataArray = $this->getApiBody();
        $dataAccessKey = $this->getConfig('data_access_key');
        
        $clientRelatedData = ($dataAccessKey != '') ? Arr::get($dataArray, $dataAccessKey) : $dataArray;
        
        if($clientRelatedData===null)
            throw new \Exception(
                __METHOD__.'(): wrong data_access_key is resolved for '.
                $this->getConfig('client_name').
                ' in apiservice.apis.'. $this->getConfig('api_name') .'.data_access_keys'
            );
        
        return $clientRelatedData;
    }
    
}