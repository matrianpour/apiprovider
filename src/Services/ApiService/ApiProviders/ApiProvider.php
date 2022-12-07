<?php 
namespace Mtrn\ApiService\Services\ApiService\ApiProviders;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class ApiProvider
{
    private array $configs;
    protected string $url;

    public function __construct()
    {
        $this->setConfig();
    }
    
    /**
     * @return Response
     */
    abstract public function requestFromApi(): Response;


    /**
     * @return Response
     */
    public function requestFromProvider(): Response
    {
        $response = $this->requestFromApi();
        $this->setData($response);

        return $response;
    }

    /**
    * @return void
    */
    private function setConfig(): void
    {
        $apiName = $this->getApiName();

        $configs = config('apiservice.apis.'.$apiName);
        $defaults = config('apiservice.defaults');
        $configs['response_type'] = $configs['response_type'] ?? ($defaults['response_type'] ?? 'json');
        $configs['data_access_key'] = $configs['data_access_key'] ?? ($defaults['data_access_key'] ?? '');
        $configs['api_name'] = $apiName;
        $this->configs = $configs;
        $this->url = $configs['url'];
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
     * @return string
     */
    private function getApiName(): string
    {
        return Str::snake(Str::remove('ApiProvider', class_basename($this)));
    }


    /**
     * @param Response $data
     * @return void
     */
    private function setData(Response $data): void
    {
        $responseType = $this->getConfig('response_type') ?? $this->getConfig('defaults.response_type');

        switch ($responseType) {
            case 'json':
                $dataBody = $this->extraxtDataFromJson($data->body());
                $extractedData = $this->extraxtDataFromJson($data->body());
                break;
            
            // case 'xml':
            //     $dataBody = $this->extraxtDataFromXml($data->body());
            //     break;

        }

        $this->dataBody = $dataBody;
    }

    /**
     * return the data of response
     * @return array
     */
    public function getData(): array
    {
        return $this->dataBody;
    }

        
    /**
     * @param string $data
     * @return array $dataArray
     */
    private function extraxtDataFromJson($data): array
    {
        if(!Str::of($data)->isJson())
            throw new \Exception(
                __METHOD__.
                ': Resoonse data type is expected to be json '
               . gettype($data).' is given.'
            );

        $dataArray = json_decode($data, true);
        
        //data_access_key is client-related
        //googleApiProvider  has a key-access for user and a key for news
        //now if this provider is called from user, use key to user and the same for news
        //i think its better to move this part to decorator layer,
        //where we have access to provider and client
        $dataAccessKey = $this->getConfig('data_access_key') ?? $this->getConfig('defaults.data_access_key');
        if($dataAccessKey !== '')
            $dataArray = Arr::get($dataArray, $dataAccessKey);

        return $dataArray;
    }
 

}