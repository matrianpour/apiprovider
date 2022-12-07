<?php 
namespace Mtrn\ApiService\Services\ApiService\Decorators;

use Illuminate\Http\Client\Response;
use Mtrn\ApiService\Services\ApiService\ApiProviders\ApiProvider;
// use Mtrn\ApiService\Traits\IsApiClient AS Client;
use Mtrn\ApiService\Models\Client;

abstract class Decorator
{
    protected $client;
    public $provider;

    /**
     * @param Client $client
     * @param ApiProvider $provider
     */
    public function __construct($client, $provider)
    {
        $this->client = $client;
        $this->provider = $provider;
    }

    /**
     * @param array $data
     * @return Client
     */
    abstract public function mapApiData(array $data):Client;

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
        $data = $this->getApiBody();
        return $this->mapApiData($data);
    }

    /**
     * @return ApiProvider
     */
    public function getProvider(): ApiProvider
    {
        return $this->provider;
    }

    /**
     * @return array
     */
    public function getApiBody(): array
    {
        return $this->getProvider()->getData();
    }
    
}